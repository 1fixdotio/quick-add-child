<?php
/**
 * Quick Add Child.
 *
 * Add child posts right from a hierarchical post editing screen.
 *
 * @package   Quick_Add_Child
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://1fix.io/quick-add-child
 * @copyright 2014 1Fix.io
 *
 * @wordpress-plugin
 * Plugin Name:       Quick Add Child
 * Plugin URI:        http://1fix.io/quick-add-child
 * Description:       Add child posts right from a hierarchical post editing screen.
 * Version:           0.7.0
 * Author:            1fixdotio
 * Author URI:        http://1fix.io
 * Text Domain:       quick-add-child
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/1fixdotio/quick-add-child
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-quick-add-child.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Quick_Add_Child', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Quick_Add_Child', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Quick_Add_Child', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-quick-add-child-admin.php' );
	add_action( 'plugins_loaded', array( 'Quick_Add_Child_Admin', 'get_instance' ) );

}
