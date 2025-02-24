<?php

use DpdUKVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can append DPD UK data to tracker data.
 */
class WPDesk_DPD_UK_Tracker implements Hookable {

	public function hooks() {
		add_filter( 'wpdesk_tracker_data', [ $this, 'wpdesk_tracker_data_dpd_uk' ], 11 );
	}

	public function wpdesk_tracker_data_dpd_uk( $data ) {
		$shipping_methods = WC()->shipping()->get_shipping_methods();
		if ( isset( $shipping_methods['dpd_uk'] ) ) {
			$dpd_uk      = $shipping_methods['dpd_uk'];
			$settings    = $dpd_uk->settings;
			$plugin_data = [];
			if ( isset( $settings['auto_create'] ) ) {
				$plugin_data['auto_create'] = $settings['auto_create'];
			}
			if ( isset( $settings['order_status'] ) ) {
				$plugin_data['order_status'] = $settings['order_status'];
			}
			if ( isset( $settings['complete_order'] ) ) {
				$plugin_data['complete_order'] = $settings['complete_order'];
			}
			if ( isset( $settings['label_format'] ) ) {
				$plugin_data['label_format'] = $settings['label_format'];
			}
			if ( isset( $settings['auto_print'] ) ) {
				$plugin_data['auto_print'] = $settings['auto_print'];
			}
			if ( isset( $settings['email_tracking_number'] ) ) {
				$plugin_data['email_tracking_number'] = $settings['email_tracking_number'];
			}

			$all_shipping_methods = flexible_shipping_get_all_shipping_methods();

			$flexible_shipping = $all_shipping_methods['flexible_shipping'];

			$flexible_shipping_rates = $flexible_shipping->get_all_rates();

			$plugin_data['liability_count'] = 0;

			foreach ( $flexible_shipping_rates as $flexible_shipping_rate ) {
				if ( isset( $flexible_shipping_rate['method_integration'] ) && $flexible_shipping_rate['method_integration'] == 'dpd_uk' ) {
					if ( isset( $flexible_shipping_rate['dpd_uk_liability'] ) && $flexible_shipping_rate['dpd_uk_liability'] ) {
						$plugin_data['liability_count'] ++;
					}
				}
			}

			$plugin_data['parcels'] = [];
			$all_parcels            = 0;
			global $wpdb;
			$sql   = "
				SELECT count(p.ID) AS count, p.post_status AS post_status, min(p.post_date) AS min, max(p.post_date) AS max
				FROM {$wpdb->posts} p, {$wpdb->postmeta} m
				WHERE p.post_type = 'shipment'
					AND p.ID = m.post_id
					AND m.meta_key = '_integration'
					AND m.meta_value = 'dpd_uk'
				GROUP BY p.post_status
			";
			$query = $wpdb->get_results( $sql );
			if ( $query ) {
				foreach ( $query as $row ) {
					$plugin_data['parcels'][ $row->post_status ] = $row->count;
					$all_parcels                                 = $all_parcels + $row->count;
				}
			}
			$plugin_data['all_parcels'] = $all_parcels;

			$plugin_data['dpd_uk_service'] = [];
			$sql                           = "
				SELECT count(p.ID) AS count, m2.meta_value AS dpd_uk_service
				FROM {$wpdb->posts} p, {$wpdb->postmeta} m1, {$wpdb->postmeta} m2
				WHERE p.post_type = 'shipment'
					AND p.ID = m2.post_id
					AND m1.meta_key = '_integration'
					AND m1.meta_value = 'dpd_uk'
					AND p.ID = m1.post_id
					AND m2.meta_key = '_dpd_uk_service'
				GROUP BY m2.meta_value
			";
			$query                         = $wpdb->get_results( $sql );
			if ( $query ) {
				foreach ( $query as $row ) {
					$plugin_data['dpd_uk_service'][ $row->dpd_uk_service ] = $row->count;
				}
			}

			$sql   = "
				SELECT TIMESTAMPDIFF(MONTH, min(p.post_date), max(p.post_date) )+1 AS months
				FROM {$wpdb->posts} p, {$wpdb->postmeta} m
				WHERE p.post_type = 'shipment'
					AND p.ID = m.post_id
					AND m.meta_key = '_integration'
					AND m.meta_value = 'dpd_uk'
				GROUP BY p.post_status
			";
			$query = $wpdb->get_results( $sql );
			if ( $query ) {
				foreach ( $query as $row ) {
					if ( $row->months != 0 ) {
						$plugin_data['avg_parcels_per_month'] = floatval( $all_parcels ) / floatval( $row->months );
					}
				}
			}

			$plugin_data['get_request_count'] = intval( get_option( 'woocommerce_dpd_uk_get_request_count', 0 ) );

			$data['dpd_uk'] = $plugin_data;
		}

		return $data;
	}

}
