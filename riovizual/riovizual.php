<?php
/**
 * Plugin Name:       RioVizual
 * Plugin URI:        https://riovizual.com/
 * Description:       Drag and drop Gutenberg table blocks plugin for WordPress to easily create customizable, responsive tables that boost engagement and conversions.
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Version:           2.3.1
 * Author:            WPRio
 * Author URI:        https://riovizual.com/
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       riovizual
 *
 * @package           riovizual
 */

defined( 'ABSPATH' ) || exit;

define( 'RIO_VIZUAL_VERSION', '2.3.1' );
define( 'RIO_VIZUAL_TEXT_DOMAIN', 'riovizual' );
define( 'RIO_VIZUAL_PATH', plugin_dir_path( __FILE__ ) );
define( 'RIO_VIZUAL_INC_PATH', plugin_dir_path( __FILE__ ) . '/includes' );
define( 'RIO_VIZUAL_BUILD_DIR', dirname( __FILE__ ) . '/build' );
define( 'RIO_VIZUAL_BUILD_URL', plugin_dir_url( __FILE__ ) . 'build' );
define( 'RIO_VIZUAL_ADMIN_URL', plugin_dir_url( __FILE__ ) . 'admin');
define( 'RIO_VIZUAL_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets');

require_once __DIR__ . '/vendor/autoload.php';

use RioVizual\Core\Activator;
use RioVizual\Core\Uninstaller;
use RioVizual\Core\I18n;

register_activation_hook( __FILE__, [ Activator::class, 'activate' ] );
register_uninstall_hook( __FILE__, [ Uninstaller::class, 'uninstall' ] );
add_action( 'plugins_loaded', [ I18n::class, 'load_textdomain' ] );

function riovizual() {
	return \RioVizual\RioVizual::get_instance();
}

// Initialize the Plugin.
riovizual();

