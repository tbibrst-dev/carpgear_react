<?php
/**
 * Plugin activation hook.
 *
 * @package Flexible Printing
 */

/**
 * Install DB table
 *
 * @return bool
 */
function flexible_printing_install() {
	global $wpdb;
	$flexible_printing_db_version = '1.0.0';

	$charset = 'CHARACTER SET UTF8';

	if ( get_option( 'flexible_printing_db_version' ) === '1.0.0' ) {
		return false;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name = $wpdb->prefix . 'fp_log';

	//phpcs:ignore
	$sql = "CREATE TABLE $table_name (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		type varchar(10) NOT NULL,
		printer varchar(60),
		integration varchar(40) NOT NULL,
		title varchar(100) NOT NULL,
		time int(10) unsigned NOT NULL,
		user_login varchar(60),
		job_id varchar(60),
		message varchar(100),
		details text,
		PRIMARY KEY id (id),
		KEY event (integration)
		) $charset";

	dbDelta( $sql );
	update_option( 'flexible_printing_db_version', $flexible_printing_db_version );
}

register_activation_hook( $plugin_file, 'flexible_printing_install' );
