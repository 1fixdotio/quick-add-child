<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Quick_Add_Child
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://gmail.com
 * @copyright 2014 1Fix.io
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

if ( is_multisite() ) {

	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );

	if ( $blogs ) {

	 	foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );

			// delete all transient, options and files you may have added
			delete_option( 'qac-display-activation-message' );
			delete_option( 'quick-add-child' );

			//info: remove and optimize tables
			$GLOBALS['wpdb']->query( "OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`" );

			restore_current_blog();
		}
	}

} else {
	// delete all transient, options and files you may have added
	delete_option( 'qac-display-activation-message' );
	delete_option( 'quick-add-child' );

	//info: remove and optimize tables
	$GLOBALS['wpdb']->query( "OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`" );
}