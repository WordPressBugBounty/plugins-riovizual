<?php
/**
 * Plugin Name:       RioVizual
 * Plugin URI:        https://riovizual.com/
 * Description:       Drag and drop Gutenberg table blocks plugin for WordPress to easily create customizable, responsive tables that boost engagement and conversions.
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Version:           2.2.1
 * Author:            WPRio
 * Author URI:        https://riovizual.com/
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       riovizual
 *
 * @package           riovizual
 */

defined( 'ABSPATH' ) || exit;

// Defines constant
define( 'RIO_VIZUAL_VERSION', '2.2.1' );
define( 'RIO_VIZUAL_TEXT_DOMAIN', 'riovizual' );

define( 'RIO_VIZUAL_PATH', plugin_dir_path( __FILE__ ) );
define( 'RIO_VIZUAL_INC_PATH', plugin_dir_path( __FILE__ ) . '/includes' );

define( 'RIO_VIZUAL_BUILD_DIR', dirname( __FILE__ ) . '/build' );
define( 'RIO_VIZUAL_BUILD_URL', plugin_dir_url( __FILE__ ) . 'build' );

define( 'RIO_VIZUAL_ADMIN_URL', plugin_dir_url( __FILE__ ) . 'admin');
define( 'RIO_VIZUAL_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets');

require_once RIO_VIZUAL_INC_PATH . '/class-rio-viz-init.php';

/**
*	Installation Hook
*/
register_activation_hook(__FILE__, 'riovizual_on_activation');

function riovizual_on_activation() {
    if ( ! get_option('_rio_vizual_plugin_installed_on') ) {
		update_option('_rio_vizual_redirect_on_activation', true);
    }
}

/**
 * Uninstallation process
 */
function rio_viz_uninstall() {
	global $wpdb;

	// Delete css from post meta.
	delete_metadata( 'post', 0, '_rio_vizual_css', '', true );

	// Delete options starting with '_rio_vizual_'
	$all_options = wp_load_alloptions();
	foreach ( $all_options as $name => $value ) {
		if ( str_starts_with( $name, '_rio_vizual_' ) ) {
			delete_option( $name );
		}
	}

	// Get all user IDs
	$users = get_users( array( 'fields' => 'ID' ) );

	foreach ( $users as $user_id ) {
		$usermeta = get_user_meta( $user_id );

		foreach ( $usermeta as $key => $value ) {
			if ( str_starts_with( $key, '_rio_vizual_' ) ) {
				delete_user_meta( $user_id, $key );
			}
		}
	}

}

register_uninstall_hook( __FILE__, 'rio_viz_uninstall' );

add_action( 'plugins_loaded', 'riovizual_load_textdomain' );
function riovizual_load_textdomain() {
    load_plugin_textdomain( 'riovizual', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// run the init method.
new Rio_Viz_Init();
