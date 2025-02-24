<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_WooCommerce_DPD_UK_FS_Hooks' ) ) {

	class WPDesk_WooCommerce_DPD_UK_FS_Hooks {

		private $plugin = null;

		const DPD_UK_CONSOLIDATE = 'dpd_uk_consolidate';

		const ZONE_GB_CODES = array( 'GB', 'IE' );
		const ZONE_EU_CODES = array( 'AT', 'BA', 'BE', 'BG', 'CH', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IS', 'IT', 'LT', 'LU', 'LV', 'MO', 'NL', 'NO', 'PL', 'PT', 'RO', 'RS', 'SE', 'SI', 'SK' );

		public function __construct( WPDesk_WooCommerce_DPD_UK_Plugin $plugin ) {

		    $this->plugin = $plugin;

			add_filter( 'flexible_shipping_integration_options', array( $this, 'flexible_shipping_integration_options' ),  10 );

			add_filter( 'flexible_shipping_method_settings', array( $this, 'flexible_shipping_method_settings' ), 10, 2 );

			add_action( 'flexible_shipping_method_script', array( $this, 'flexible_shipping_method_script' ) );

			add_filter( 'flexible_shipping_process_admin_options', array( $this, 'flexible_shipping_process_admin_options' ), 10, 1 );

			add_filter( 'flexible_shipping_method_integration_col', array( $this, 'flexible_shipping_method_integration_col' ), 10, 2 );

			add_filter( 'flexible_shipping_method_rate_id', array( $this, 'flexible_shipping_method_rate_id' ), 10, 2 );

			add_filter( 'flexible_shipping_add_method', array( $this, 'flexible_shipping_add_method' ), 10, 3 );
		}

		function flexible_shipping_integration_options( $options ) {
			$options['dpd_uk'] = __( 'DPD UK', 'woocommerce-dpd-uk' );
			return $options;
		}

		/**
		 * @param array $shipping_method
		 *
		 * @return WC_Shipping_Zone
		 */
		private function get_zone_for_shipping_method( $shipping_method ) {
			$woocommerce_shipping_zones = WC_Shipping_Zones::get_zones();
			$zone = new WC_Shipping_Zone();
			$woocommerce_method_instance_id = -1;
			if ( isset( $_GET['instance_id'] ) ) {
				$woocommerce_method_instance_id = $_GET['instance_id'];
			}
			if ( isset( $shipping_method['woocommerce_method_instance_id'] ) ) {
				$woocommerce_method_instance_id = $shipping_method['woocommerce_method_instance_id'];
			}
			foreach ( $woocommerce_shipping_zones as $woocommerce_shipping_zone ) {
				foreach ( $woocommerce_shipping_zone['shipping_methods'] as $woocommerce_shipping_method ) {
					if ( $woocommerce_shipping_method->instance_id == $woocommerce_method_instance_id ) {
						$zone = $woocommerce_shipping_zone;
					}
				}
			}
			return $zone;
        }

		/**
         * Get locations from shipping zone.
         *
		 * @param array|WC_Shipping_Zone $zone
		 *
		 * @return array
		 */
        private function get_zone_locations( $zone ) {
		    if ( $zone instanceof WC_Shipping_Zone ) {
                return $zone->get_zone_locations();
            }
            else {
		        return $zone['zone_locations'];
            }
        }

		/**
		 * Services available in Shipping Zone for current API.
		 *
		 * @param WC_Shipping_Zone $zone .
		 *
		 * @return array
		 */
		private function get_services_for_zone( $zone ) {
			$dpd_uk_shipping_method = $this->plugin->get_dpd_uk_shipping_method();
			$api_data               = $dpd_uk_shipping_method->get_api()->get_api_data();
			$zone_gb                = true;
			$zone_eu                = true;

			$zone_locations = $this->get_zone_locations( $zone );
			if ( is_array( $zone_locations ) ) {
				foreach ( $zone_locations as $zone_location ) {
					if ( 'country' === $zone_location->type || 'state' === $zone_location->type ) {
					    $code_exploded = explode( ':', $zone_location->code );
					    $country_code = $code_exploded[0];
						if ( ! in_array( $country_code, self::ZONE_GB_CODES, true ) ) {
							$zone_gb = false;
						}
						if ( ! in_array( $country_code, self::ZONE_EU_CODES, true ) ) {
							$zone_eu = false;
						}
					} elseif ( 'continent' === $zone_location->type ) {
						$zone_gb = false;
						if ( 'EU' !== $zone_location->code ) {
							$zone_eu = false;
						}
					} elseif ( 'postcode' !== $zone_location->type ) {
						$zone_gb = false;
						$zone_eu = false;
					}
				}
			} else {
				$zone_gb = false;
				$zone_eu = false;
			}
			if ( $zone_gb ) {
				$services = $api_data->get_services_for_gb();
			} elseif ( $zone_eu ) {
				$services = $api_data->get_services_for_eu();
			} else {
				$services = $api_data->get_services_for_world();
			}
			return $services;
		}

		/**
		 * @return array
		 */
		private function get_ship_to_shop_services() {
			$api_data = $this->plugin->get_dpd_uk_shipping_method()->get_api()->get_api_data();

			return $api_data->get_ship_to_shop_services();
		}

		/**
		 * @return bool
		 */
		private function is_ship_to_shop_available() {
			return (bool) is_plugin_active( 'flexible-shipping-pickup-points/flexible-shipping-pickup-points.php' );
		}

		/**
		 * @param array $flexible_shipping_settings
		 * @param array $shipping_method
		 *
		 * @return array
		 */
		public function flexible_shipping_method_settings( $flexible_shipping_settings, $shipping_method ) {

			$zone = $this->get_zone_for_shipping_method( $shipping_method );

			$shipping_methods = WC()->shipping->get_shipping_methods();
			if ( $shipping_methods['dpd_uk']->enabled == 'yes' ) { // always available
				$liability_custom_attributes = array();
				if ( isset( $shipping_method['dpd_uk_liability'] ) && $shipping_method['dpd_uk_liability'] == '1' ) {
					$liability_custom_attributes = array( 'checked' => 'checked' );
				}

				$consolidate_custom_attributes = array( 'checked' => 'checked' );
				if ( isset( $shipping_method[ self::DPD_UK_CONSOLIDATE ] ) && (int) $shipping_method[ self::DPD_UK_CONSOLIDATE ] === 0 ) {
					$consolidate_custom_attributes = array( 'unchecked' => 'unchecked' );
				}

				$services = $this->get_services_for_zone( $zone );

				$settings = array(
					'dpd_uk_service'            => array(
						'title'       => __( 'Service', 'woocommerce-dpd-uk' ),
						'type'        => 'select',
						'description' => sprintf(
							__( 'Please mind that sending the shipments via DPD UK Ship to Shop service requires the %1$sDPD UK Pickup Points WooCommerce plugin%2$s to be installed and active as well.', 'woocommerce-dpd-uk' ),
							'<a href="https://octolize.com/product/dpd-uk-dpd-local-pickup-points-woocommerce/?utm_source=dpd-uk&utm_medium=shipping-method&utm_campaign=dpduk-pp" target="_blank">',
							'</a>'
						),
						'desc_tip'    => __( 'List of DPD services available in this shipping zone.', 'woocommerce-dpd-uk' ),
						'default'     => isset( $shipping_method['dpd_uk_service'] ) ? $shipping_method['dpd_uk_service'] : '',
						'options'     => $services,
					),
					'dpd_uk_liability'          => array(
						'title'             => __( 'Liability', 'woocommerce-dpd-uk' ),
						'label'             => __( 'Check to add extended liability', 'woocommerce-dpd-uk' ),
						'type'              => 'checkbox',
						'custom_attributes' => $liability_custom_attributes,
						'description'       => __( 'Liability amount will automatically filled with order total.', 'woocommerce-dpd-uk' ),
						'desc_tip'          => true,
					),
					self::DPD_UK_CONSOLIDATE    => array(
						'title'             => __( 'Consolidation', 'woocommerce-dpd-uk' ),
						'label'             => __( 'Check to consolidate parcels', 'woocommerce-dpd-uk' ),
						'type'              => 'checkbox',
						'custom_attributes' => $consolidate_custom_attributes,
						'description'       => __( 'Check this option if you want the parcels will consolidate together if being delivered to the same address.', 'woocommerce-dpd-uk' ),
						'desc_tip'          => true,
					),
					'dpd_uk_reference1'         => array(
						'title'       => __( 'Reference 1', 'woocommerce-dpd-uk' ),
						'type'        => 'text',
						'default'     => isset( $shipping_method['dpd_uk_reference1'] ) ? $shipping_method['dpd_uk_reference1'] : '',
						'description' => __( 'Notes on the label. Maximum of 25 characters.', 'woocommerce-dpd-uk' ),
					),
					'dpd_uk_reference2'         => array(
						'title'       => __( 'Reference 2', 'woocommerce-dpd-uk' ),
						'type'        => 'text',
						'default'     => isset( $shipping_method['dpd_uk_reference2'] ) ? $shipping_method['dpd_uk_reference2'] : '',
						'description' => __( 'Notes on the label. Maximum of 25 characters.', 'woocommerce-dpd-uk' ),
						'desc_tip'    => true,
					),
					'dpd_uk_reference3'         => array(
						'title'       => __( 'Reference 3', 'woocommerce-dpd-uk' ),
						'type'        => 'text',
						'default'     => isset( $shipping_method['dpd_uk_reference3'] ) ? $shipping_method['dpd_uk_reference3'] : '',
						'description' => __( 'Notes on the label. Maximum of 25 characters.', 'woocommerce-dpd-uk' ),
						'desc_tip'    => true,
					),
					'dpd_uk_parcel_description' => array(
						'title'       => __( 'Parcel description', 'woocommerce-dpd-uk' ),
						'type'        => 'text',
						'default'     => isset( $shipping_method['dpd_uk_parcel_description'] ) ? $shipping_method['dpd_uk_parcel_description'] : '',
						'description' => __( 'DPD Europe by Road service requires filling the Parcel Description field.', 'woocommerce-dpd-uk' ),
						'desc_tip'    => true,
					),
				);

				return array_merge( $flexible_shipping_settings, $settings );
			}

			return $flexible_shipping_settings;
		}

		public function flexible_shipping_method_script() {
			?>
				<script type="text/javascript">
					jQuery(document).ready(function() {
						let ship_to_shop_services = <?php echo json_encode( $this->get_ship_to_shop_services() ); ?>;
						let ship_to_shop_available = <?php echo json_encode( $this->is_ship_to_shop_available() ); ?>;
						let $woocommerce_flexible_shipping_method_integration = jQuery('#woocommerce_flexible_shipping_method_integration');
						let $woocommerce_flexible_shipping_dpd_uk_service = jQuery('#woocommerce_flexible_shipping_dpd_uk_service');
						let $woocommerce_flexible_shipping_map_status = jQuery('#woocommerce_flexible_shipping_map_status');
						let $woocommerce_flexible_shipping_map_providers = jQuery('#woocommerce_flexible_shipping_map_providers');

						function dpdUkOptions() {
							let is_dpd_uk = $woocommerce_flexible_shipping_method_integration.val() === 'dpd_uk';
							$woocommerce_flexible_shipping_dpd_uk_service.closest('tr').toggle( is_dpd_uk );
                            jQuery('#woocommerce_flexible_shipping_dpd_uk_liability').closest('tr').toggle( is_dpd_uk );
                            jQuery('#woocommerce_flexible_shipping_dpd_uk_consolidate').closest('tr').toggle( is_dpd_uk );
                            jQuery('#woocommerce_flexible_shipping_dpd_uk_reference1').closest('tr').toggle( is_dpd_uk );
                            jQuery('#woocommerce_flexible_shipping_dpd_uk_reference2').closest('tr').toggle( is_dpd_uk );
                            jQuery('#woocommerce_flexible_shipping_dpd_uk_reference3').closest('tr').toggle( is_dpd_uk );
                            jQuery('#woocommerce_flexible_shipping_dpd_uk_parcel_description').closest('tr').toggle( is_dpd_uk );

							$woocommerce_flexible_shipping_dpd_uk_service.parent().find('p').toggle(
								!ship_to_shop_available && ship_to_shop_services.includes($woocommerce_flexible_shipping_dpd_uk_service.val())
							);

							if ( is_dpd_uk ) {
								if ( ship_to_shop_services.includes( $woocommerce_flexible_shipping_dpd_uk_service.val() ) ) {
									$woocommerce_flexible_shipping_map_status.prop('checked',true);
									$woocommerce_flexible_shipping_map_status.change();
									jQuery('#woocommerce_flexible_shipping_map_providers option').each(function(){
										jQuery(this).attr('selected', jQuery(this).attr('value') === 'dpd');
									});
									$woocommerce_flexible_shipping_map_providers.change();
								} else {
									$woocommerce_flexible_shipping_map_status.prop('checked',false);
									$woocommerce_flexible_shipping_map_status.change();
								}
								$woocommerce_flexible_shipping_map_status.prop('disabled',true);
								$woocommerce_flexible_shipping_map_providers.prop('disabled',true);
							}
						}
						$woocommerce_flexible_shipping_method_integration.change(function() {
                            dpdUkOptions();
						});
                        $woocommerce_flexible_shipping_dpd_uk_service.change(function() {
                            dpdUkOptions();
                        });
						$woocommerce_flexible_shipping_method_integration.on('flexible-shipping-pickup-points:integration-changed',function() {
							dpdUkOptions();
						});
						dpdUkOptions();
						jQuery('#mainform').submit(function() {
							$woocommerce_flexible_shipping_map_status.prop('disabled', false);
							$woocommerce_flexible_shipping_map_providers.prop('disabled', false);
						});
					});
				</script>
			<?php
		}

		public function flexible_shipping_process_admin_options( $shipping_method )	{
			$shipping_method['dpd_uk_service'] = $_POST['woocommerce_flexible_shipping_dpd_uk_service'];

			$shipping_method['dpd_uk_reference1'] = $_POST['woocommerce_flexible_shipping_dpd_uk_reference1'];
			$shipping_method['dpd_uk_reference2'] = $_POST['woocommerce_flexible_shipping_dpd_uk_reference2'];
			$shipping_method['dpd_uk_reference3'] = $_POST['woocommerce_flexible_shipping_dpd_uk_reference3'];

			$shipping_method['dpd_uk_parcel_description'] = $_POST['woocommerce_flexible_shipping_dpd_uk_parcel_description'];

			$shipping_method['dpd_uk_liability'] = 0;
			if ( isset( $_POST['woocommerce_flexible_shipping_dpd_uk_liability'] ) ) {
				$shipping_method['dpd_uk_liability'] = $_POST['woocommerce_flexible_shipping_dpd_uk_liability'];
			}

			$shipping_method[ self::DPD_UK_CONSOLIDATE ] = 0;
			if ( isset( $_POST['woocommerce_flexible_shipping_'.self::DPD_UK_CONSOLIDATE] ) ) {
				$shipping_method[ self::DPD_UK_CONSOLIDATE ] = $_POST['woocommerce_flexible_shipping_'.self::DPD_UK_CONSOLIDATE];
			}

			return $shipping_method;
		}

		public function flexible_shipping_method_integration_col( $col, $shipping_method ) {
			$shipping_methods = WC()->shipping->get_shipping_methods();
			if ( $shipping_methods['dpd_uk']->enabled == 'yes' ) {
				if ( isset( $shipping_method['method_integration'] ) && 'dpd_uk' === $shipping_method['method_integration'] ) {
					ob_start();
					$tip = __( 'None', 'woocommerce-dpd-uk' );
					?>
					<td width="1%" class="integration default">
						<span class="tips" data-tip="<?php echo $tip; ?>">
							<?php echo $shipping_methods['dpd_uk']->title; ?>
						</span>
					</td>
					<?php
					$col = ob_get_contents();
					ob_end_clean();
				}
			}
			return $col;
		}

		public function flexible_shipping_method_rate_id( $rate_id, $shipping_method ) {
			if ( isset( $shipping_method['method_integration'] ) && 'dpd_uk' === $shipping_method['method_integration'] ) {
				$rate_id = $rate_id . '_dpd_uk_' . sanitize_title( $shipping_method['dpd_uk_service'] );
				if ( isset( $shipping_method['dpd_uk_liability'] ) ) {
					$rate_id .=  '_' . $shipping_method['dpd_uk_liability'];
				}
				if ( isset( $shipping_method[ self::DPD_UK_CONSOLIDATE ] ) ) {
					$rate_id .=  '_' . $shipping_method[ self::DPD_UK_CONSOLIDATE ];
				}
			}
			return $rate_id;
		}

		public function flexible_shipping_add_method( $add_method, $shipping_method, $package )	{
			if ( isset( $shipping_method['method_integration'] ) && 'dpd_uk' === $shipping_method['method_integration']
				&& isset( $shipping_method['dpd_product'] )
			) {
			}
			return $add_method;
		}

	}

}
