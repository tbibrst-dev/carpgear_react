<?php
/**
 * Plugin Name: Flexible Printing
 * Plugin URI: https://octol.io/printing-plugin-site
 * Description: Print the shipping labels using the PrintNode service directly from your WooCommerce store.
 * Version: 1.5.22
 * Author: Octolize
 * Author URI: https://octol.io/printing-author
 * Text Domain: flexible-printing
 * Domain Path: /lang/
 * Requires at least: 5.8
 * Tested up to: 6.5
 * WC requires at least: 8.7
 * WC tested up to: 9.1
 * Requires PHP: 7.4
 *
 * Copyright 2019 WP Desk Ltd.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Flexible_Printing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$plugin_version = '1.5.22';
define( 'FLEXIBLE_PRINTING_VERSION', $plugin_version );

$plugin_name        = 'Flexible Printing';
$product_id         = 'Flexible Printing';
$plugin_class_name  = 'Flexible_Printing_Plugin';
$plugin_text_domain = 'flexible-printing';
$plugin_file        = __FILE__;
$plugin_dir         = __DIR__;
$plugin_shops       = [
	'pl_PL'   => 'https://www.wpdesk.pl/',
	'default' => 'https://octolize.com/',
];

$requirements = [
	'php'     => '7.4',
	'wp'      => '4.5',
	'plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
		],
	],
];

require __DIR__ . '/inc/plugin-install.php';
require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52.php';
