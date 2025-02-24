<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    DSCPW_Conditional_Payments
 * @subpackage DSCPW_Conditional_Payments/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    DSCPW_Conditional_Payments
 * @subpackage DSCPW_Conditional_Payments/public
 * @author     Sahil Multani
 */
if ( !class_exists( 'DSCPW_Conditional_Payments_Public' ) ) {
    class DSCPW_Conditional_Payments_Public {
        private static $admin_object = null;

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string $plugin_name The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string $version The current version of this plugin.
         */
        private $version;

        /**
         * Initialize the class and set its properties.
         *
         * @param string $plugin_name The name of the plugin.
         * @param string $version     The version of this plugin.
         *
         * @since    1.0.0
         */
        public function __construct( $plugin_name, $version ) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            self::$admin_object = new DSCPW_Conditional_Payments_Admin($plugin_name, $version);
        }

        /**
         * Register the scripts for the public area.
         *
         * @since    1.0.0
         *
         */
        public function dscpw_enqueue_public_scripts() {
            wp_enqueue_script(
                $this->plugin_name . 'public-js',
                plugin_dir_url( __FILE__ ) . 'js/conditional-payments-public.js',
                array(),
                $this->version,
                true
            );
            wp_localize_script( $this->plugin_name . 'public-js', 'dscpw_conditional_payments_settings', array(
                'name_address_fields' => $this->dscpw_name_address_fields(),
                'ajaxurl'             => admin_url( 'admin-ajax.php' ),
            ) );
        }

        /**
         * Match condition based on rule list
         *
         * @param array $cp_post_data
         *
         * @return bool True if $final_condition_flag is 1, false otherwise. if $cp_status is off then also return false.
         * @since 1.0.0
         *
         * @uses  DSCPW_Conditional_Payments_Admin::dscpw_get_default_language_with_sitepress()
         * @uses  dscpw_get_woo_version_number()
         * @uses  WC_Cart::get_cart()
         *
         */
        public function dscpw_condition_match_rules( $cp_post_data = array() ) {
            if ( is_admin() ) {
                return;
            }
            global $sitepress;
            $default_lang = self::$admin_object->dscpw_get_default_language_with_sitepress();
            $wc_curr_version = $this->dscpw_get_woo_version_number();
            $is_passed = array();
            $final_condition_flag = array();
            $cart_product_ids_array = array();
            if ( empty( WC()->cart ) ) {
                $cart_array = array();
            } else {
                $cart_array = WC()->cart->get_cart();
            }
            $cart_product = $this->dscpw_fee_array_column_public( $cart_array, 'product_id' );
            if ( isset( $cart_product ) && !empty( $cart_product ) ) {
                foreach ( $cart_product as $cart_product_id ) {
                    $product_cart_array = new WC_Product($cart_product_id);
                    if ( $product_cart_array->is_type( 'simple' ) ) {
                        if ( !empty( $sitepress ) ) {
                            $cart_product_ids_array[] = apply_filters(
                                'wpml_object_id',
                                $cart_product_id,
                                'product',
                                true,
                                $default_lang
                            );
                        } else {
                            $cart_product_ids_array[] = $cart_product_id;
                        }
                    }
                }
            }
            $cart_variation_ids_array = array();
            $variation_cart_product = $this->dscpw_fee_array_column_public( $cart_array, 'variation_id' );
            if ( isset( $variation_cart_product ) && !empty( $variation_cart_product ) ) {
                foreach ( $variation_cart_product as $cart_variation_id ) {
                    if ( 0 !== $cart_variation_id ) {
                        $product_cart_array = wc_get_product( $cart_variation_id );
                        if ( !empty( $sitepress ) ) {
                            $cart_variation_ids_array[] = apply_filters(
                                'wpml_object_id',
                                $cart_variation_id,
                                'product',
                                true,
                                $default_lang
                            );
                        } else {
                            $cart_variation_ids_array[] = $cart_variation_id;
                        }
                    }
                }
            }
            $cp_status = get_post_status( $cp_post_data->ID );
            if ( isset( $cp_status ) && 'draft' === $cp_status ) {
                return false;
            }
            $get_condition_array = get_post_meta( $cp_post_data->ID, 'cp_metabox', true );
            $general_rule_match = 'all';
            if ( !empty( $get_condition_array ) ) {
                $product_array = array();
                $variable_product_array = array();
                $cart_total_array = array();
                $cart_total_after_array = array();
                $shipping_method_array = array();
                $billing_firstname_array = array();
                $billing_lastname_array = array();
                $billing_company_array = array();
                $billing_address_1_array = array();
                $billing_address_2_array = array();
                $billing_country_array = array();
                $billing_city_array = array();
                $billing_postcode_array = array();
                $shipping_firstname_array = array();
                $shipping_lastname_array = array();
                $shipping_company_array = array();
                $shipping_address_1_array = array();
                $shipping_address_2_array = array();
                $shipping_country_array = array();
                $shipping_city_array = array();
                $shipping_postcode_array = array();
                $day_of_week_array = array();
                $date_array = array();
                foreach ( $get_condition_array as $key => $value ) {
                    if ( array_search( 'product', $value, true ) ) {
                        $product_array[$key] = $value;
                    }
                    if ( array_search( 'variable_product', $value, true ) ) {
                        $variable_product_array[$key] = $value;
                    }
                    if ( array_search( 'cart_total', $value, true ) ) {
                        $cart_total_array[$key] = $value;
                    }
                    if ( array_search( 'cart_totalafter', $value, true ) ) {
                        $cart_total_after_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_method', $value, true ) ) {
                        $shipping_method_array[$key] = $value;
                    }
                    if ( array_search( 'billing_first_name', $value, true ) ) {
                        $billing_firstname_array[$key] = $value;
                    }
                    if ( array_search( 'billing_last_name', $value, true ) ) {
                        $billing_lastname_array[$key] = $value;
                    }
                    if ( array_search( 'billing_company', $value, true ) ) {
                        $billing_company_array[$key] = $value;
                    }
                    if ( array_search( 'billing_address_1', $value, true ) ) {
                        $billing_address_1_array[$key] = $value;
                    }
                    if ( array_search( 'billing_address_2', $value, true ) ) {
                        $billing_address_2_array[$key] = $value;
                    }
                    if ( array_search( 'billing_country', $value, true ) ) {
                        $billing_country_array[$key] = $value;
                    }
                    if ( array_search( 'billing_city', $value, true ) ) {
                        $billing_city_array[$key] = $value;
                    }
                    if ( array_search( 'billing_postcode', $value, true ) ) {
                        $billing_postcode_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_first_name', $value, true ) ) {
                        $shipping_firstname_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_last_name', $value, true ) ) {
                        $shipping_lastname_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_company', $value, true ) ) {
                        $shipping_company_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_address_1', $value, true ) ) {
                        $shipping_address_1_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_address_2', $value, true ) ) {
                        $shipping_address_2_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_country', $value, true ) ) {
                        $shipping_country_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_city', $value, true ) ) {
                        $shipping_city_array[$key] = $value;
                    }
                    if ( array_search( 'shipping_postcode', $value, true ) ) {
                        $shipping_postcode_array[$key] = $value;
                    }
                    if ( array_search( 'day_of_week', $value, true ) ) {
                        $day_of_week_array[$key] = $value;
                    }
                    if ( array_search( 'date', $value, true ) ) {
                        $date_array[$key] = $value;
                    }
                    //Check if is product exist
                    if ( is_array( $product_array ) && isset( $product_array ) && !empty( $product_array ) && !empty( $cart_product_ids_array ) ) {
                        $product_passed = $this->dscpw_match_simple_products_rule( $cart_product_ids_array, $product_array, $general_rule_match );
                        if ( 'yes' === $product_passed ) {
                            $is_passed['has_condition_based_on_product'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_product'] = 'no';
                        }
                    }
                    //Check if is variable product exist
                    if ( is_array( $variable_product_array ) && isset( $variable_product_array ) && !empty( $variable_product_array ) && !empty( $cart_product_ids_array ) ) {
                        $variable_prd_passed = $this->dscpw_match_variable_products_rule( $cart_variation_ids_array, $variable_product_array, $general_rule_match );
                        if ( 'yes' === $variable_prd_passed ) {
                            $is_passed['has_condition_based_on_var_product'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_var_product'] = 'no';
                        }
                    }
                    //Check if is Cart Subtotal (Before Discount) exist
                    if ( is_array( $cart_total_array ) && isset( $cart_total_array ) && !empty( $cart_total_array ) && !empty( $cart_product_ids_array ) ) {
                        $cart_total_before_passed = $this->dscpw_match_cart_subtotal_before_discount_rule( $wc_curr_version, $cart_total_array, $general_rule_match );
                        if ( 'yes' === $cart_total_before_passed ) {
                            $is_passed['has_condition_based_on_cart_total'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_cart_total'] = 'no';
                        }
                    }
                    //Check if is Cart Subtotal (After Discount) exist
                    if ( is_array( $cart_total_after_array ) && isset( $cart_total_after_array ) && !empty( $cart_total_after_array ) && !empty( $cart_product_ids_array ) ) {
                        $cart_total_after_passed = $this->dscpw_match_cart_subtotal_after_discount_rule( $wc_curr_version, $cart_total_after_array, $general_rule_match );
                        if ( 'yes' === $cart_total_after_passed ) {
                            $is_passed['has_condition_based_on_cart_total_after'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_cart_total_after'] = 'no';
                        }
                    }
                    //Check if is shipping method exist
                    if ( is_array( $shipping_method_array ) && isset( $shipping_method_array ) && !empty( $shipping_method_array ) && !empty( $cart_array ) ) {
                        $shipping_method_passed = $this->dscpw_match_shipping_method_rule( $wc_curr_version, $shipping_method_array, $general_rule_match );
                        if ( 'yes' === $shipping_method_passed ) {
                            $is_passed['has_condition_based_on_shipping_method'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_method'] = 'no';
                        }
                    }
                    //Check if is billing firstname exist
                    if ( is_array( $billing_firstname_array ) && isset( $billing_firstname_array ) && !empty( $billing_firstname_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_firstname_passed = $this->dscpw_match_billing_firstname_rules( 'billing_first_name', $billing_firstname_array, $general_rule_match );
                        if ( 'yes' === $billing_firstname_passed ) {
                            $is_passed['has_condition_based_on_billing_firstname'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_firstname'] = 'no';
                        }
                    }
                    //Check if is billing lastname exist
                    if ( is_array( $billing_lastname_array ) && isset( $billing_lastname_array ) && !empty( $billing_lastname_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_lastname_passed = $this->dscpw_match_billing_lastname_rules( 'billing_last_name', $billing_lastname_array, $general_rule_match );
                        if ( 'yes' === $billing_lastname_passed ) {
                            $is_passed['has_condition_based_on_billing_lastname'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_lastname'] = 'no';
                        }
                    }
                    //Check if is billing company exist
                    if ( is_array( $billing_company_array ) && isset( $billing_company_array ) && !empty( $billing_company_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_company_passed = $this->dscpw_match_billing_company_rules( 'billing_company', $billing_company_array, $general_rule_match );
                        if ( 'yes' === $billing_company_passed ) {
                            $is_passed['has_condition_based_on_billing_company'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_company'] = 'no';
                        }
                    }
                    //Check if is billing address 1 exist
                    if ( is_array( $billing_address_1_array ) && isset( $billing_address_1_array ) && !empty( $billing_address_1_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_address_1_passed = $this->dscpw_match_billing_address_1_rules( 'billing_address_1', $billing_address_1_array, $general_rule_match );
                        if ( 'yes' === $billing_address_1_passed ) {
                            $is_passed['has_condition_based_on_billing_address_1'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_address_1'] = 'no';
                        }
                    }
                    //Check if is billing address 2 exist
                    if ( is_array( $billing_address_2_array ) && isset( $billing_address_2_array ) && !empty( $billing_address_2_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_address_2_passed = $this->dscpw_match_billing_address_2_rules( 'billing_address_2', $billing_address_2_array, $general_rule_match );
                        if ( 'yes' === $billing_address_2_passed ) {
                            $is_passed['has_condition_based_on_billing_address_2'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_address_2'] = 'no';
                        }
                    }
                    //Check if is billing country exist
                    if ( is_array( $billing_country_array ) && isset( $billing_country_array ) && !empty( $billing_country_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_country_passed = $this->dscpw_match_billing_country_rules( $billing_country_array, $general_rule_match );
                        if ( 'yes' === $billing_country_passed ) {
                            $is_passed['has_condition_based_on_billing_country'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_country'] = 'no';
                        }
                    }
                    //Check if is billing city exist
                    if ( is_array( $billing_city_array ) && isset( $billing_city_array ) && !empty( $billing_city_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_city_passed = $this->dscpw_match_billing_city_rules( 'billing_city', $billing_city_array, $general_rule_match );
                        if ( 'yes' === $billing_city_passed ) {
                            $is_passed['has_condition_based_on_billing_city'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_city'] = 'no';
                        }
                    }
                    //Check if is billing postcode exist
                    if ( is_array( $billing_postcode_array ) && isset( $billing_postcode_array ) && !empty( $billing_postcode_array ) && !empty( $cart_product_ids_array ) ) {
                        $billing_postcode_passed = $this->dscpw_match_billing_postcode_rules( $billing_postcode_array, $general_rule_match );
                        if ( 'yes' === $billing_postcode_passed ) {
                            $is_passed['has_condition_based_on_billing_postcode'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_billing_postcode'] = 'no';
                        }
                    }
                    //Check if is shipping firstname exist
                    if ( is_array( $shipping_firstname_array ) && isset( $shipping_firstname_array ) && !empty( $shipping_firstname_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_firstname_passed = $this->dscpw_match_shipping_firstname_rules( 'shipping_first_name', $shipping_firstname_array, $general_rule_match );
                        if ( 'yes' === $shipping_firstname_passed ) {
                            $is_passed['has_condition_based_on_shipping_firstname'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_firstname'] = 'no';
                        }
                    }
                    //Check if is shipping lastname exist
                    if ( is_array( $shipping_lastname_array ) && isset( $shipping_lastname_array ) && !empty( $shipping_lastname_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_lastname_passed = $this->dscpw_match_shipping_lastname_rules( 'shipping_last_name', $shipping_lastname_array, $general_rule_match );
                        if ( 'yes' === $shipping_lastname_passed ) {
                            $is_passed['has_condition_based_on_shipping_lastname'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_lastname'] = 'no';
                        }
                    }
                    //Check if is shipping company exist
                    if ( is_array( $shipping_company_array ) && isset( $shipping_company_array ) && !empty( $shipping_company_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_company_passed = $this->dscpw_match_shipping_company_rules( 'shipping_company', $shipping_company_array, $general_rule_match );
                        if ( 'yes' === $shipping_company_passed ) {
                            $is_passed['has_condition_based_on_shipping_company'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_company'] = 'no';
                        }
                    }
                    //Check if is shipping address 1 exist
                    if ( is_array( $shipping_address_1_array ) && isset( $shipping_address_1_array ) && !empty( $shipping_address_1_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_address_1_passed = $this->dscpw_match_shipping_address_1_rules( 'shipping_address_1', $shipping_address_1_array, $general_rule_match );
                        if ( 'yes' === $shipping_address_1_passed ) {
                            $is_passed['has_condition_based_on_shipping_address_1'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_address_1'] = 'no';
                        }
                    }
                    //Check if is shipping address 2 exist
                    if ( is_array( $shipping_address_2_array ) && isset( $shipping_address_2_array ) && !empty( $shipping_address_2_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_address_2_passed = $this->dscpw_match_shipping_address_2_rules( 'shipping_address_2', $shipping_address_2_array, $general_rule_match );
                        if ( 'yes' === $shipping_address_2_passed ) {
                            $is_passed['has_condition_based_on_shipping_address_2'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_address_2'] = 'no';
                        }
                    }
                    //Check if is shipping country exist
                    if ( is_array( $shipping_country_array ) && isset( $shipping_country_array ) && !empty( $shipping_country_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_country_passed = $this->dscpw_match_shipping_country_rules( $shipping_country_array, $general_rule_match );
                        if ( 'yes' === $shipping_country_passed ) {
                            $is_passed['has_condition_based_on_shipping_country'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_country'] = 'no';
                        }
                    }
                    //Check if is shipping city exist
                    if ( is_array( $shipping_city_array ) && isset( $shipping_city_array ) && !empty( $shipping_city_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_city_passed = $this->dscpw_match_shipping_city_rules( 'shipping_city', $shipping_city_array, $general_rule_match );
                        if ( 'yes' === $shipping_city_passed ) {
                            $is_passed['has_condition_based_on_shipping_city'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_city'] = 'no';
                        }
                    }
                    //Check if is shipping postcode exist
                    if ( is_array( $shipping_postcode_array ) && isset( $shipping_postcode_array ) && !empty( $shipping_postcode_array ) && !empty( $cart_product_ids_array ) ) {
                        $shipping_postcode_passed = $this->dscpw_match_shipping_postcode_rules( $shipping_postcode_array, $general_rule_match );
                        if ( 'yes' === $shipping_postcode_passed ) {
                            $is_passed['has_condition_based_on_shipping_postcode'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_shipping_postcode'] = 'no';
                        }
                    }
                    //Check if is day of week exist
                    if ( is_array( $day_of_week_array ) && isset( $day_of_week_array ) && !empty( $day_of_week_array ) && !empty( $cart_product_ids_array ) ) {
                        $day_of_week_passed = $this->dscpw_match_day_of_week_rules( $day_of_week_array, $general_rule_match );
                        if ( 'yes' === $day_of_week_passed ) {
                            $is_passed['has_condition_based_on_day_of_week'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_day_of_week'] = 'no';
                        }
                    }
                    //Check if is date exist
                    if ( is_array( $date_array ) && isset( $date_array ) && !empty( $date_array ) && !empty( $cart_product_ids_array ) ) {
                        $date_passed = $this->dscpw_match_date_rules( $date_array, $general_rule_match );
                        if ( 'yes' === $date_passed ) {
                            $is_passed['has_condition_based_on_date'] = 'yes';
                        } else {
                            $is_passed['has_condition_based_on_date'] = 'no';
                        }
                    }
                }
            }
            if ( isset( $is_passed ) && !empty( $is_passed ) && is_array( $is_passed ) ) {
                if ( !in_array( 'no', $is_passed, true ) ) {
                    $final_condition_flag['passed'] = 'yes';
                }
            }
            if ( empty( $final_condition_flag ) && $final_condition_flag === '' ) {
                return false;
            } else {
                if ( !empty( $final_condition_flag ) && in_array( 'no', $final_condition_flag, true ) ) {
                    return false;
                } else {
                    if ( empty( $final_condition_flag ) && in_array( '', $final_condition_flag, true ) ) {
                        return false;
                    } else {
                        if ( !empty( $final_condition_flag ) && in_array( 'yes', $final_condition_flag, true ) ) {
                            return true;
                        }
                    }
                }
            }
        }

        /**
         * Store customer details to the session for being used in filters
         * 
         * @since    1.0.0
         */
        public function dscpw_store_customer_details( $post_data ) {
            $data = array();
            parse_str( $post_data, $data );
            $attrs = array(
                'billing_first_name',
                'billing_last_name',
                'billing_company',
                'shipping_first_name',
                'shipping_last_name',
                'shipping_company',
                'billing_email'
            );
            $same_addr = FALSE;
            if ( !isset( $data['ship_to_different_address'] ) || $data['ship_to_different_address'] !== '1' ) {
                $same_addr = TRUE;
                $attrs = array(
                    'billing_first_name',
                    'billing_last_name',
                    'billing_company',
                    'billing_email'
                );
            }
            foreach ( $attrs as $attr ) {
                WC()->customer->set_props( array(
                    $attr => ( isset( $data[$attr] ) ? wp_unslash( $data[$attr] ) : null ),
                ) );
                if ( $same_addr ) {
                    $attr2 = str_replace( 'billing', 'shipping', $attr );
                    WC()->customer->set_props( array(
                        $attr2 => ( isset( $data[$attr] ) ? wp_unslash( $data[$attr] ) : null ),
                    ) );
                }
            }
        }

        /**
         * Get order attribute
         * 
         * @since    1.0.0
         */
        public static function dscpw_get_order_attr( $attr ) {
            $order_id = absint( get_query_var( 'order-pay' ) );
            // Gets attribute from "pay for order" page.
            if ( 0 < $order_id ) {
                $order = wc_get_order( $order_id );
                return call_user_func( array($order, "get_{$attr}") );
            } elseif ( WC()->cart ) {
                return call_user_func( array(WC()->customer, "get_{$attr}") );
            }
            return NULL;
        }

        /**
         * Match simple products rules
         *
         * @param array $cart_product_ids_array
         * @param array $product_array
         * @param string  $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_simple_products_rule( $cart_product_ids_array, $product_array, $general_rule_match ) {
            $is_passed = array();
            foreach ( $product_array as $key => $product ) {
                if ( 'is_equal_to' === $product['payments_conditions_is'] ) {
                    if ( !empty( $product['payment_conditions_values'] ) ) {
                        foreach ( $product['payment_conditions_values'] as $product_id ) {
                            settype( $product_id, 'integer' );
                            if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
                                $is_passed[$key]['has_condition_based_on_product'] = 'yes';
                                break;
                            } else {
                                $is_passed[$key]['has_condition_based_on_product'] = 'no';
                            }
                        }
                    }
                }
                if ( 'not_in' === $product['payments_conditions_is'] ) {
                    if ( !empty( $product['payment_conditions_values'] ) ) {
                        foreach ( $product['payment_conditions_values'] as $product_id ) {
                            settype( $product_id, 'integer' );
                            if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
                                $is_passed[$key]['has_condition_based_on_product'] = 'no';
                                break;
                            } else {
                                $is_passed[$key]['has_condition_based_on_product'] = 'yes';
                            }
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_product', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match variable products rules
         *
         * @param array $cart_product_ids_array
         * @param array $variableproduct_array
         * @param string  $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_variable_products_rule( $cart_product_ids_array, $variableproduct_array, $general_rule_match ) {
            $is_passed = array();
            foreach ( $variableproduct_array as $key => $product ) {
                if ( 'is_equal_to' === $product['payments_conditions_is'] ) {
                    if ( !empty( $product['payment_conditions_values'] ) ) {
                        foreach ( $product['payment_conditions_values'] as $product_id ) {
                            settype( $product_id, 'integer' );
                            if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
                                $is_passed[$key]['has_condition_based_on_var_product'] = 'yes';
                                break;
                            } else {
                                $is_passed[$key]['has_condition_based_on_var_product'] = 'no';
                            }
                        }
                    }
                }
                if ( 'not_in' === $product['payments_conditions_is'] ) {
                    if ( !empty( $product['payment_conditions_values'] ) ) {
                        foreach ( $product['payment_conditions_values'] as $product_id ) {
                            settype( $product_id, 'integer' );
                            if ( in_array( $product_id, $cart_product_ids_array, true ) ) {
                                $is_passed[$key]['has_condition_based_on_var_product'] = 'no';
                                break;
                            } else {
                                $is_passed[$key]['has_condition_based_on_var_product'] = 'yes';
                            }
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_var_product', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match rule based on cart subtotal before discount
         *
         * @param string $wc_curr_version
         * @param array  $cart_total_array
         * @param string  $general_rule_match
         *
         * @return array $is_passed
         *
         * @uses     WC_Cart::get_subtotal()
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_cart_subtotal_before_discount_rule( $wc_curr_version, $cart_total_array, $general_rule_match ) {
            global $woocommerce, $woocommerce_wpml;
            if ( $wc_curr_version >= 3.0 ) {
                $total = WC()->cart->get_subtotal();
            } else {
                $total = $woocommerce->cart->get_subtotal();
            }
            if ( isset( $woocommerce_wpml ) && !empty( $woocommerce_wpml->multi_currency ) ) {
                $new_total = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $total );
                $float_total = floatval( $new_total );
            } else {
                $new_total = $total;
                $float_total = floatval( $new_total );
            }
            $is_passed = array();
            foreach ( $cart_total_array as $key => $cart_total ) {
                settype( $cart_total['payment_conditions_values'], 'float' );
                if ( 'is_equal_to' === $cart_total['payments_conditions_is'] ) {
                    if ( !empty( $cart_total['payment_conditions_values'] ) ) {
                        if ( $cart_total['payment_conditions_values'] === $float_total ) {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'no';
                        }
                    }
                }
                if ( 'less_equal_to' === $cart_total['payments_conditions_is'] ) {
                    if ( !empty( $cart_total['payment_conditions_values'] ) ) {
                        if ( $cart_total['payment_conditions_values'] >= $float_total ) {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'no';
                        }
                    }
                }
                if ( 'less_then' === $cart_total['payments_conditions_is'] ) {
                    if ( !empty( $cart_total['payment_conditions_values'] ) ) {
                        if ( $cart_total['payment_conditions_values'] > $float_total ) {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'no';
                        }
                    }
                }
                if ( 'greater_equal_to' === $cart_total['payments_conditions_is'] ) {
                    if ( !empty( $cart_total['payment_conditions_values'] ) ) {
                        if ( $cart_total['payment_conditions_values'] <= $float_total ) {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'no';
                        }
                    }
                }
                if ( 'greater_then' === $cart_total['payments_conditions_is'] ) {
                    $cart_total['payment_conditions_values'];
                    if ( !empty( $cart_total['payment_conditions_values'] ) ) {
                        if ( $cart_total['payment_conditions_values'] < $float_total ) {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $cart_total['payments_conditions_is'] ) {
                    if ( !empty( $cart_total['payment_conditions_values'] ) ) {
                        if ( $float_total === $cart_total['payment_conditions_values'] ) {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_cart_total'] = 'yes';
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_cart_total', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match rule based on cart subtotal after discount
         *
         * @param string $wc_curr_version
         * @param array  $cart_totalafter_array
         * @param string  $general_rule_match
         *
         * @return array $is_passed
         * @uses     WC_Cart::get_total_discount()
         *
         * @since    1.0.0
         *
         * @uses     dscpw_remove_currency_symbol()
         * @uses     WC_Cart::get_subtotal()
         */
        public function dscpw_match_cart_subtotal_after_discount_rule( $wc_curr_version, $cart_totalafter_array, $general_rule_match ) {
            global $woocommerce, $woocommerce_wpml;
            $get_cart = array();
            if ( $wc_curr_version >= 3.0 ) {
                $get_cart = WC()->cart;
            } else {
                $get_cart = $woocommerce->cart;
            }
            $totalprice = $this->dscpw_remove_currency_symbol( $get_cart->get_cart_subtotal() );
            $totaldisc = 0;
            $subtotal = 0;
            foreach ( $get_cart->get_cart() as $cart_item ) {
                $subtotal += $cart_item['data']->get_price() * $cart_item['quantity'];
            }
            if ( $get_cart->applied_coupons ) {
                foreach ( $get_cart->applied_coupons as $coupon_code ) {
                    $coupon = new WC_Coupon($coupon_code);
                    if ( $coupon->is_valid() ) {
                        if ( $coupon->get_discount_type() === 'percent' ) {
                            $totaldisc += $subtotal * ($coupon->get_amount() / 100);
                        } else {
                            $totaldisc += $coupon->get_amount();
                        }
                    }
                }
            }
            $is_passed = array();
            if ( "" !== $totaldisc && 0.0 !== $totaldisc ) {
                $resultprice = $totalprice - $totaldisc;
                if ( isset( $woocommerce_wpml ) && !empty( $woocommerce_wpml->multi_currency ) ) {
                    $new_resultprice = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultprice );
                    $new_float_price = floatval( $new_resultprice );
                } else {
                    $new_resultprice = $resultprice;
                    $new_float_price = floatval( $new_resultprice );
                }
                foreach ( $cart_totalafter_array as $key => $cart_totalafter ) {
                    settype( $cart_totalafter['payment_conditions_values'], 'float' );
                    if ( 'is_equal_to' === $cart_totalafter['payments_conditions_is'] ) {
                        if ( !empty( $cart_totalafter['payment_conditions_values'] ) ) {
                            if ( $cart_totalafter['payment_conditions_values'] === $new_float_price ) {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'yes';
                            } else {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'no';
                            }
                        }
                    }
                    if ( 'less_equal_to' === $cart_totalafter['payments_conditions_is'] ) {
                        if ( !empty( $cart_totalafter['payment_conditions_values'] ) ) {
                            if ( $cart_totalafter['payment_conditions_values'] >= $new_float_price ) {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'yes';
                            } else {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'no';
                            }
                        }
                    }
                    if ( 'less_then' === $cart_totalafter['payments_conditions_is'] ) {
                        if ( !empty( $cart_totalafter['payment_conditions_values'] ) ) {
                            if ( $cart_totalafter['payment_conditions_values'] > $new_float_price ) {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'yes';
                            } else {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'no';
                            }
                        }
                    }
                    if ( 'greater_equal_to' === $cart_totalafter['payments_conditions_is'] ) {
                        if ( !empty( $cart_totalafter['payment_conditions_values'] ) ) {
                            if ( $cart_totalafter['payment_conditions_values'] <= $new_float_price ) {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'yes';
                            } else {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'no';
                            }
                        }
                    }
                    if ( 'greater_then' === $cart_totalafter['payments_conditions_is'] ) {
                        if ( !empty( $cart_totalafter['payment_conditions_values'] ) ) {
                            if ( $cart_totalafter['payment_conditions_values'] < $new_float_price ) {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'yes';
                            } else {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'no';
                            }
                        }
                    }
                    if ( 'not_in' === $cart_totalafter['payments_conditions_is'] ) {
                        if ( !empty( $cart_totalafter['payment_conditions_values'] ) ) {
                            if ( $new_float_price === $cart_totalafter['payment_conditions_values'] ) {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'no';
                            } else {
                                $is_passed[$key]['has_condition_based_on_cart_total_after'] = 'yes';
                            }
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_cart_total_after', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping methods rules
         *
         * @param string $wc_curr_version
         * @param array $shipping_method_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_shipping_method_rule( $wc_curr_version, $shipping_method_array, $general_rule_match ) {
            $is_passed = array();
            global $woocommerce;
            if ( $wc_curr_version >= 3.0 ) {
                $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
            } else {
                $chosen_shipping_methods = $woocommerce->session->chosen_shipping_methods;
            }
            if ( !empty( $chosen_shipping_methods ) ) {
                foreach ( $shipping_method_array as $key => $method ) {
                    if ( 'is_equal_to' === $method['payments_conditions_is'] ) {
                        if ( in_array( $chosen_shipping_methods[0], $method['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_method'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_method'] = 'no';
                        }
                    }
                    if ( 'not_in' === $method['payments_conditions_is'] ) {
                        if ( in_array( $chosen_shipping_methods[0], $method['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_method'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_method'] = 'yes';
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_method', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing first name rules
         *
         * @param string $attr
         * @param array $billing_firstname_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_billing_firstname_rules( $attr, $billing_firstname_array, $general_rule_match ) {
            $get_firstname = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $billing_firstname_array as $key => $firstname ) {
                settype( $firstname['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $firstname['payments_conditions_is'] ) {
                    if ( !empty( $firstname['payment_conditions_values'] ) ) {
                        if ( strtolower( $firstname['payment_conditions_values'] ) === $get_firstname ) {
                            $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $firstname['payments_conditions_is'] ) {
                    if ( !empty( $firstname['payment_conditions_values'] ) ) {
                        if ( $get_firstname === strtolower( $firstname['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $firstname['payments_conditions_is'] ) {
                    if ( empty( $get_firstname ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $firstname['payments_conditions_is'] ) {
                    if ( !empty( $get_firstname ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_firstname'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_firstname', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing last name rules
         *
         * @param string $attr
         * @param array $billing_lastname_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_billing_lastname_rules( $attr, $billing_lastname_array, $general_rule_match ) {
            $get_lastname = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $billing_lastname_array as $key => $lastname ) {
                settype( $lastname['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $lastname['payments_conditions_is'] ) {
                    if ( !empty( $lastname['payment_conditions_values'] ) ) {
                        if ( strtolower( $lastname['payment_conditions_values'] ) === $get_lastname ) {
                            $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $lastname['payments_conditions_is'] ) {
                    if ( !empty( $lastname['payment_conditions_values'] ) ) {
                        if ( $get_lastname === strtolower( $lastname['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $lastname['payments_conditions_is'] ) {
                    if ( empty( $get_lastname ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $lastname['payments_conditions_is'] ) {
                    if ( !empty( $get_lastname ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_lastname'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_lastname', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing company name rules
         *
         * @param string $attr
         * @param array $billing_company_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_billing_company_rules( $attr, $billing_company_array, $general_rule_match ) {
            $get_company = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $billing_company_array as $key => $company ) {
                settype( $company['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $company['payments_conditions_is'] ) {
                    if ( !empty( $company['payment_conditions_values'] ) ) {
                        if ( strtolower( $company['payment_conditions_values'] ) === $get_company ) {
                            $is_passed[$key]['has_condition_based_on_billing_company'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_company'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $company['payments_conditions_is'] ) {
                    if ( !empty( $company['payment_conditions_values'] ) ) {
                        if ( $get_company === strtolower( $company['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_company'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_company'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $company['payments_conditions_is'] ) {
                    if ( empty( $get_company ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_company'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_company'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $company['payments_conditions_is'] ) {
                    if ( !empty( $get_company ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_company'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_company'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_company', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing address 1 rules
         *
         * @param string $attr
         * @param array $billing_address_1_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_billing_address_1_rules( $attr, $billing_address_1_array, $general_rule_match ) {
            $get_address_1 = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $billing_address_1_array as $key => $address_1 ) {
                settype( $address_1['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $address_1['payments_conditions_is'] ) {
                    if ( !empty( $address_1['payment_conditions_values'] ) ) {
                        if ( strtolower( $address_1['payment_conditions_values'] ) === $get_address_1 ) {
                            $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $address_1['payments_conditions_is'] ) {
                    if ( !empty( $address_1['payment_conditions_values'] ) ) {
                        if ( $get_address_1 === strtolower( $address_1['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $address_1['payments_conditions_is'] ) {
                    if ( empty( $get_address_1 ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $address_1['payments_conditions_is'] ) {
                    if ( !empty( $get_address_1 ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_address_1'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_address_1', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing address 2 rules
         *
         * @param string $attr
         * @param array $billing_address_2_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_billing_address_2_rules( $attr, $billing_address_2_array, $general_rule_match ) {
            $get_address_2 = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $billing_address_2_array as $key => $address_2 ) {
                settype( $address_2['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $address_2['payments_conditions_is'] ) {
                    if ( !empty( $address_2['payment_conditions_values'] ) ) {
                        if ( strtolower( $address_2['payment_conditions_values'] ) === $get_address_2 ) {
                            $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $address_2['payments_conditions_is'] ) {
                    if ( !empty( $address_2['payment_conditions_values'] ) ) {
                        if ( $get_address_2 === strtolower( $address_2['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $address_2['payments_conditions_is'] ) {
                    if ( empty( $get_address_2 ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $address_2['payments_conditions_is'] ) {
                    if ( !empty( $get_address_2 ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_address_2'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_address_2', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing country rules
         *
         * @param array $billing_country_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         * @uses     WC_Customer::get_shipping_country()
         *
         */
        public function dscpw_match_billing_country_rules( $billing_country_array, $general_rule_match ) {
            $selected_country = WC()->customer->get_billing_country();
            $is_passed = array();
            foreach ( $billing_country_array as $key => $country ) {
                if ( 'is_equal_to' === $country['payments_conditions_is'] ) {
                    if ( !empty( $country['payment_conditions_values'] ) ) {
                        if ( in_array( $selected_country, $country['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_country'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_country'] = 'no';
                        }
                    }
                    if ( empty( $country['payment_conditions_values'] ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_country'] = 'yes';
                    }
                }
                if ( 'not_in' === $country['payments_conditions_is'] ) {
                    if ( !empty( $country['payment_conditions_values'] ) ) {
                        if ( in_array( $selected_country, $country['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_country'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_country'] = 'yes';
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_country', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing city rules
         *
         * @param string $attr
         * @param array $billing_city_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_billing_city_rules( $attr, $billing_city_array, $general_rule_match ) {
            $get_city = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $billing_city_array as $key => $city ) {
                if ( 'is_equal_to' === $city['payments_conditions_is'] ) {
                    if ( !empty( $city['payment_conditions_values'] ) ) {
                        $citystr = str_replace( PHP_EOL, "<br/>", trim( $city['payment_conditions_values'] ) );
                        $city_val_array = explode( '<br/>', $citystr );
                        $new_city_array = array();
                        foreach ( $city_val_array as $value ) {
                            $new_city_array[] = trim( $value );
                        }
                        $cities_array = array_map( 'trim', $new_city_array );
                        $final_city_array = array_map( 'strtolower', $cities_array );
                        if ( in_array( $get_city, $final_city_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_city'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_city'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $city['payments_conditions_is'] ) {
                    if ( !empty( $city['payment_conditions_values'] ) ) {
                        $citystr = str_replace( PHP_EOL, "<br/>", $city['payment_conditions_values'] );
                        $city_val_array = explode( '<br/>', $citystr );
                        $cities_array = array_map( 'trim', $city_val_array );
                        $final_city_array = array_map( 'strtolower', $cities_array );
                        if ( in_array( $get_city, $final_city_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_city'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_city'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $city['payments_conditions_is'] ) {
                    if ( empty( $get_city ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_city'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_city'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $city['payments_conditions_is'] ) {
                    if ( !empty( $get_city ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_city'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_city'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_city', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match billing postcode rules
         *
         * @param array $billing_postcode_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         * @uses     WC_Customer::get_billing_postcode()
         *
         */
        public function dscpw_match_billing_postcode_rules( $billing_postcode_array, $general_rule_match ) {
            $selected_postcode = WC()->customer->get_billing_postcode();
            $is_passed = array();
            foreach ( $billing_postcode_array as $key => $postcode ) {
                if ( 'is_equal_to' === $postcode['payments_conditions_is'] ) {
                    if ( !empty( $postcode['payment_conditions_values'] ) ) {
                        $postcodestr = str_replace( PHP_EOL, "<br/>", $postcode['payment_conditions_values'] );
                        $postcode_val_array = explode( '<br/>', $postcodestr );
                        $new_postcode_array = array();
                        foreach ( $postcode_val_array as $value ) {
                            $new_postcode_array[] = trim( $value );
                        }
                        if ( in_array( $selected_postcode, $new_postcode_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $postcode['payments_conditions_is'] ) {
                    if ( !empty( $postcode['payment_conditions_values'] ) ) {
                        $postcodestr = str_replace( PHP_EOL, "<br/>", $postcode['payment_conditions_values'] );
                        $postcode_val_array = explode( '<br/>', $postcodestr );
                        $new_postcode_array = array();
                        foreach ( $postcode_val_array as $value ) {
                            $new_postcode_array[] = trim( $value );
                        }
                        if ( in_array( $selected_postcode, $new_postcode_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $postcode['payments_conditions_is'] ) {
                    if ( empty( $selected_postcode ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $postcode['payments_conditions_is'] ) {
                    if ( !empty( $selected_postcode ) ) {
                        $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_billing_postcode'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_billing_postcode', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Find unique id based on given array
         *
         * @param array $array
         *
         * @return array $result if $array is empty it will return false otherwise return array as $result
         * @since    1.0.0
         *
         */
        public function dscpw_pro_array_flatten( $array ) {
            if ( !is_array( $array ) ) {
                return false;
            }
            $result = array();
            foreach ( $array as $key => $value ) {
                if ( is_array( $value ) ) {
                    $result = array_merge( $result, $this->dscpw_pro_array_flatten( $value ) );
                } else {
                    $result[$key] = $value;
                }
            }
            return $result;
        }

        /**
         * Match shipping firstname rules
         *
         * @param string $attr
         * @param array $shipping_firstname_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_shipping_firstname_rules( $attr, $shipping_firstname_array, $general_rule_match ) {
            $get_firstname = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $shipping_firstname_array as $key => $firstname ) {
                settype( $firstname['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $firstname['payments_conditions_is'] ) {
                    if ( !empty( $firstname['payment_conditions_values'] ) ) {
                        if ( strtolower( $firstname['payment_conditions_values'] ) === $get_firstname ) {
                            $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $firstname['payments_conditions_is'] ) {
                    if ( !empty( $firstname['payment_conditions_values'] ) ) {
                        if ( $get_firstname === strtolower( $firstname['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $firstname['payments_conditions_is'] ) {
                    if ( empty( $get_firstname ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $firstname['payments_conditions_is'] ) {
                    if ( !empty( $get_firstname ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_firstname'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_firstname', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping lastname rules
         *
         * @param string $attr
         * @param array $shipping_lastname_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_shipping_lastname_rules( $attr, $shipping_lastname_array, $general_rule_match ) {
            $get_lastname = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $shipping_lastname_array as $key => $lastname ) {
                settype( $lastname['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $lastname['payments_conditions_is'] ) {
                    if ( !empty( $lastname['payment_conditions_values'] ) ) {
                        if ( strtolower( $lastname['payment_conditions_values'] ) === $get_lastname ) {
                            $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $lastname['payments_conditions_is'] ) {
                    if ( !empty( $lastname['payment_conditions_values'] ) ) {
                        if ( $get_lastname === strtolower( $lastname['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $lastname['payments_conditions_is'] ) {
                    if ( empty( $get_lastname ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $lastname['payments_conditions_is'] ) {
                    if ( !empty( $get_lastname ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_lastname'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_lastname', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping company name rules
         *
         * @param string $attr
         * @param array $shipping_company_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_shipping_company_rules( $attr, $shipping_company_array, $general_rule_match ) {
            $get_company = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $shipping_company_array as $key => $company ) {
                settype( $company['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $company['payments_conditions_is'] ) {
                    if ( !empty( $company['payment_conditions_values'] ) ) {
                        if ( strtolower( $company['payment_conditions_values'] ) === $get_company ) {
                            $is_passed[$key]['has_condition_based_on_shipping_company'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_company'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $company['payments_conditions_is'] ) {
                    if ( !empty( $company['payment_conditions_values'] ) ) {
                        if ( $get_company === strtolower( $company['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_company'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_company'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $company['payments_conditions_is'] ) {
                    if ( empty( $get_company ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_company'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_company'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $company['payments_conditions_is'] ) {
                    if ( !empty( $get_company ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_company'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_company'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_company', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping address 1 rules
         *
         * @param string $attr
         * @param array $shipping_address_1_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_shipping_address_1_rules( $attr, $shipping_address_1_array, $general_rule_match ) {
            $get_address_1 = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $shipping_address_1_array as $key => $address_1 ) {
                settype( $address_1['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $address_1['payments_conditions_is'] ) {
                    if ( !empty( $address_1['payment_conditions_values'] ) ) {
                        if ( strtolower( $address_1['payment_conditions_values'] ) === $get_address_1 ) {
                            $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $address_1['payments_conditions_is'] ) {
                    if ( !empty( $address_1['payment_conditions_values'] ) ) {
                        if ( $get_address_1 === strtolower( $address_1['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $address_1['payments_conditions_is'] ) {
                    if ( empty( $get_address_1 ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $address_1['payments_conditions_is'] ) {
                    if ( !empty( $get_address_1 ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_address_1'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_address_1', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping address 2 rules
         *
         * @param string $attr
         * @param array $shipping_address_2_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_shipping_address_2_rules( $attr, $shipping_address_2_array, $general_rule_match ) {
            $get_address_2 = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $shipping_address_2_array as $key => $address_2 ) {
                settype( $address_2['payment_conditions_values'], 'string' );
                if ( 'is_equal_to' === $address_2['payments_conditions_is'] ) {
                    if ( !empty( $address_2['payment_conditions_values'] ) ) {
                        if ( strtolower( $address_2['payment_conditions_values'] ) === $get_address_2 ) {
                            $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $address_2['payments_conditions_is'] ) {
                    if ( !empty( $address_2['payment_conditions_values'] ) ) {
                        if ( $get_address_2 === strtolower( $address_2['payment_conditions_values'] ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $address_2['payments_conditions_is'] ) {
                    if ( empty( $get_address_2 ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $address_2['payments_conditions_is'] ) {
                    if ( !empty( $get_address_2 ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_address_2'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_address_2', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping country rules
         *
         * @param array $shipping_country_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         * @uses     WC_Customer::get_shipping_country()
         *
         */
        public function dscpw_match_shipping_country_rules( $shipping_country_array, $general_rule_match ) {
            $selected_country = WC()->customer->get_shipping_country();
            $is_passed = array();
            foreach ( $shipping_country_array as $key => $country ) {
                if ( 'is_equal_to' === $country['payments_conditions_is'] ) {
                    if ( !empty( $country['payment_conditions_values'] ) ) {
                        if ( in_array( $selected_country, $country['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_country'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_country'] = 'no';
                        }
                    }
                    if ( empty( $country['payment_conditions_values'] ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_country'] = 'yes';
                    }
                }
                if ( 'not_in' === $country['payments_conditions_is'] ) {
                    if ( !empty( $country['payment_conditions_values'] ) ) {
                        if ( in_array( $selected_country, $country['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_country'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_country'] = 'yes';
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_country', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping city rules
         *
         * @param string $attr
         * @param array $shipping_city_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         */
        public function dscpw_match_shipping_city_rules( $attr, $shipping_city_array, $general_rule_match ) {
            $get_city = strtolower( self::dscpw_get_order_attr( $attr ) );
            $is_passed = array();
            foreach ( $shipping_city_array as $key => $city ) {
                if ( 'is_equal_to' === $city['payments_conditions_is'] ) {
                    if ( !empty( $city['payment_conditions_values'] ) ) {
                        $citystr = str_replace( PHP_EOL, "<br/>", trim( $city['payment_conditions_values'] ) );
                        $city_val_array = explode( '<br/>', $citystr );
                        $new_city_array = array();
                        foreach ( $city_val_array as $value ) {
                            $new_city_array[] = trim( $value );
                        }
                        $cities_array = array_map( 'trim', $new_city_array );
                        $final_city_array = array_map( 'strtolower', $cities_array );
                        if ( in_array( $get_city, $final_city_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_city'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_city'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $city['payments_conditions_is'] ) {
                    if ( !empty( $city['payment_conditions_values'] ) ) {
                        $citystr = str_replace( PHP_EOL, "<br/>", $city['payment_conditions_values'] );
                        $city_val_array = explode( '<br/>', $citystr );
                        $cities_array = array_map( 'trim', $city_val_array );
                        $final_city_array = array_map( 'strtolower', $cities_array );
                        if ( in_array( $get_city, $final_city_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_city'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_city'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $city['payments_conditions_is'] ) {
                    if ( empty( $get_city ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_city'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_city'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $city['payments_conditions_is'] ) {
                    if ( !empty( $get_city ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_city'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_city'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_city', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match shipping postcode rules
         *
         * @param array $shipping_postcode_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.0.0
         *
         * @uses     WC_Customer::get_shipping_postcode()
         *
         */
        public function dscpw_match_shipping_postcode_rules( $shipping_postcode_array, $general_rule_match ) {
            $selected_postcode = WC()->customer->get_shipping_postcode();
            $is_passed = array();
            foreach ( $shipping_postcode_array as $key => $postcode ) {
                if ( 'is_equal_to' === $postcode['payments_conditions_is'] ) {
                    if ( !empty( $postcode['payment_conditions_values'] ) ) {
                        $postcodestr = str_replace( PHP_EOL, "<br/>", $postcode['payment_conditions_values'] );
                        $postcode_val_array = explode( '<br/>', $postcodestr );
                        $new_postcode_array = array();
                        foreach ( $postcode_val_array as $value ) {
                            $new_postcode_array[] = trim( $value );
                        }
                        if ( in_array( $selected_postcode, $new_postcode_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $postcode['payments_conditions_is'] ) {
                    if ( !empty( $postcode['payment_conditions_values'] ) ) {
                        $postcodestr = str_replace( PHP_EOL, "<br/>", $postcode['payment_conditions_values'] );
                        $postcode_val_array = explode( '<br/>', $postcodestr );
                        $new_postcode_array = array();
                        foreach ( $postcode_val_array as $value ) {
                            $new_postcode_array[] = trim( $value );
                        }
                        if ( in_array( $selected_postcode, $new_postcode_array, true ) ) {
                            $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'yes';
                        }
                    }
                }
                if ( 'is_empty' === $postcode['payments_conditions_is'] ) {
                    if ( empty( $selected_postcode ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'no';
                    }
                }
                if ( 'is_not_empty' === $postcode['payments_conditions_is'] ) {
                    if ( !empty( $selected_postcode ) ) {
                        $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'yes';
                    } else {
                        $is_passed[$key]['has_condition_based_on_shipping_postcode'] = 'no';
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_shipping_postcode', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match day of week rules
         *
         * @param array $day_of_week_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.1.1
         *
         */
        public function dscpw_match_day_of_week_rules( $day_of_week_array, $general_rule_match ) {
            $today = strtolower( gmdate( "D" ) );
            $is_passed = array();
            foreach ( $day_of_week_array as $key => $day ) {
                if ( 'is_equal_to' === $day['payments_conditions_is'] ) {
                    if ( !empty( $day['payment_conditions_values'] ) ) {
                        if ( in_array( $today, $day['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_day_of_week'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_day_of_week'] = 'no';
                        }
                    }
                    if ( empty( $day['payment_conditions_values'] ) ) {
                        $is_passed[$key]['has_condition_based_on_day_of_week'] = 'yes';
                    }
                }
                if ( 'not_in' === $day['payments_conditions_is'] ) {
                    if ( !empty( $day['payment_conditions_values'] ) ) {
                        if ( in_array( $today, $day['payment_conditions_values'], true ) ) {
                            $is_passed[$key]['has_condition_based_on_day_of_week'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_day_of_week'] = 'yes';
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_day_of_week', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Match date rules
         *
         * @param array  $date_array
         * @param string $general_rule_match
         *
         * @return array $is_passed
         *
         * @since    1.1.1
         */
        public function dscpw_match_date_rules( $date_array, $general_rule_match ) {
            $is_passed = array();
            $current_date = strtotime( gmdate( 'd-m-Y' ) );
            foreach ( $date_array as $key => $date ) {
                $selected_date = ( isset( $date['payment_conditions_values'] ) && !empty( $date['payment_conditions_values'] ) ? strtotime( $date['payment_conditions_values'] ) : '' );
                if ( 'is_equal_to' === $date['payments_conditions_is'] ) {
                    if ( !empty( $selected_date ) ) {
                        if ( $selected_date === $current_date ) {
                            $is_passed[$key]['has_condition_based_on_date'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_date'] = 'no';
                        }
                    }
                }
                if ( 'less_equal_to' === $date['payments_conditions_is'] ) {
                    if ( !empty( $selected_date ) ) {
                        if ( $selected_date >= $current_date ) {
                            $is_passed[$key]['has_condition_based_on_date'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_date'] = 'no';
                        }
                    }
                }
                if ( 'less_then' === $date['payments_conditions_is'] ) {
                    if ( !empty( $selected_date ) ) {
                        if ( $selected_date > $current_date ) {
                            $is_passed[$key]['has_condition_based_on_date'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_date'] = 'no';
                        }
                    }
                }
                if ( 'greater_equal_to' === $date['payments_conditions_is'] ) {
                    if ( !empty( $selected_date ) ) {
                        if ( $selected_date <= $current_date ) {
                            $is_passed[$key]['has_condition_based_on_date'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_date'] = 'no';
                        }
                    }
                }
                if ( 'greater_then' === $date['payments_conditions_is'] ) {
                    if ( !empty( $selected_date ) ) {
                        if ( $selected_date < $current_date ) {
                            $is_passed[$key]['has_condition_based_on_date'] = 'yes';
                        } else {
                            $is_passed[$key]['has_condition_based_on_date'] = 'no';
                        }
                    }
                }
                if ( 'not_in' === $date['payments_conditions_is'] ) {
                    if ( !empty( $selected_date ) ) {
                        if ( $current_date === $selected_date ) {
                            $is_passed[$key]['has_condition_based_on_date'] = 'no';
                        } else {
                            $is_passed[$key]['has_condition_based_on_date'] = 'yes';
                        }
                    }
                }
            }
            $main_is_passed = $this->dscpw_check_all_passed_general_rule( $is_passed, 'has_condition_based_on_date', $general_rule_match );
            return $main_is_passed;
        }

        /**
         * Find unique id based on given array
         *
         * @param array  $is_passed
         * @param string $has_rule_based
         * @param string $general_rule_match
         *
         * @return string $main_is_passed
         * @since  1.1.1
         *
         */
        public function dscpw_check_all_passed_general_rule( $is_passed, $has_rule_based, $general_rule_match ) {
            $main_is_passed = 'no';
            $flag = array();
            if ( !empty( $is_passed ) ) {
                foreach ( $is_passed as $key => $is_passed_value ) {
                    if ( 'yes' === $is_passed_value[$has_rule_based] ) {
                        $flag[$key] = true;
                    } else {
                        $flag[$key] = false;
                    }
                }
                if ( 'any' === $general_rule_match ) {
                    if ( in_array( true, $flag, true ) ) {
                        $main_is_passed = 'yes';
                    } else {
                        $main_is_passed = 'no';
                    }
                } else {
                    if ( in_array( false, $flag, true ) ) {
                        $main_is_passed = 'no';
                    } else {
                        $main_is_passed = 'yes';
                    }
                }
            }
            return $main_is_passed;
        }

        /**
         * Unset payments methods based on rule
         *
         * @return array $available_payments_methods
         * @uses  get_posts()
         *
         * @since 1.0.0
         *
         */
        public function dscpw_unset_payments_methods( $available_payments_methods ) {
            if ( is_admin() ) {
                return;
            }
            $all_available_payments = $available_payments_methods;
            $matched_methods = array();
            $all_methods = array();
            $sm_posts = self::$admin_object->dscpw_get_conditional_payments_rules();
            $default_disable_payments = array();
            if ( !empty( $sm_posts ) ) {
                foreach ( $sm_posts as $sm_post ) {
                    // Check if payments conditions match
                    $is_match = $this->dscpw_condition_match_rules( $sm_post );
                    // Add to matched methods array
                    if ( true === $is_match ) {
                        $matched_methods[] = $sm_post->ID;
                    }
                    $all_methods[] = $sm_post->ID;
                }
            }
            if ( !empty( $all_methods ) && is_array( $all_methods ) ) {
                foreach ( $all_methods as $method_id ) {
                    $cp_actions_metabox = get_post_meta( $method_id, 'cp_actions_metabox', true );
                    if ( !empty( $cp_actions_metabox ) && is_array( $cp_actions_metabox ) ) {
                        foreach ( $cp_actions_metabox as $cp_action ) {
                            if ( $cp_action['conditional_payments_actions'] === 'enable_payments' ) {
                                foreach ( $available_payments_methods as $key => $gateway ) {
                                    if ( in_array( $key, (array) $cp_action['payment_actions_values'], true ) ) {
                                        $default_disable_payments[$key] = $available_payments_methods[$key];
                                        unset($available_payments_methods[$key]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ( !empty( $matched_methods ) && is_array( $matched_methods ) ) {
                foreach ( $matched_methods as $cp_id ) {
                    $cp_actions_metabox = get_post_meta( $cp_id, 'cp_actions_metabox', true );
                    if ( !empty( $cp_actions_metabox ) && is_array( $cp_actions_metabox ) ) {
                        foreach ( $cp_actions_metabox as $cp_action ) {
                            if ( $cp_action['conditional_payments_actions'] === 'disable_payments' ) {
                                foreach ( $available_payments_methods as $key => $gateway ) {
                                    if ( in_array( $key, (array) $cp_action['payment_actions_values'], true ) ) {
                                        unset($available_payments_methods[$key]);
                                    }
                                }
                            } elseif ( $cp_action['conditional_payments_actions'] === 'enable_payments' ) {
                                foreach ( $all_available_payments as $all_keys => $gateway ) {
                                    if ( in_array( $all_keys, (array) $cp_action['payment_actions_values'], true ) ) {
                                        foreach ( $default_disable_payments as $key => $default_disable_payment ) {
                                            if ( !in_array( $default_disable_payment, $available_payments_methods, true ) ) {
                                                $available_payments_methods[$key] = $default_disable_payment;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $available_payments_methods;
        }

        /**
         * Display array column
         *
         * @param array $input
         * @param int   $columnKey
         * @param int   $indexKey
         *
         * @return array $array It will return array if any error generate then it will return false
         * @since  1.0.0
         *
         */
        public function dscpw_fee_array_column_public( array $input, $columnKey, $indexKey = null ) {
            $array = array();
            foreach ( $input as $value ) {
                if ( !isset( $value[$columnKey] ) ) {
                    wp_die( sprintf( esc_html_x( 'Key %d does not exist in array', esc_attr( $columnKey ), 'conditional-payments' ) ) );
                    return false;
                }
                if ( is_null( $indexKey ) ) {
                    $array[] = $value[$columnKey];
                } else {
                    if ( !isset( $value[$indexKey] ) ) {
                        wp_die( sprintf( esc_html_x( 'Key %d does not exist in array', esc_attr( $indexKey ), 'conditional-payments' ) ) );
                        return false;
                    }
                    if ( !is_scalar( $value[$indexKey] ) ) {
                        wp_die( sprintf( esc_html_x( 'Key %d does not contain scalar value', esc_attr( $indexKey ), 'conditional-payments' ) ) );
                        return false;
                    }
                    $array[$value[$indexKey]] = $value[$columnKey];
                }
            }
            return $array;
        }

        /**
         * Get fields which require manual trigger for checkout update
         * 
         * By default changing first name, last name, company and certain other fields
         * do not trigger checkout update. Thus we need to trigger update manually if we have
         * conditions for these fields.
         * 
         * Triggering will be done in JS. However, we check here if we have conditions for these
         * fields. If we dont have, we dont want to trigger update as that would be unnecessary.
         * 
         * @since  1.0.0
         */
        public function dscpw_name_address_fields() {
            $found_fields = get_transient( 'dscpw_name_address_fields' );
            if ( false === $found_fields ) {
                $matched_methods = array();
                $sm_posts = self::$admin_object->dscpw_get_conditional_payments_rules();
                if ( !empty( $sm_posts ) ) {
                    foreach ( $sm_posts as $sm_post ) {
                        $matched_methods[] = $sm_post->ID;
                    }
                }
                $found_fields = array();
                $fields = array(
                    'billing_first_name',
                    'billing_last_name',
                    'billing_company',
                    'shipping_first_name',
                    'shipping_last_name',
                    'shipping_company',
                    'billing_email',
                    'previous_order'
                );
                $condition_fields = [];
                if ( !empty( $matched_methods ) && is_array( $matched_methods ) ) {
                    foreach ( $matched_methods as $cp_id ) {
                        $cp_metabox = get_post_meta( $cp_id, 'cp_metabox', true );
                        if ( !empty( $cp_metabox ) && is_array( $cp_metabox ) ) {
                            foreach ( $cp_metabox as $cp_conditions ) {
                                $condition_fields[] = $cp_conditions['conditional_payments_conditions'];
                                foreach ( $fields as $field ) {
                                    if ( in_array( $field, $condition_fields, true ) ) {
                                        $found_fields = array_intersect( $fields, $condition_fields );
                                    }
                                }
                            }
                        }
                    }
                }
                set_transient( 'dscpw_name_address_fields', $found_fields, 60 * MINUTE_IN_SECONDS );
            }
            return $found_fields;
        }

        /**
         * Remove WooCommerce currency symbol
         *
         * @param float $price
         *
         * @return float $new_price2
         * @since  1.0.0
         *
         * @uses   get_woocommerce_currency_symbol()
         *
         */
        public function dscpw_remove_currency_symbol( $price ) {
            $wc_currency_symbol = get_woocommerce_currency_symbol();
            $new_price = str_replace( $wc_currency_symbol, '', $price );
            $new_price2 = (double) preg_replace( '/[^.\\d]/', '', $new_price );
            return $new_price2;
        }

        /*
         * Get WooCommerce version number
         *
         * @since  1.0.0
         *
         * @return string if file is not exists then it will return null
         */
        function dscpw_get_woo_version_number() {
            // If get_plugins() isn't available, require it
            if ( !function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            // Create the plugins folder and file variables
            $plugin_folder = get_plugins( '/' . 'woocommerce' );
            $plugin_file = 'woocommerce.php';
            // If the plugin version number is set, return it
            if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
                return $plugin_folder[$plugin_file]['Version'];
            } else {
                return null;
            }
        }

    }

}