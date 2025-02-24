<?php
/**
 * Class Flexible_Printing_Notices
 *
 * @package Flexible Printing
 */

use FPrintingVendor\WPDesk\Notice\Notice;
use FPrintingVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can display notices.
 */
class Flexible_Printing_Notices implements Hookable {

	/**
	 * .
	 */
	public function hooks() {
		add_action( 'admin_notices', array( $this, 'gcp_notice' ) );
	}

	/**
	 * .
	 */
	public function gcp_notice() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
		if ( 'flexible-printing-settings' === $page && 'authentication' === $tab ) {
			$link = get_locale() === 'pl_PL' ? 'https://www.google.com/intl/pl_ALL/cloudprint/learn/' : 'https://www.google.com/intl/en_ALL/cloudprint/learn/';
			new Notice(
				sprintf(
					// Translators: link.
					__( 'Google Cloud Print service is no longer officially supported by Google. Therefore, we cannot guarantee its proper functioning. %1$sRead more →%2$s', 'flexible-printing' ),
					'<a href="' . $link . '" target="_blank">',
					'</a>'
				),
				Notice::NOTICE_TYPE_ERROR
			);
		}
	}

}
