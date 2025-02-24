<?php
/**
 * Plugin.
 *
 * @package Flexible Printing
 */

use FPrintingVendor\Octolize\ShippingExtensions\ShippingExtensions;
use FPrintingVendor\Octolize\Tracker\OptInNotice\ShouldDisplayGetParameterValue;
use FPrintingVendor\Octolize\Tracker\OptInNotice\ShouldDisplayOrConditions;
use FPrintingVendor\Octolize\Tracker\TrackerInitializer;
use FPrintingVendor\WPDesk\PluginBuilder\Plugin\HookableParent;

/**
 * Class Flexible_Printing_Plugin
 */
class Flexible_Printing_Plugin extends \FPrintingVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin implements \FPrintingVendor\WPDesk\PluginBuilder\Plugin\HookableCollection {

	use HookableParent;

	/**
	 * Scripts version
	 *
	 * @var string
	 */
	public $scripts_version = '10';

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Template path.
	 *
	 * @var string
	 */
	public $template_path;

	/**
	 * Integrations.
	 *
	 * @var Flexible_Printing_Integrations
	 */
	private $integrations;

	/**
	 * .
	 *
	 * @var Flexible_Printing
	 */
	public $flexible_printing;

	/**
	 * .
	 *
	 * @var Flexible_Printing_Google_Print
	 */
	public $google_print;

	/**
	 * .
	 *
	 * @var Flexible_Printing_Print_Node
	 */
	public $print_node;

	/**
	 * WPDesk_Settings_1_4
	 *
	 * @var WPDesk_Settings_1_4
	 */
	public $settings;

	/**
	 * .
	 *
	 * @var array
	 */
	public $options;

	/**
	 * Default setting tabs
	 *
	 * @var string
	 */
	private $default_settings_tab;

	/**
	 * Plugin text domain.
	 *
	 * @var string
	 */
	private $plugin_text_domain;


	/**
	 * Flexible_Printing_Plugin constructor.
	 *
	 * @param \FPrintingVendor\WPDesk_Plugin_Info $plugin_info .
	 */
	public function __construct( \FPrintingVendor\WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
		parent::__construct( $this->plugin_info );
	}

	/**
	 * .
	 *
	 * @return string
	 */
	public function get_plugin_dir() {
		return $this->plugin_info->get_plugin_dir();
	}

	/**
	 * Init base variables for plugin
	 */
	public function init_base_variables() {
		$this->plugin_url           = $this->plugin_info->get_plugin_url();
		$this->plugin_path          = $this->plugin_info->get_plugin_dir();
		$this->template_path        = $this->plugin_info->get_text_domain();
		$this->plugin_text_domain   = $this->plugin_info->get_text_domain();
		$this->plugin_namespace     = $this->plugin_info->get_text_domain();
		$this->default_settings_tab = 'print_node';
	}

	/**
	 * Init plugin
	 */
	public function init() {
		$this->init_tracker();

		parent::init();

	}

	/**
	 * Init tracker.
	 *
	 * @return void
	 */
	private function init_tracker() {
		$should_display = new ShouldDisplayOrConditions();
		$should_display->add_should_diaplay_condition( new ShouldDisplayGetParameterValue( 'page', 'flexible-printing' ) );
		$should_display->add_should_diaplay_condition( new ShouldDisplayGetParameterValue( 'page', 'flexible-printing-settings' ) );
		$this->add_hookable( TrackerInitializer::create_from_plugin_info( $this->plugin_info, $should_display ) );
	}


	/**
	 * .
	 */
	public function hooks() {
		parent::hooks();

		add_action( 'init', [ $this, 'init_plugin' ], 0 );

		add_filter( 'flexible_printing', '__return_true' );

		$this->add_hookable( new Flexible_Printing_Notices() );

		$this->add_hookable( new ShippingExtensions( $this->plugin_info ) );

		$this->hooks_on_hookable_objects();
	}

	public function init_plugin() {
		$this->settings = new WPDesk_Settings_1_4( $this, $this->get_namespace(), $this->default_settings_tab );
		$this->options  = $this->settings->get_settings();

		$this->flexible_printing = new Flexible_Printing( $this );
		$this->google_print      = new Flexible_Printing_Google_Print( $this );
		$this->print_node        = new Flexible_Printing_Print_Node( $this );
		$this->settings->delete_option( 'authorized_redirect_url' );
		$this->integrations = new Flexible_Printing_Integrations( $this, $this->google_print );
		new Flexible_Printing_Settings_Hooks( $this, $this->flexible_printing, $this->google_print, $this->integrations );
		new Flexible_Printing_Log( $this );

	}

	/**
	 * Get integrations
	 *
	 * @return mixed
	 */
	public function get_integrations() {
		return $this->integrations;
	}

	/**
	 * Admin enqueue scripts
	 */
	public function admin_enqueue_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( ! defined( 'WC_VERSION' ) || ! in_array( $screen_id, wc_get_screen_ids(), true ) ) {
			wp_register_script( 'jquery-tiptip', trailingslashit( $this->get_plugin_assets_url() ) . 'js/jquery.tipTip' . $suffix . '.js', [ 'jquery' ], $this->scripts_version, true );
			wp_enqueue_style(
				'tipTip',
				trailingslashit( $this->get_plugin_assets_url() ) . 'css/tipTip' . $suffix . '.css',
				'',
				$this->scripts_version,
				'all'
			);
		}
		wp_enqueue_script( 'jquery-tiptip' );
		wp_enqueue_script(
			'flexible-printing',
			trailingslashit( $this->get_plugin_assets_url() ) . 'js/admin' . $suffix . '.js',
			[ 'jquery' ],
			$this->scripts_version,
			true
		);
		wp_localize_script(
			'flexible-printing',
			'flexible_printing',
			[
				'ajax_url'         => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
				'printing_message' => __( 'Printing...', 'flexible-printing' ),
			]
		);
		wp_enqueue_style(
			'flexible-printing',
			trailingslashit( $this->get_plugin_assets_url() ) . 'css/admin' . $suffix . '.css',
			'',
			$this->scripts_version,
			'all'
		);
	}

	/**
	 * Plugin actions links.
	 *
	 * @param array $links Links.
	 *
	 * @return array
	 */
	public function links_filter( $links ) {
		$is_pl        = 'pl_PL' === get_locale();
		$docs_link    = $is_pl ? 'https://octol.io/printing-docs-pl/' : 'https://octol.io/printing-docs/';
		$support_link = $is_pl ? 'https://octol.io/printing-support-pl/' : 'https://octol.io/printing-support/';
		$settings_url = admin_url( 'admin.php?page=flexible-printing-settings' );

		$plugin_links = [
			'<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'flexible-printing' ) . '</a>',
			'<a href="' . esc_url( $docs_link ) . '" target="_blank">' . __( 'Documentation', 'flexible-printing' ) . '</a>',
			'<a href="' . esc_url( $support_link ) . '" target="_blank">' . __( 'Support', 'flexible-printing' ) . '</a>',
		];

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Get settings.
	 *
	 * @return WPDesk_Settings_1_4
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get option.
	 *
	 * @param string $key .
	 * @param string $default .
	 *
	 * @return string
	 */
	public function get_option( $key, $default ) {
		return $this->settings->get_option( $key, $default );
	}

	/**
	 * Get plugin.
	 *
	 * @return Flexible_Printing_Plugin
	 */
	public function get_plugin() {
		return $this;
	}

}
