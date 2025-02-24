<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Base plugin class for WP Desk plugins
 *
 * @author Grzegorz
 *
 */

abstract class Flexible_Printing_Printer {

	protected $printer_data;
	protected $printer_type;

	public function __construct( $printer_type, $printer_data ) {
		$this->printer_type = $printer_type;
		$this->printer_data = $printer_data;
		$this->hooks();
	}

	public function hooks() {
	}

	public function get_test_page() {
		return 'test';
	}

	public function get_printer_settings( $id_prefix ) {
		return array();
	}

	public function get_print_options( $integration ) {
		return false;
	}


	public function get_printer_type() {
		return $this->printer_type;
	}

	public function get_printer_data() {
		return $this->printer_data;
	}

	public function get_id() {
		return '';
	}

	public function get_display_name() {
		return '';
	}

	public function print_document( $integration, $content, $content_type, $title ) {
	}

}

class Flexible_Printing_Printer_GCP extends Flexible_Printing_Printer {

	private $google_print;

	public function __construct( $printer_data, Flexible_Printing_Google_Print $google_print ) {
		parent::__construct( 'gcp', $printer_data );
		$this->google_print = $google_print;
	}

	public function get_id() {
		return 'gcp_' . $this->printer_data->id;
	}

	public function get_display_name() {
		return $this->printer_data->displayName . ' ' . __( '(GCP)', 'flexible-printing' );
	}

	public function get_printer_id() {
		return $this->printer_data->id;
	}

	public function get_test_page() {
		$content = '';
		ob_start();
		include( 'views/print-test-page.php' );
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function print_document( $integration, $content, $content_type, $title ) {
		$this->google_print->print_document( $integration, $content, $content_type, $title, $this->get_id() );
	}

	public function get_printer_settings( $id_prefix ) {
		$printer = $this;
		$settings = array();
		$settings[] = array (
			'id'   => $id_prefix . '_name',
			'name' => $printer->get_display_name(),
			'desc' => '',
			'type' => 'header',
			'std'  => '',
		);
		if ( isset( $printer->printer_data->options )
		     && isset( $printer->printer_data->options->capabilities )
		     && isset( $printer->printer_data->options->capabilities->printer )
		) {
			foreach ( $printer->printer_data->options->capabilities->printer as $cap => $val ) {
				if ( isset( $val->option ) && is_array( $val->option ) && count( $val->option ) > 0 ) {
					$options = array();
					$std = '';
					foreach ( $val->option as $option_key => $option_val ) {
						$options[$option_key] = print_r( $option_val, true );
						if ( isset( $option_val->custom_display_name ) ) {
							$options[$option_key] = $option_val->custom_display_name;
						}
						else if ( isset( $option_val->type ) ) {
							$options[$option_key] = $option_val->type;
						}
						else if ( $cap == 'dpi' ) {
							$options[$option_key] = $option_val->horizontal_dpi . ' x ' . $option_val->vertical_dpi;
						}
						if ( isset( $option_val->is_default ) ) {
							$std = strval( $option_key );
						}
						$options[$option_key] = $this->google_print->get_translation( $options[$option_key] );
					}
					$settings[] = array(
						'id'        => $id_prefix . '_cap_' . $cap,
						'name'      => $this->google_print->get_translation( $cap ),
						'type'      => 'select',
						'options'   => $options,
						'std'       => $std,
					);
				}
				else {
					$std = 1;
					$cap_settings = array(
						'id'        => $id_prefix . '_cap_' . $cap,
						'name'      => $this->google_print->get_translation( $cap ),
						'type'      => 'text',
						'std'       => $std,
					);
					if ( $cap == 'copies' ) {
						$cap_settings['type'] = 'number';
						$cap_settings['min'] = 1;
						$cap_settings['max'] = 100;
					}
					if ( $cap == 'collate' ) {
						$cap_settings['type'] = 'checkbox';
					}
					if ( $cap != 'supported_content_type'
					     && $cap != 'vendor_capability'
					) {
						$settings[] = $cap_settings;
					}
				}
			}
		}
		if ( 1 == 2 ) {
			$settings[] = array(
				'id'   => $id_prefix . '_data',
				'name' => 'data',
				'type' => 'descriptive_text',
				'desc' => '<pre>' . print_r( $printer->printer_data, true ) . '</pre>',
			);
		}
		return $settings;
	}


}

class Flexible_Printing_Printer_PN extends Flexible_Printing_Printer {

	private $print_node;

	public function __construct( $printer_data, Flexible_Printing_Print_Node $print_node ) {
		parent::__construct( 'gcp', $printer_data );
		$this->print_node = $print_node;
	}

	public function get_id() {
		return 'pn_' . $this->printer_data->id;
	}

	public function get_printer_id() {
		return $this->printer_data->id;
	}

	public function get_display_name() {
		return $this->printer_data->name . ' ' . __( '(PN)', 'flexible-printing' );
	}

	public function get_printer_settings( $id_prefix ) {
		$printer = $this;
		$settings = array();
		$caps = $this->printer_data->capabilities;
		$settings[] = array (
			'id'   => $id_prefix . '_name',
			'name' => $printer->get_display_name(),
			'desc' => '',
			'type' => 'header',
			'std'  => '',
		);
		foreach ( $caps as $cap => $val ) {
			if ( in_array( $cap, array( 'extent', 'printrate' ) ) ) {
				continue;
			}
			$type = false;
			$options = array( '' => __( 'Default (not set)', 'flexible-printing' ) );
			$std = '';
			if ( is_array( $val ) && count( $val ) ) {
				foreach ( $val as $val_key=>$val_val ) {
					$options[ $val_key ] = $val_val;
					if ( $cap == 'papers' ) {
						$options[ $val_key ] = $val_key;
					}
					else {
						if ( is_array( $val_val ) ) {
							$options[ $val_key ] = print_r( $val_key );
						}
					}
				}
				$type = 'select';
			}
			if ( $cap == 'duplex' && $val === true ) {
				$options['none'] = __( 'None', 'flexible-printing' );
				$options['long-edge'] = __( 'Long edge', 'flexible-printing' );
				$options['short-edge'] = __( 'Short edge', 'flexible-printing' );
				$settings[] = array(
					'id'        => $id_prefix . '_cap_' . $cap,
					'name'      => $this->print_node->get_translation( $cap ),
					'type'      => 'select',
					'options'   => $options,
					'std'       => $std,
				);
			}
			if ( $cap == 'copies' ) {
				$type = 'number';
				$std = 1;
			}
			if ( $type == 'number' ) {
				$settings[] = array(
					'id'        => $id_prefix . '_cap_' . $cap,
					'name'      => $this->print_node->get_translation( $cap ),
					'type'      => 'number',
					'std'       => $std,
				);
			}
			if ( $type == 'select' ) {
				$settings[] = array(
					'id'        => $id_prefix . '_cap_' . $cap,
					'name'      => $this->print_node->get_translation( $cap ),
					'type'      => 'select',
					'options'   => $options,
					'std'       => $std,
				);
			}
		}
		return $settings;
	}

	public function get_print_options( $integration ) {
		$print_options = new stdClass();
		//$print_options = array();
		$printer = $this;
		$printer_id = $this->get_id();
		$caps = array();
		foreach ( $this->print_node->plugin->settings->get_settings() as $key => $setting ) {
			if ( strpos( $key, $integration . '_printer_' . $printer_id . '_cap_' ) === 0 ) {
				$cap = substr( $key, strlen( $integration . '_printer_' . $printer_id . '_cap_' ) );
				$caps[$cap] = $setting;
			}
			if ( strpos( $key, 'printer_' . $printer_id . '_cap_' ) === 0 ) {
				$cap = substr( $key, strlen( 'printer_' . $printer_id . '_cap_' ) );
				if ( !isset( $caps[$cap] ) ) {
					$caps[ $cap ] = $setting;
				}
			}
		}
		foreach ( $caps as $key=>$val) {
			if ( $key == 'papers' ) {
				$key = 'paper';
			}
			$print_options->{$key} = $val;
			//$print_options[$key] = $val;
		}
		return $print_options;
	}

	public function print_document( $integration, $content, $content_type, $title ) {
		$this->print_node->print_document( $integration, $content, $content_type, $title, $this->get_id() );
	}

}