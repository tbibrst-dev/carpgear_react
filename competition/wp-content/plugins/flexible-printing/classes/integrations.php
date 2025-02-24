<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Base plugin class for WP Desk plugins
 *
 * @author Grzegorz
 *
 */

class Flexible_Printing_Integrations {

    private $_plugin;
    private $_google_print;
    private $integrations = false;

    public function __construct( Flexible_Printing_Plugin $plugin, Flexible_Printing_Google_Print $google_print ) {
        $this->_plugin = $plugin;
        $this->_google_print = $google_print;
        $this->hooks();
    }

    public function hooks() {
        add_filter( 'flexible_printing_integration_option', array( $this, 'flexible_printing_integration_option' ), 10, 3 );

        add_filter( 'flexible_printing_print_button', array( $this, 'flexible_printing_print_button' ), 10, 3 );

        add_action( 'wp_ajax_flexible_printing', array( $this, 'wp_ajax_flexible_printing' ) );

        add_action( 'flexible_printing_print_fp', array( $this, 'flexible_printing_print_fp' ) );

        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        $this->integrations = $this->get_integrations();
    }

    public function flexible_printing_print_fp( $args ) {
        $section = sanitize_text_field( $args['data-section'] );
        $printer_id = substr( $section, 8 );
        $printer = $this->_plugin->flexible_printing->get_printer( $printer_id );
        $content = file_get_contents( $this->_plugin->get_plugin_dir() . '/assets/pdf/test_page.pdf' );
        $args_print = array(
            'printer_id'    => $printer_id,
            'title'         => __( 'Print test page', 'flexible-printing' ),
            'content'       => $content,
            'content_type'  => 'application/pdf'
        );
        do_action( 'flexible_printing_print', 'fp', $args_print );
    }

    public function flexible_printing_print_button( $content, $integration, $args ) {
        $class = '';
        if ( isset( $args['class'] ) ) {
            $class = $args['class'];
        }
        $icon = false;
        if ( isset( $args['icon'] ) && $args['icon'] == true ) {
            $icon = true;
        }
        $printers = apply_filters( 'flexible_printing_printers', array() );
        $printer_id = apply_filters( 'flexible_printing_integration_option', '0', $integration, 'default_printer' );
        $printer = '';
        if ( isset( $printers[$printer_id] ) ) {
            $printer = $printers[$printer_id];
        }
        $label = '';
        if ( isset( $args['label'] ) ) {
            $label = sprintf($args['label'], $printer);
        }
        $tip = "";
        if ( isset( $args['tip'] ) ) {
            $tip = sprintf($args['tip'], $printer);
        }
        $title = "";
        if ( isset( $args['title'] ) ) {
            $title = sprintf($args['title'], $printer);
        }
        ob_start();
        include( 'views/print-button.php' );
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    public function get_integrations() {
        if ( $this->integrations === false ) {
            $integrations = array();
            $this->integrations = apply_filters( 'flexible_printing_integrations', $integrations );
        }
        return $this->integrations;
    }

    public function flexible_printing_integration_option( $val, $integration, $option ) {
        return $this->_plugin->get_option( $integration . '_' . $option, $val );
    }

    public function wp_ajax_flexible_printing() {
        $integration_id = '';
        if ( isset( $_REQUEST['data-integration']) ) {
	        $integration_id = sanitize_text_field( $_REQUEST['data-integration'] );
        }
        check_ajax_referer( 'flexible-printing-' . $integration_id, 'data-security' );
        $ret = array();
	    $ret['id'] = isset($_REQUEST['id'])? sanitize_text_field( $_REQUEST['id'] ): '';
        if ( isset( $this->integrations[$integration_id] ) ) {
	        $integration = $this->integrations[ $integration_id ];
            $ret['message'] = __( 'Printed', 'flexible-printing' );
            $ret['status'] = 'ok';
	        $args = array( 'data' => array() );
	        foreach ( $_REQUEST as $key => $value ) {
	            if ( strpos( $key, 'data-' ) === 0 ) {
	                $data_key = substr( $key, strlen( 'data-' ) );
	                $args['data'][$data_key] = sanitize_text_field( $value );
                }
                else {
	                $args[$key] = sanitize_text_field( $value );
                }
            }
	        try {
		        $integration->do_print_action( $args );
	        }
	        catch ( Exception $e ) {
		        $ret['message'] = $e->getMessage();
                $ret['status'] = 'error';
	        }
        }
        else {
	        if ( $integration_id == 'fp' ) {
		        $ret['message'] = __( 'Printed', 'flexible-printing' );
		        $args = $_REQUEST;
		        try {
			        $this->flexible_printing_print_fp( $args );
		        }
		        catch ( Exception $e ) {
                    $ret['status'] = 'error';
			        $ret['message'] = $e->getMessage();
		        }
	        }
	        else {
                $ret['status'] = 'error';
		        $ret['message'] = sprintf( __( 'Unknown integration (%s)!', 'flexible-printing' ), $integration_id );
	        }
        }
        echo json_encode( $ret );
        wp_die();
    }


}
