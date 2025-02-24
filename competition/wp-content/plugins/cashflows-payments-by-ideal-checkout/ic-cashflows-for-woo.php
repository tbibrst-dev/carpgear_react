<?php

/**
 * Cashflows' Payment Gateway Plugin by iDEAL Checkout.
 *
 * @author            iDEAL Checkout
 * @copyright         2021 iDEAL Checkout (CodeBrain BV)
 *
 * @wordpress-plugin
 * Plugin Name:       Cashflows Payments by iDEAL Checkout
 * Plugin URI:        https://www.ideal-checkout.nl/payment-providers/cashflows
 * Description:       Cashflows Payment Gateway accepts card payments for eCommerce transactions
 * Version:           2.1.9.1
 * Requires at least: 5.8
 * Tested up to:      6.4.1
 * Requires PHP:      7.4
 * Author:            iDEAL Checkout
 * Author URI:        https://www.ideal-checkout.nl/over-ons
 * Text Domain:       ic-cashflows-for-woo
 * Domain Path: 	  /
 */

// Block output if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('ICCF_ROOT_PATH', __DIR__); // Path without trailing slash
define('ICCF_ROOT_URL', plugins_url('/', __FILE__)); // URL With trailing slash

// Load default plugin functions
require_once ABSPATH.DIRECTORY_SEPARATOR.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php';

if (!defined('ICCF_FUNCTIONS_LOADED')) {
    // Load our libraries
    require_once ICCF_ROOT_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'functions.php';
}

// Load text domain
load_plugin_textdomain('ic-cashflows-for-woo', false, plugin_basename(__DIR__).DIRECTORY_SEPARATOR.'languages');

// Check if cUrl is installed on this server
if (!function_exists('curl_version')) {
    function iccf_doShowCurlError()
    {
        echo '<div class="error"><p>Curl is not installed.<br>In order to use this Plugin, you must have CURL installed on the server.<br>Ask your system administrator/hosting provider to install php_curl</p></div>';
    }
    add_action('admin_notices', 'iccf_doShowCurlError');
}

// Is WooCommerce active on this Wordpress installation?
if (is_plugin_active('woocommerce'.DIRECTORY_SEPARATOR.'woocommerce.php') || is_plugin_active_for_network('woocommerce'.DIRECTORY_SEPARATOR.'woocommerce.php')) {
    include ICCF_ROOT_PATH.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'woocommerce-cashflows.php';
    IdealcheckoutCashflowsForWooCommerce::init();
} else {
    // Woocommerce isn't active, show error
    function iccf_doShowWoocommerceError()
    {
        echo '<div class="error"><p>Cashflows for WooCommerce plugin requires WooCommerce to be active</p></div>';
    }
    add_action('admin_notices', 'iccf_doShowWoocommerceError');
}

function iccf_appendLinks($links_array, $plugin_file_name, $plugin_data, $status)
{
    if (strpos($plugin_file_name, basename(__FILE__))) {
        $links_array[] = '<a href="https://wordpress.org/support/plugin/cashflows-payments-by-ideal-checkout/">Support</a>';
    }

    return $links_array;
}
add_filter('plugin_row_meta', 'iccf_appendLinks', 10, 4);

function iccf_pluginLinks($links)
{
    $actionLinks = [
        'settings' => '<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout').'" aria-label="'.esc_attr__('View WooCommerce settings', 'woocommerce').'">'.esc_html__('Settings', 'woocommerce').'</a>',
    ];

    return array_merge($actionLinks, $links);
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'iccf_pluginLinks');
