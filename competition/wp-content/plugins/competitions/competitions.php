<?php
/**
 * @package Competitions
 */
/*
Plugin Name: Competitions
Description: Lottery System Admin Management.
Version: 1.0
Requires at least: 5.8
Requires PHP: 5.6.20
Author: Dev
*/


// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('COMPETITIONS_VERSION', '1.0');
define('COMPETITIONS__MINIMUM_WP_VERSION', '5.8');
define('COMPETITIONS__PLUGIN_DIR', plugin_dir_path(__FILE__));


register_activation_hook(__FILE__, array('Competitions', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('Competitions', 'plugin_deactivation'));

require_once (COMPETITIONS__PLUGIN_DIR . 'class.competitions.php');


add_action('init', array('Competitions', 'init'));
add_action('init', array('Competitions', 'register_post_type_winners'));

if (is_admin()) {
	require_once (COMPETITIONS__PLUGIN_DIR . 'class.competitions-admin.php');
	add_action('init', array('Competitions_Admin', 'init'));
}

