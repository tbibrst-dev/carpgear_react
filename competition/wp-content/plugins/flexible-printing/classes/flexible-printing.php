<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Base plugin class for WP Desk plugins
 *
 * @author Grzegorz
 *
 */

class Flexible_Printing {

	private $plugin;

	private $translations = array();

	private $access_token = '';

	public function __construct( Flexible_Printing_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->init_translarions();
		$this->hooks();
	}

	private function init_translarions() {
		$this->translations = array(
			'media_size'        => __( 'Media size', 'flexible-printing' ),
			'page_orientation'  => __( 'Page orientation', 'flexible-printing' ),
			'duplex'            => __( 'Duplex', 'flexible-printing' ),
			'dpi'               => __( 'DPI', 'flexible-printing' ),
			'NO_DUPLEX'         => __( 'No duplex', 'flexible-printing' ),
			'LONG_EDGE'         => __( 'Long edge', 'flexible-printing' ),
			'SHORT_EDGE'        => __( 'Short edge', 'flexible-printing' ),
			'PORTRAIT'          => __( 'Portrait', 'flexible-printing' ),
			'LANDSCAPE'         => __( 'Landscape', 'flexible-printing' ),
			'AUTO'              => __( 'Auto', 'flexible-printing' ),
			'color'             => __( 'Color', 'flexible-printing' ),
			'STANDARD_COLOR'    => __( 'Standard color', 'flexible-printing' ),
			'collate'           => __( 'Collate', 'flexible-printing' ),
			'copies'            => __( 'Copies', 'flexible-printing' ),
		);
	}

	public function get_translation( $text ) {
		if ( isset( $this->translations[$text] ) ) {
			return $this->translations[$text];
		}
		return $text;
	}

	public function hooks() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 1 );

		add_action( 'flexible_printing_settings_tab_bottom_printers_default_printer', array( $this, 'refresh_printers' ) );

		add_filter( 'flexible_printing_settings_tab_url', array( $this, 'flexible_printing_settings_tab_url' ) );

		add_filter( 'flexible_printing_settings_section_url', array( $this, 'flexible_printing_settings_section_url' ) );

		add_filter( 'flexible_printing_printers', array( $this, 'flexible_printing_printers' ) );
		add_filter( 'flexible_printing_print', array( $this, 'flexible_printing_print' ), 10, 5 );

		add_filter( 'flexible_printing_media_size', array( $this, 'flexible_printing_media_size' ) );
		add_filter( 'flexible_printing_media_size_options', array( $this, 'flexible_printing_media_size_options' ) );

		add_action( 'flexible_printing_settings_tab_bottom_printers', array( $this, 'reset_defaults' ), 10 );
		add_action( 'flexible_printing_settings_tab_bottom_printers', array( $this, 'test_print' ), 11 );

		add_action( 'flexible_printing_settings_tab_bottom_integrations', array( $this, 'integrations_printer_settings' ) );

		add_filter( 'flexible_printing_integration_url', array( $this, 'flexible_printing_integration_url' ) );

	}

	function admin_menu() {
		add_menu_page( __('Flexible Printing', 'flexible-printing' ), __('Flexible Printing', 'flexible-printing' ), 'manage_options', 'flexible-printing' );
	}

	public function flexible_printing_integration_url( $integration ) {
		return admin_url( 'admin.php?page=flexible-printing-settings&tab=integrations&section=' . $integration );
	}

	public function flexible_printing_media_size( array $options ) {
		$options['A4'] = array(
			'height_microns'        => 296900,
			'name'                  => 'ISO_A4',
			'width_microns'         => 209900,
			'custom_display_name'   => 'A4',
		);
		$options['A5'] = array(
			'height_microns'        => 209900,
			'name'                  => 'ISO_A5',
			'width_microns'         => 147900,
			'custom_display_name'   => 'A5'
		);
		return $options;
	}

	public function flexible_printing_media_size_options( array $options ) {
		$media_sizes = apply_filters( 'flexible_printing_media_size', array() );
		$options = array();
		foreach ( $media_sizes as $key => $media_size ) {
			$options[$key] = $media_size['custom_display_name'];
		}
		return $options;
	}

	public function flexible_printing_settings_tab_url( $tab_url ) {
		$tab_url = remove_query_arg( 'refresh', $tab_url );
		$tab_url = remove_query_arg( 'state', $tab_url );
		$tab_url = remove_query_arg( 'message', $tab_url );
		return $tab_url;
	}

	public function flexible_printing_settings_section_url( $tab_url ) {
		$tab_url = remove_query_arg( 'refresh', $tab_url );
		$tab_url = remove_query_arg( 'state', $tab_url );
		$tab_url = remove_query_arg( 'message', $tab_url );
		return $tab_url;
	}

	public function integrations_printer_settings( $section ) {
		include( 'views/integrations-printer-settings.php' );
	}

	public function common_js() {
		include( 'views/common-js.php' );
	}

	public function test_print( $section ) {
		if ( $section != 'default_printer' ) {
			include( 'views/test-print.php' );
		}
	}

	public function reset_defaults( $section ) {
		if ( $section != 'printer___google__docs' && $section != 'default_printer' ) {
			include( 'views/reset-defaults.php' );
		}
	}

	public function refresh_printers() {
		include( 'views/refresh-printers.php' );
		$this->common_js();
	}

	public function  flexible_printing_printers( array $ret ) {
		$printers = $this->get_printers();
		$ret['-1'] = __( 'Default printer', 'flexible-printing' );
		foreach ( $printers as $printer ) {
			$ret[$printer->get_id()] = $printer->get_display_name();
		}
		return $ret;
	}

	public function flexible_printing_print( $integration, $args ) {
		$defaults = array(
			'printer_id'    => $this->plugin->get_option( $integration . '_default_printer', false ),
			'content_type'  => 'application/pdf',
		);
		if ( $defaults['printer_id'] == -1 ) {
			$defaults['printer_id'] = false;
		}
		$args = array_merge( $defaults, $args );
		$ret = $this->print_document( $integration, $args['content'], $args['content_type'], $args['title'], $args['printer_id'] );
		return $ret;
	}

	public function get_option( $key, $default ) {
		return $this->plugin->get_option( $key, $default );
	}

	public function print_document( $integration, $content, $content_type = 'text/html', $title = false, $printer_id = false ) {
		if ( $printer_id === false ) {
			$printer_id = $this->get_option( 'default_printer', '' );
		}
		if ( $title == false ) {
			$title = 'flexible-printing-' . rand( 1, 100000 );
		}
		try {
            $printer = $this->get_printer( $printer_id );
        }
        catch ( Exception $e ) {
            $printer_id = $this->get_option( 'default_printer', '' );
            try {
                $printer = $this->get_printer($printer_id);
            }
            catch ( Exception $e ) {
                throw new Exception( __( 'Please set default printer!', 'flexible-printing' ) );
            }
        }
		$printer->print_document( $integration, $content, $content_type, $title );
		return;
	}

	public function get_printers() {
		$printers = array();
		try {
			$gcp_printers = $this->plugin->google_print->get_printers();
			$pn_printers = $this->plugin->print_node->get_printers();
			foreach ( $gcp_printers as $key => $printer ) {
				$flexible_printing_printer                        = new Flexible_Printing_Printer_GCP( $printer, $this->plugin->google_print );
				$printers[ $flexible_printing_printer->get_id() ] = $flexible_printing_printer;
			}
			foreach ( $pn_printers as $key => $printer ) {
				$flexible_printing_printer                        = new Flexible_Printing_Printer_PN( $printer, $this->plugin->print_node );
				$printers[ $flexible_printing_printer->get_id() ] = $flexible_printing_printer;
			}
		}
		catch ( Exception $e ) {
		}
		return $printers;
	}

	public function printers_options( $add_default = false ) {
		$ret = array();
		$printers = $this->get_printers();
		if ( is_array( $printers ) ) {
			if ( $add_default ) {
				$ret['-1'] = __( 'Default printer', 'flexible-printing' );
			}
			foreach ( $printers as $key => $printer ) {
				$ret[$printer->get_id()] = $printer->get_display_name();
			}
		}
		return $ret;
	}


	public function get_printer( $printer_id ) {
		$printers = $this->get_printers();
		if ( !isset( $printers[$printer_id] ) ) {
			throw new Exception( __( 'Printer not found!', 'flexible-printing' ) );
		}
		return $printers[$printer_id];
	}

}
