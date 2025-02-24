<?php

use DpdUKVendor\Octolize\Brand\Assets\AdminAssets;
use DpdUKVendor\Octolize\Brand\UpsellingBox\ConstantShouldShowStrategy;
use DpdUKVendor\Octolize\Onboarding\PluginUpgrade\PluginUpgradeMessage;
use DpdUKVendor\Octolize\Onboarding\PluginUpgrade\PluginUpgradeOnboardingFactory;
use DpdUKVendor\Octolize\Tracker\TrackerInitializer;

class WPDesk_WooCommerce_DPD_UK_Plugin extends \DpdUKVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin {

	use \DpdUKVendor\WPDesk\PluginBuilder\Plugin\HookableParent;

	private $script_version = '4';

	public $dpd_uk = null;

	public $flexible_printing_integration = false;

	/**
	 * @param \DpdUKVendor\WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( \DpdUKVendor\WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
		parent::__construct( $this->plugin_info );
		$this->settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping&section=dpd_uk' );
	}

	/**
	 * Logic for init hooks
	 *
	 * @return void
	 */
	public function init() {
		$this->init_upgrade_onboarding();
		parent::init();
	}


	public function hooks() {
		parent::hooks();

		add_filter( 'flexible_printing_integrations', array( $this, 'flexible_printing_integrations' ) );

		add_action('plugins_loaded', function() {
			if ( class_exists( 'WPDesk_Flexible_Shipping_Shipment' ) ) {
				WPDesk_Flexible_Shipping_Shipment_dpd_uk::set_plugin( $this );

				$this->dpd_uk = WPDesk_WooCommerce_DPD_UK::get_instance( $this );
				new WPDesk_WooCommerce_DPD_UK_FS_Hooks( $this );
			}

			( new WPDesk_DPD_UK_Tracker() )->hooks();
			TrackerInitializer::create_from_plugin_info_for_shipping_method( $this->plugin_info, WPDesk_WooCommerce_DPD_UK_Shipping_Method::SHIPPING_METHOD_ID )->hooks();
		});

		$should_show_strategy = new \DpdUKVendor\Octolize\Brand\UpsellingBox\ShippingMethodAndConstantDisplayStrategy( WPDesk_WooCommerce_DPD_UK_Shipping_Method::SHIPPING_METHOD_ID, 'FS_PICKUP_POINTS_PRO_VERSION' );
		$this->add_hookable( new AdminAssets( $this->get_plugin_assets_url() . '../vendor_prefixed/octolize/wp-octolize-brand-assets/assets/', 'dpd-uk', $should_show_strategy ) );
		$this->add_hookable( new DpdUKVendor\Octolize\Brand\UpsellingBox\SettingsSidebar(
			'woocommerce_settings_tabs_shipping',
			$should_show_strategy,
			__( 'Get Pickup Points PRO WooCommerce', 'woocommerce-dpd-uk' ),
			[
				__( 'Selecting the DPD UK Pickup Points from the list or map', 'woocommerce-dpd-uk' ),
				__( 'Nearest DPD UK Pickup Points suggestions', 'woocommerce-dpd-uk' ),
				__( 'Saving the customer\'s choice to the order details', 'woocommerce-dpd-uk' ),
				__( 'DPD UK Ship to Shop service integration', 'woocommerce-dpd-uk' ),
				__( 'Pickup points display management', 'woocommerce-dpd-uk' ),
				__( 'DPD, DHL, UPS pickup points supported', 'woocommerce-dpd-uk' ),
				__( 'Popular themes & plugins compatible', 'woocommerce-dpd-uk' ),
			],
			'https://octol.io/dpd-uk-pp-up-box',
			__( 'Buy now', 'woocommerce-dpd-uk' ),
			1200
		));

		$this->hooks_on_hookable_objects();
	}

	/**
	 * Renders end returns selected template
	 *
	 * @param string $name Name of the template.
	 * @param string $path Additional inner path to the template.
	 * @param array  $args args Accessible from template.
	 *
	 * @return string
	 */
	public function load_template($name, $path = '', $args = array())
	{
		$resolver = new \DpdUKVendor\WPDesk\View\Resolver\ChainResolver();
		$resolver->appendResolver( new \DpdUKVendor\WPDesk\View\Resolver\WPThemeResolver( basename( $this->plugin_info->get_plugin_dir() ) ) );
		$resolver->appendResolver( new \DpdUKVendor\WPDesk\View\Resolver\DirResolver( trailingslashit( $this->plugin_info->get_plugin_dir() ) . 'templates' ) );
		$renderer = new DpdUKVendor\WPDesk\View\Renderer\SimplePhpRenderer( $resolver );

		return $renderer->render( trailingslashit( $path ) . $name, $args );
	}

	public function flexible_printing_integrations( array $integrations ) {
		$this->flexible_printing_integration                      = new WPDesk_WooCommerce_DPD_UK_Flexible_Printing_Integration( $this );
		$integrations[ $this->flexible_printing_integration->id ] = $this->flexible_printing_integration;

		return $integrations;
	}

	public function admin_enqueue_scripts( ) {
		$current_screen = get_current_screen();
		$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$suffix = '';
		if ( in_array( $current_screen->id, array( 'shop_order', 'edit-shop_order', 'woocommerce_page_wc-orders' ) ) ) {
			wp_register_style( 'dpd_uk_admin_css', $this->get_plugin_assets_url() . 'css/admin' . $suffix . '.css', array(), $this->script_version );
			wp_enqueue_style( 'dpd_uk_admin_css' );

			wp_enqueue_script( 'dpd_uk_admin_order_js', $this->get_plugin_assets_url() . 'js/admin-order' . $suffix . '.js', array( 'jquery' ), $this->script_version, true );

			wp_localize_script( 'dpd_uk_admin_order_js', 'dpd_uk_ajax_object', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			) );
		}
		if ( in_array( $current_screen->id, array( 'woocommerce_page_wc-settings' ) ) && isset( $_GET['section'] ) && $_GET['section'] === 'dpd_uk' ) {
			wp_register_style( 'dpd_uk_admin_css', $this->get_plugin_assets_url() . 'css/admin' . $suffix . '.css', array(), $this->script_version );
			wp_enqueue_style( 'dpd_uk_admin_css' );
		}
	}

	/**
	 * action_links function.
	 *
	 * @access public
	 *
	 * @param mixed $links
	 *
	 * @return array
	 */
	public function links_filter( $links ) {

		$plugin_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=dpd_uk' ) . '">' . __( 'Settings', 'woocommerce-dpd-uk' ) . '</a>',
			'docs'     => '<a target="_blank" href="https://octol.io/dpd-uk-docs">' . __( 'Docs', 'woocommerce-dpd-uk' ) . '</a>',
			'support'  => '<a target="_blank" href="https://octol.io/dpd-uk-support">' . __( 'Support', 'woocommerce-dpd-uk' ) . '</a>',
		);

		if ( defined( 'WC_VERSION' ) ) {
			if ( version_compare( WC_VERSION, '2.6', '<' ) ) {
				$plugin_links['settings'] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=wpdesk_woocommerce_dpd_uk_shipping_method' ) . '">' . __( 'Settings', 'woocommerce-dpd-uk' ) . '</a>';
			}
		} else {
			unset( $plugin_links['settings'] );
		}

		return array_merge( $plugin_links, $links );
	}

	/**
	 * @return WPDesk_WooCommerce_DPD_UK_Shipping_Method
	 */
	public function get_dpd_uk_shipping_method() {
		return new WPDesk_WooCommerce_DPD_UK_Shipping_Method();
	}

	private function init_upgrade_onboarding() {
		$upgrade_onboarding = new PluginUpgradeOnboardingFactory(
			str_replace( ' ', '&nbsp;', $this->plugin_info->get_plugin_name() ),
			$this->plugin_info->get_version(),
			$this->plugin_info->get_plugin_file_name(),
			'dpd_uk'
		);
		$upgrade_onboarding->add_upgrade_message(
			new PluginUpgradeMessage(
				'2.0.0',
				$this->plugin_info->get_plugin_url() . '/assets/images/icon-package-add.svg',
				__( 'DPD UK Pickup Points plugin compatibility', 'woocommerce-dpd-uk' ),
				sprintf(
					__( 'We have maintained the compatibility with the new %1$sDPD&nbsp;UK&nbsp;Pickup&nbsp;Points&nbsp;plugin%2$s we have just released. Combine them together, show your customers the DPD UK Pickup Points map to choose from and ship their orders flawlessly from now on with the DPD Ship to Shop service.', 'woocommerce-dpd-uk' ),
					'<a href="https://octol.io/dpd-uk-popup-info-pp" target="_blank">',
					'</a>'
				),
				'',
				''
			)
		);
		$upgrade_onboarding->create_onboarding();
	}

}
