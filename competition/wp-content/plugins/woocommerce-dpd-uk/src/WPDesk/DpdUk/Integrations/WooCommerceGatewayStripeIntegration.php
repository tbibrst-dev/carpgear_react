<?php

namespace WPDesk\DpdUk\Integrations;

use DpdUKVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Integrates with WooCommerce Gateway Stripe
 *
 * @see https://pl.wordpress.org/plugins/woocommerce-gateway-stripe/
 *
 * Disables service verification for UK and CA and Apple Pay and Google Pay.
 * Apple Pay and Google Pay sends only 3 characters for postcode on payment.
 */
class WooCommerceGatewayStripeIntegration implements Hookable {

	/**
	 * @var \WPDesk_WooCommerce_DPD_UK_Services_Verifier
	 */
	private $services_verifier;

	/**
	 * @param \WPDesk_WooCommerce_DPD_UK_Services_Verifier $services_verifier .
	 */
	public function __construct( \WPDesk_WooCommerce_DPD_UK_Services_Verifier $services_verifier ) {
		$this->services_verifier = $services_verifier;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wc_stripe_payment_request_shipping_posted_values', [ $this, 'disable_services_verifier' ] );
	}

	/**
	 * @param array $address
	 *
	 * @return array
	 */
	public function disable_services_verifier( $address ) {
		$country = $address['country'] ?? '';

		if ( in_array( $country, [ 'GB', 'CA' ], true ) ) {
			remove_filter( 'flexible_shipping_add_method', [ $this->services_verifier, 'verify_service' ], 10 );
		}

		return $address;
	}

}
