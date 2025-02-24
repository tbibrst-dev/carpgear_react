<?php
/**
 * Plugin Name: DPD UK & DPD Local Labels and Tracking
 * Plugin URI: https://octol.io/dpd-uk-plugin-site
 * Description: WooCommerce DPD UK integration.
 * Version: 2.0.23
 * Author: Octolize
 * Author URI: https://octol.io/dpd-uk-author
 * Text Domain: woocommerce-dpd-uk
 * Domain Path: /lang/
 * Requires at least: 5.8
 * Tested up to: 6.5
 * WC requires at least: 8.7
 * WC tested up to: 9.1
 * Requires PHP: 7.4
 * ​
 * Copyright 2017 WP Desk Ltd.
 * ​
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * ​
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * ​
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package WooCommerce DPD UK
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '2.0.23';

$plugin_name        = 'WooCommerce DPD UK';
$product_id         = 'WooCommerce DPD UK';
$plugin_class_name  = 'WPDesk_WooCommerce_DPD_UK_Plugin';
$plugin_text_domain = 'woocommerce-dpd-uk';
$plugin_file        = __FILE__;
$plugin_dir         = __DIR__;
$plugin_shops       = [
	'default' => 'https://octolize.com',
];

define( $plugin_class_name, $plugin_version );
define( 'WOOCOMMERCE_DPD_UK_VERSION', $plugin_version );

$requirements = [
	'php'          => '7.4',
	'wp'           => '4.5',
	'repo_plugins' => [
		[
			'name'      => 'flexible-shipping/flexible-shipping.php',
			'nice_name' => 'Flexible Shipping',
			'version'   => '3.7',
		],
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '3.5',
		],
	],
];

require_once( plugin_basename( 'inc/wpdesk-woo27-functions.php' ) );
require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52.php';
