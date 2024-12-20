<?php
/**
 * Image CDN WordPress plugin by ImageEngine.
 *
 * @package   ImageCDN
 * @author    imageengine
 * @copyright 2021 ScientiaMobile, Inc.
 * @license   GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Image CDN
 * Description:       Optimize your WordPress site with <a href="https://imageengine.io" target="_blank">ImageEngine</a> or another Image CDN (or any other Content Delivery Network).
 * Author:            imageengine
 * Author URI:        https://imageengine.io/
 * Requires at least: 5.3
 * Requires PHP:      7.4
 * Text Domain:       image-cdn
 * License:           GPLv2 or later
 * Version:           1.2.4
 */

// Update this then you update "Requires at least" above!
define( 'IMAGE_CDN_MIN_WP', '5.3' );

// Update this when you update the "Version" above!
define( 'IMAGE_CDN_VERSION', '1.2.4' );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once( __DIR__ . '/vendor/autoload.php' );
}

// Load plugin files.
require_once __DIR__ . '/imageengine/class-settings.php';
require_once __DIR__ . '/imageengine/class-rewriter.php';
require_once __DIR__ . '/imageengine/class-imagecdn.php';
require_once __DIR__ . '/imageengine/class-optionstorage.php';

defined( 'ABSPATH' ) || exit();

define( 'IMAGE_CDN_FILE', __FILE__ );
define( 'IMAGE_CDN_DIR', __DIR__ );
define( 'IMAGE_CDN_BASE', plugin_basename( __FILE__ ) );

add_action( 'plugins_loaded', array( ImageEngine\ImageCDN::class, 'instance' ) );
add_action( 'activated_plugin', array( ImageEngine\ImageCDN::class, 'settings_redirect' ) );
register_uninstall_hook( __FILE__, array( ImageEngine\ImageCDN::class, 'handle_uninstall_hook' ) );
register_activation_hook( __FILE__, array( ImageEngine\ImageCDN::class, 'handle_activation_hook' ) );
add_action( 'admin_notices', array( ImageEngine\ImageCDN::class, 'ie_admin_notice' ) );
