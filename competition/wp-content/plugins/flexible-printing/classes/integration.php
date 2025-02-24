<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Flexible_Printing_Integration {

	public $id = null;
	public $title = null;

	public function __construct() {
		$this->id = 'fp';
		$this->title = 'Flexible Printing Integration';
	}

	public function options() {
		return array();
	}

	public function get_option( $option, $default = null ) {
		return apply_filters( 'flexible_printing_integration_option', $default, $this->id, $option );
	}

	public function print_button( $args ) {
		return apply_filters( 'flexible_printing_print_button', '', $this->id, $args );
	}

	public function do_print( $title, $content, $content_type = 'application/pdf', $silent = false ) {
		$args = array(
			'title'         => $title,
			'content'       => $content,
			'content_type'  => $content_type,
			'silent'        => $silent,
		);
		do_action( 'flexible_printing_print', $this->id, $args );
	}

	public function do_print_action( $args ) {
		throw new Exception( __( 'Method do_print must be overwritten in integration class.', 'flexible-printing' ) );
	}

}