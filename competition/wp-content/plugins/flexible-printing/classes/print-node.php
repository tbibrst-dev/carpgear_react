<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Base plugin class for WP Desk plugins
 *
 * @author Grzegorz
 *
 */

include( 'PrintNode/Bootstrap.php' );

class Flexible_Printing_Print_Node {

	public $plugin;

	private $translations = array();

	private $print_node_api_key = '';

	public function __construct( Flexible_Printing_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->print_node_api_key = $this->plugin->get_option( 'print_node_api_key', '' );
		$this->init_translations();
		$this->hooks();
	}

	public function hooks() {
	}

	private function init_translations() {
		$this->translations = array(
			'bins'        => __( 'Bin', 'flexible-printing' ),
			'dpis'        => __( 'DPI', 'flexible-printing' ),
			'duplex'      => __( 'Duplex', 'flexible-printing' ),
			'medias'      => __( 'Medias', 'flexible-printing' ),
			'papers'      => __( 'Papers', 'flexible-printing' ),
			'nup'         => __( 'Numbers of pages per page', 'flexible-printing' ),
			'copies'      => __( 'Copies', 'flexible-printing' ),
		);
	}

	public function get_translation( $text ) {
		if ( isset( $this->translations[$text] ) ) {
			return $this->translations[$text];
		}
		return $text;
	}

	public function get_printers() {
		$transient_name = 'flexible_printing_printers_pn';
		$printers = get_transient( $transient_name );
		if ( is_array( $printers ) && count( $printers ) == 0 ) {
			$printers = false;
		}
		if ( ! is_array( $printers ) ) {
			$printers = array();
			if ( $this->print_node_api_key != '' ) {

				$credentials = new \PrintNode\Credentials\ApiKey( $this->print_node_api_key );
				$client = new \PrintNode\Client($credentials);

				try {
					$printers = $client->viewPrinters();
				} catch ( Exception $e ) {
				}
			}
			set_transient( $transient_name, $printers, WEEK_IN_SECONDS );
		}
		return $printers;
	}

	public function print_document( $integration, $content, $content_type = 'text/html', $title = false, $printer_id = false ) {
		if ( $printer_id === false ) {
			$printer_id = $this->get_option( 'default_printer', '' );
		}
		if ( $title == false ) {
			$title = 'flexible-printing-' . rand( 1, 100000 );
		}

		$printer_name = false;

		try {
			$printer = $this->plugin->flexible_printing->get_printer( $printer_id );

			if ( $printer !== false ) {
				$printer_name = $printer->get_display_name();
			}

			$credentials = new \PrintNode\Credentials\ApiKey( $this->print_node_api_key );
			$client = new \PrintNode\Client($credentials);

			$print_job = new \PrintNode\Entity\PrintJob($client);

			$print_job->printer = $printer->get_printer_id();

			$print_job->contentType = 'pdf_base64';

			if ( $content_type != 'application/pdf' ) {
				$print_job->contentType = 'raw_base64';
			}

			$print_job->content = base64_encode( $content );

			$print_job->source = 'flexible-printing';

			$print_job->title = $title;

			//$options = $printer->get_print_options( $integration );

			//$print_job->options = $options;

			$print_job_id = $client->createPrintJob($print_job);

			$status_code = $client->lastResponse->httpStatusCode;
			$status_message = $client->lastResponse->httpStatusMessage;

			if ( $status_code == '201' ) {
				do_action( 'flexible_printing_log', $integration, $printer_name, $title, '$print_job_id', '', '' );
			}
			else {
				do_action( 'flexible_printing_log', $integration, $printer_name, $title, '', $status_message, $response );
				throw new Exception( $status_message );
			}
			return $print_job_id;

		}
		catch ( Exception $e ) {
			do_action( 'flexible_printing_log', $integration, $printer_name, $title, '', $e->getMessage(), '' );
			throw $e;
		}
	}


}
