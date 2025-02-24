<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Base plugin class for WP Desk plugins
 *
 * @author Grzegorz
 *
 */

 class Flexible_Printing_Settings_Hooks {

 	private $plugin;
 	private $google_print;
 	private $integrations;
 	private $flexible_printing;

 	public function __construct(
 		Flexible_Printing_Plugin $plugin,
	    Flexible_Printing $flexible_printing,
	    Flexible_Printing_Google_Print $google_print,
	    Flexible_Printing_Integrations $integrations
    ) {
 		$this->plugin = $plugin;
 		$this->google_print = $google_print;
 		$this->integrations = $integrations;
 		$this->flexible_printing = $flexible_printing;
 		$this->hooks();
 	}

 	public function hooks() {
 		$func = str_replace( '-', '_', $this->plugin->get_namespace() );

 		// settings menu
 		add_filter( $func . '_menu', array( $this, 'settings_menu' ) );

 		// settings tabs
 		add_filter( $func . '_settings_tabs', array( $this, 'settings_tabs' ) );

 		// unsavable tabs
 		add_filter( $func . '_unsavable_tabs', array( $this, 'unsavable_tabs' ) );

 		// settings sections
 		add_filter( $func . '_registered_settings_sections', array( $this, 'registered_settings_sections' ) );

 		// settings
 		add_filter( $func . '_registered_settings', array( $this, 'registered_settings' ) );
 	}

 	public function get_text_domain() {
 		return $this->plugin->get_text_domain();
 	}

 	public function settings_menu( array $menu ) {
 		$menu['type']       = 'submenu';
 		$menu['parent']     = 'flexible-printing';
 		$menu['page_title'] = __( 'Flexible Printing Settings', $this->get_text_domain() );
 		$menu['show_title'] = true;
 		$menu['menu_title'] = __( 'Settings', $this->get_text_domain() );
 		$menu['capability'] = 'manage_options';
 		$menu['icon']       = 'dashicons-media-default';
 		$menu['position']   = null;
 		return $menu;
 	}

 	public function settings_tabs( $tabs ) {
 		$tabs = array(
		    'print_node'        =>  __( 'Print Node', 'flexible-printing' ),
 			'authentication'    =>  __( 'Google Cloud Print', 'flexible-printing' ),
 		);
// 		if ( $this->google_print->authenticated() ) {
			$tabs['printers'] = __( 'Printers', 'flexible-printing' );
//	    }
	    $integrations = $this->integrations->get_integrations();
 		if ( count( $integrations ) ) {
            $tabs['integrations'] = __( 'Integrations', 'flexible-printing' );
        }
 		return $tabs;
 	}

 	public function unsavable_tabs( $tabs ) {
 		$tabs = array(
 		);
        if ( $this->google_print->authenticated() ) {
            $tabs[] = 'authentication';
        }
 		return $tabs;
 	}

 	public function registered_settings_sections( $sections ) {
 		$sections = array(
 			'authentication' => array(
 				'authentication'     => __( 'Google Cloud Print', 'flexible-printing' ),
 			),
		    'print_node' => array(
		    	'print_node' => __( 'Print Node', 'flexible-printing' ),
		    )
 		);
// 		if ( $this->google_print->authenticated() ) {
			$sections['printers'] = array(
			    'default_printer'     => __( 'Default printer', 'flexible-printing' ),
		    );

            $integrations = $this->integrations->get_integrations();
            $sections['integrations'] = array();
            foreach ( $integrations as $key => $integration ) {
                $sections['integrations'][$integration->id] = $integration->title;
            }
//	    }
//	    if ( $this->google_print->authenticated() ) {
		    $printers = $this->plugin->flexible_printing->get_printers();
		    foreach ( $printers as $key => $printer ) {
			    $sections['printers'][ 'printer_' . $printer->get_id() ] = $printer->get_display_name();
		    }
//	    }
 		return $sections;
 	}

 	public function registered_settings( $settings ) {
	    $read_only = false;
	    if ( $this->google_print->authenticated() ) {
	    	$read_only = true;
	    }
 		$plugin_settings = array(
 				'authentication' => array(
                    'authentication' => array(
                        array(
                            'id'        => 'google_client_id',
                            'name'      => __( 'Google Client ID', 'flexible-printing' ),
                            'desc'      => '',
						    'readonly'  => $read_only,
                            'type'      => 'text',
                            'std'       => ''
                        ),
                        array(
                            'id'        => 'google_client_secret',
                            'name'      => __( 'Google Client Secret', 'flexible-printing' ),
                            'desc'      => '',
                            'readonly'  => $read_only,
                            'type'      => 'password',
                            'std'       => ''
                        ),
					    array(
						    'id'        => 'authorized_redirect_url',
						    'name'      => __( 'Authorized redirect URI', 'flexible-printing' ),
						    'desc'      => '',
						    'type'      => 'text',
						    'readonly'  => true,
						    'std'       => $this->google_print->redirect_uri()
					    ),
                    ),
 				),
			    'print_node' => array(
				    'print_node' => array(
					    array(
						    'id'        => 'print_node_api_key',
						    'name'      => __( 'API Key', 'flexible-printing' ),
						    'desc'      => '',
						    'type'      => 'text',
						    'std'       => ''
					    ),
				    ),
			    ),
			    'printers' => array(
				    'default_printer' => array(
				    	array(
						    'id'   => 'default_printer',
						    'name' => __( 'Default Printer', 'flexible-printing' ),
						    'desc' => '',
						    'type' => 'select',
						    'std'  => '',
							'options' => array_merge( array( '' => __( 'Select printer', 'flexible-printing' ) ), $this->flexible_printing->printers_options() ),
					    )
				    ),
			    )
	    );

	    if ( $this->google_print->authenticated() ) {
	    	$printers = $this->plugin->flexible_printing->get_printers();
	    	foreach ( $printers as $printer ) {
			    $plugin_settings['printers']['printer_' . $printer->get_id()] = $printer->get_printer_settings( 'printer_' . $printer->get_id() );
		    }
	    }

        $plugin_settings['integrations'] = array( 'integrations' => array() );
        $integrations = $this->integrations->get_integrations();
        foreach ( $integrations as $key => $integration ) {
            $integration_options = array(
                array(
                    'id'   => $key . '_header',
                    'name' => $integration->title,
                    'desc' => '',
                    'type' => 'header'
                ),
                array(
                    'id'   => $key . '_default_printer',
                    'name' => __( 'Printer', 'flexible-printing' ),
                    'desc' => '',
                    'type' => 'select',
                    'std'  => '',
                    'options'   => $this->flexible_printing->printers_options( true ),
	                'class'     => 'integration-printer'
                )
            );

	        $options_printers = array();

	        if ( $this->google_print->authenticated() ) {
		        $printers = $this->plugin->flexible_printing->get_printers();
		        foreach ( $printers as $printer ) {
		        	$options_count = 0;
			        $options_printers[] = array (
				        'id'    => $key . '_printer_' . $printer->get_id() . '_name',
				        'name'  => __( 'Printer settings', 'flexible-printing' ),
				        'desc'  => '',
				        'type'  => 'header',
				        'class' => 'printer-setting ' . $printer->get_id(),
				        'std'   => '',
			        );
			        $printer_options = $printer->get_printer_settings( $key . '_printer_' . $printer->get_id() );
			        $options_count = count( $printer_options );
			        if ( $options_count > 1 ) {
			        	foreach ( $printer_options as $printer_option ) {
							if ( is_array( $printer_option ) ) {
								$printer_option['class'] = 'printer-setting ' . $printer->get_id();
							}
					        $options_printers[] = $printer_option;
				        }
			        }
			        if ( $options_count == 1 ) {
				        $options_printers[] = array(
					        'id'        => $key . '_printer_' . $printer->get_id() . '_no_options',
					        'name'      => '',
					        'type'      => 'descriptive_text',
					        'desc'      => __( 'Printer has no settings', 'flexible-printing' ),
					        'class'     => 'printer-setting printer-no-setting ' . $printer->get_id(),
				        );
			        }
		        }
	        }

	        $add_options = $integration->options();
            foreach ( $add_options as $key_add_option => $add_option ) {
                $add_options[$key_add_option]['id'] = $key . '_' . $add_options[$key_add_option]['id'];
            }
            $plugin_settings['integrations'][$key] = array_merge( array_merge( $integration_options, $add_options ), $options_printers );
        }
		return array_merge( $settings, $plugin_settings );
 	}

}

