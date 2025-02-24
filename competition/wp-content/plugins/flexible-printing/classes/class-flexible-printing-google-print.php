<?php
/**
 * Google Cloud Print.
 *
 * @package Flexible Printing
 */

/**
 * Class Flexible_Printing_Google_Print
 */
class Flexible_Printing_Google_Print {

	/**
	 * Plugin.
	 *
	 * @var Flexible_Printing_Plugin
	 */
	private $plugin;

	/**
	 * Translations.
	 *
	 * @var array
	 */
	private $translations = array();

	/**
	 * Access token.
	 *
	 * @var string
	 */
	private $access_token = '';

	/**
	 * Flexible_Printing_Google_Print constructor.
	 *
	 * @param Flexible_Printing_Plugin $plugin .
	 */
	public function __construct( Flexible_Printing_Plugin $plugin ) {
		$this->plugin       = $plugin;
		$this->access_token = get_option( 'flexible-printing-access-token', '' );
		$this->init_translations();
		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'wp_init' ) );

		add_action(
			'flexible_printing_settings_tab_bottom_authentication_authentication',
			array( $this, 'do_authenticate' )
		);
		add_action(
			'flexible_printing_settings_tab_top_authentication_authentication',
			array( $this, 'authenticate_messages' )
		);
	}

	/**
	 * Init translations.
	 */
	private function init_translations() {
		$this->translations = array(
			'media_size'       => __( 'Media size', 'flexible-printing' ),
			'page_orientation' => __( 'Page orientation', 'flexible-printing' ),
			'duplex'           => __( 'Duplex', 'flexible-printing' ),
			'dpi'              => __( 'DPI', 'flexible-printing' ),
			'NO_DUPLEX'        => __( 'No duplex', 'flexible-printing' ),
			'LONG_EDGE'        => __( 'Long edge', 'flexible-printing' ),
			'SHORT_EDGE'       => __( 'Short edge', 'flexible-printing' ),
			'PORTRAIT'         => __( 'Portrait', 'flexible-printing' ),
			'LANDSCAPE'        => __( 'Landscape', 'flexible-printing' ),
			'AUTO'             => __( 'Auto', 'flexible-printing' ),
			'color'            => __( 'Color', 'flexible-printing' ),
			'STANDARD_COLOR'   => __( 'Standard color', 'flexible-printing' ),
			'collate'          => __( 'Collate', 'flexible-printing' ),
			'copies'           => __( 'Copies', 'flexible-printing' ),
		);
	}

	/**
	 * Get translations.
	 *
	 * @param string $text .
	 *
	 * @return string
	 */
	public function get_translation( $text ) {
		if ( isset( $this->translations[ $text ] ) ) {
			return $this->translations[ $text ];
		}

		return $text;
	}

	/**
	 * Is authenticated?
	 *
	 * @return bool
	 */
	public function authenticated() {
		if ( '' !== $this->access_token ) {
			try {
				$access_token = $this->get_access_token();
			} catch ( Exception $e ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Get access token.
	 *
	 * @return object
	 */
	private function get_access_token() {
		$client = new Google_Client();
		$client->setClientId( $this->plugin->flexible_printing->get_option( 'google_client_id', '' ) );
		$client->setClientSecret( $this->plugin->flexible_printing->get_option( 'google_client_secret', '' ) );
		$client->setAccessToken( json_encode( $this->access_token ) ); // phpcs:ignore
		if ( $client->isAccessTokenExpired() ) {
			$client->refreshToken( $this->access_token->refresh_token );
			$access_token               = json_decode( $client->getAccessToken() );
			$access_token->expires_time = time() + $access_token->expires_in - 60;
			update_option( 'flexible-printing-access-token', $access_token );
			$this->access_token = $access_token;
		}

		return $this->access_token->access_token;
	}

	/**
	 * Print document.
	 *
	 * @param string      $integration .
	 * @param string      $content .
	 * @param string      $content_type .
	 * @param bool|string $title .
	 * @param bool|string $printer_id .
	 *
	 * @return mixed
	 * @throws \Glavweb\GoogleCloudPrint\Exception .
	 */
	public function print_document( $integration, $content, $content_type = 'text/html', $title = false, $printer_id = false ) {
		if ( false === $printer_id ) {
			$printer_id = $this->get_option( 'default_printer', '' );
		}
		if ( false === $title ) {
			$title = 'flexible-printing-' . rand( 1, 100000 ); // phpcs:ignore
		}
		$ticket  = array(
			'version' => '1.0',
			'print'   => array(),
		);
		$printer = $this->plugin->flexible_printing->get_printer( $printer_id );
		if ( false !== $printer ) {
			$caps = array();
			foreach ( $this->plugin->settings->get_settings() as $key => $setting ) {
				if ( strpos( $key, $integration . '_printer_' . $printer_id . '_cap_' ) === 0 ) {
					$cap          = substr( $key, strlen( $integration . '_printer_' . $printer_id . '_cap_' ) );
					$caps[ $cap ] = $setting;
				}
				if ( strpos( $key, 'printer_' . $printer_id . '_cap_' ) === 0 ) {
					$cap = substr( $key, strlen( 'printer_' . $printer_id . '_cap_' ) );
					if ( ! isset( $caps[ $cap ] ) ) {
						$caps[ $cap ] = $setting;
					}
				}
			}
			if ( isset( $printer->options )
				&& isset( $printer->options->capabilities )
				&& isset( $printer->options->capabilities->printer )
			) {
				foreach ( $caps as $cap => $setting ) {
					if ( isset( $printer->options->capabilities->printer->{$cap} ) ) {
						if ( isset( $printer->options->capabilities->printer->{$cap}->option ) ) {
							if ( isset( $printer->options->capabilities->printer->{$cap}->option[ $setting ] ) ) {
								$ticket['print'][ $cap ] = $printer->options->capabilities->printer->{$cap}->option[ $setting ];
							}
						} else {
							if ( 'copies' === $cap ) {
								$ticket['print'][ $cap ] = array( $cap => intval( $setting ) );
							}
							if ( 'collate' === $cap ) {
								if ( 0 === intval( $setting ) ) {
									$ticket['print'][ $cap ] = array( $cap => false );
								} else {
									$ticket['print'][ $cap ] = array( $cap => true );
								}
							}
						}
					}
					if ( isset( $ticket['print'][ $cap ] ) ) {
						if ( isset( $ticket['print'][ $cap ]->name ) ) {
							unset( $ticket['print'][ $cap ]->name );
						}
						if ( isset( $ticket['print'][ $cap ]->custom_display_name ) ) {
							unset( $ticket['print'][ $cap ]->custom_display_name );
						}
						if ( isset( $ticket['print'][ $cap ]->is_default ) ) {
							unset( $ticket['print'][ $cap ]->is_default );
						}
					}
				}
			}
		}
		if ( 0 === count( $ticket['print'] ) ) {
			unset( $ticket['print'] );
		}
		$args     = array(
			'printerid'               => $printer->get_printer_id(),
			'title'                   => $title,
			'content'                 => base64_encode( $content ), // phpcs:ignore
			'contentType'             => $content_type,
			'contentTransferEncoding' => 'base64',
			'tag'                     => 'Flexible Printing ' . site_url(),
			'ticket'                  => json_encode( $ticket ), // phpcs:ignore
		);
		$print    = new Flexible_Printing_Google_CLoud_Print( $this->get_access_token() );
		$response = $print->submit( $args );
		unset( $response->request->params->content );
		$printer_name = false;
		if ( false !== $printer ) {
			$printer_name = $printer->get_display_name();
		}
		if ( isset( $response->success ) && 1 === intval( $response->success ) ) {
			do_action( 'flexible_printing_log', $integration, $printer_name, $title, $response->job->id, '', '' );

			return $response;
		} else {
			do_action( 'flexible_printing_log', $integration, $printer_name, $title, '', $response->message, $response );
			throw new Exception( $response->message );
		}
	}

	/**
	 * Get printer.
	 *
	 * @param string $printer_id .
	 *
	 * @return bool|mixed
	 */
	public function get_printer( $printer_id ) {
		$printers = $this->get_printers();
		foreach ( $printers as $printer ) {
			if ( $printer_id === $printer->id ) {
				return $printer;
			}
		}

		return false;
	}

	/**
	 * Get printers.
	 *
	 * @return array|bool
	 */
	public function get_printers() {
		$transient_name = 'flexible_printing_printers_gcp';
		$printers       = get_transient( $transient_name );
		if ( is_array( $printers ) && 0 === count( $printers ) ) {
			$printers = false;
		}
		if ( ! is_array( $printers ) ) {
			if ( $this->authenticated() ) {
				$print        = new Flexible_Printing_Google_CLoud_Print( $this->get_access_token() );
				$printers_ret = $print->search();
				$printers     = $printers_ret->printers;
				foreach ( $printers as $key => $printer ) {
					$printer_options           = $print->printer( array( 'printerid' => $printer->id ) );
					$printers[ $key ]->options = $printer_options->printers[0];
				}
				set_transient( $transient_name, $printers, WEEK_IN_SECONDS );
			} else {
				$printers = array();
			}
		}

		return $printers;
	}

	/**
	 * Redirect URI.
	 *
	 * @return mixed
	 */
	public function redirect_uri() {
		return admin_url( '?flexible-printing=redirect' );
	}

	/**
	 * Authentication message.
	 */
	public function authenticate_messages() {
		include 'views/authenticate-messages.php';
	}

	/**
	 * Do authenticate.
	 */
	public function do_authenticate() {

		if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) { // phpcs:ignore

			if ( '' !== $this->access_token ) {
				$this->do_revoke();
			}

			$client = new Google_Client();
			$client->setClientId( $this->plugin->flexible_printing->get_option( 'google_client_id', '' ) );
			$client->setRedirectUri( $this->redirect_uri() );
			$client->setScopes( array( 'https://www.googleapis.com/auth/cloudprint' ) );
			$client->setState( 'token' );
			$client->setApprovalPrompt( 'force' );
			$client->setAccessType( 'offline' );
			$url = $client->createAuthUrl();

			wp_redirect( $url ); // phpcs:ignore
			die();
		}
		include 'views/do-authenticate.php';
		$this->plugin->flexible_printing->common_js();
	}

	/**
	 * Do revoke.
	 *
	 * @throws Google_Auth_Exception .
	 */
	public function do_revoke() {
		if ( '' !== $this->access_token && is_object( $this->access_token ) ) {
			$client = new Google_Client();
			$client->revokeToken( $this->access_token->access_token );
			update_option( 'flexible-printing-access-token', '' );
			$this->access_token = '';
		}
	}

	/**
	 * Init.
	 *
	 * @throws Google_Auth_Exception .
	 */
	public function wp_init() {
		if ( current_user_can( 'manage_options' ) ) {
			if ( isset( $_GET['flexible-printing'] ) && 'revoke' === $_GET['flexible-printing'] ) { // phpcs:ignore
				$this->do_revoke();
				wp_redirect( admin_url( 'admin.php?page=flexible-printing-settings&tab=authentication' . '&state=success' ) ); // phpcs:ignore
				die();
			}
			if ( isset( $_GET['page'] ) && 'flexible-printing-settings' === $_GET['page'] // phpcs:ignore
			     && isset( $_GET['tab'] ) && 'printers' === $_GET['tab'] // phpcs:ignore
			     && isset( $_GET['refresh'] ) && 1 === intval( $_GET['refresh'] ) // phpcs:ignore
			) {
				delete_transient( 'flexible_printing_printers_gcp' );
				delete_transient( 'flexible_printing_printers_pn' );
				$url = remove_query_arg( 'refresh' );
				$url = add_query_arg( 'message', urlencode( __( 'Printers refreshed', 'flexible-printing' ) ), $url ); // phpcs:ignore
				wp_redirect( $url ); // phpcs:ignore
				die();
			}
			if ( isset( $_GET['page'] ) && ( 'flexible-printing-settings' === $_GET['page'] ) // phpcs:ignore
			     && isset( $_GET['tab'] ) && 'printers' === $_GET['tab'] // phpcs:ignore
			     && isset( $_GET['reset'] ) && 1 === intval( $_GET['reset'] ) // phpcs:ignore
			     && isset( $_GET['section'] ) // phpcs:ignore
			) {
				$settings = $this->plugin->get_settings()->get_settings();
				foreach ( $settings as $key => $val ) {
					if ( strpos( $key, $_GET['section'] . '_cap_' ) === 0 ) { // phpcs:ignore
						$this->plugin->get_settings()->delete_option( $key );
					}
				}
				$url = remove_query_arg( 'reset' ); // phpcs:ignore
				$url = add_query_arg( 'message', urlencode( __( 'Printer settings reseted.', 'flexible-printing' ) ), $url ); // phpcs:ignore
				wp_redirect( $url ); // phpcs:ignore
				die();
			}
			if ( isset( $_GET['page'] ) && 'flexible-printing-settings' === $_GET['page'] // phpcs:ignore
			     && isset( $_GET['tab'] ) && 'integrations' === $_GET['tab'] // phpcs:ignore
			     && isset( $_GET['reset'] ) && intval( $_GET['reset'] ) === 1 // phpcs:ignore
			     && isset( $_GET['section'] ) // phpcs:ignore
			) {
				$settings = $this->plugin->get_settings()->get_settings();
				foreach ( $settings as $key => $val ) {
					if ( strpos( $key, $_GET['section'] . '_printer_' ) === 0 ) { // phpcs:ignore
						if ( strpos( $key, '_cap_' ) > 0 ) {
							$this->plugin->get_settings()->delete_option( $key );
						}
					}
				}
				$url = remove_query_arg( 'reset' );
				$url = add_query_arg( 'message', urlencode( __( 'Printer settings reseted.', 'flexible-printing' ) ), $url ); // phpcs:ignore
				wp_redirect( $url ); // phpcs:ignore
				die();
			}
			if ( isset( $_GET['flexible-printing'] ) && 'redirect' === $_GET['flexible-printing'] ) { // phpcs:ignore
				if ( isset( $_GET['state'] ) && 'token' === $_GET['state'] ) { // phpcs:ignore
					if ( isset( $_GET['code'] ) ) { // phpcs:ignore
						delete_transient( 'flexible-printing-printers' );
						try {
							$client = new Google_Client();
							$client->setClientId( $this->plugin->flexible_printing->get_option( 'google_client_id', '' ) );
							$client->setClientSecret( $this->plugin->flexible_printing->get_option( 'google_client_secret', '' ) );
							$client->setRedirectUri( $this->redirect_uri() );
							$client->authenticate( sanitize_text_field( $_GET['code'] ) ); // phpcs:ignore
							$access_token               = json_decode( $client->getAccessToken() ); // phpcs:ignore
							$access_token->expires_time = time() + $access_token->expires_in - 60;
							update_option( 'flexible-printing-access-token', $access_token );
							$msg = __( 'Flexible Printing, Google API authentication successful.', 'flexible-printing' );
							wp_redirect( admin_url( 'admin.php?page=flexible-printing-settings&tab=authentication' . '&state=success&message=' . urlencode( $msg ) ) ); // phpcs:ignore
							die();
						} catch ( Exception $e ) {
							$msg = sprintf( __( 'Flexible Printing, Google API authentication: %s.', 'flexible-printing' ), $e->getMessage() ); // phpcs:ignore
							wp_redirect( admin_url( 'admin.php?page=flexible-printing-settings&tab=authentication' . '&error=' . urlencode( $msg ) ) ); // phpcs:ignore
							die();
						}
					}
					if ( isset( $_GET['error'] ) ) { // phpcs:ignore
						$msg = sprintf( __( 'Flexible Printing, Google API authentication: %s.', 'flexible-printing' ), $_GET['error'] ); // phpcs:ignore
						wp_redirect( admin_url( 'admin.php?page=flexible-printing-settings&tab=authentication' . '&error=' . urlencode( $msg ) ) ); // phpcs:ignore
						die();
					}
				}
			}
		}
	}
}
