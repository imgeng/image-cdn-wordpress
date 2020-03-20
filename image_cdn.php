<?php

/*
Plugin Name: Image CDN
Text Domain: image-cdn
Description: Optimize your WordPress site with <a href="https://imageengine.io" target="_blank">ImageEngine</a> or another Image CDN (Content Delivery Network).  This plugin is based on "CDN Enabler" by KeyCDN.
Author: ImageEngine / ScientiaMobile
Author URI: https://imageengine.io/
License: GPLv2 or later
Version: 0.9.0
*/

require_once __DIR__ . '/ImageEngine/Settings.php';
require_once __DIR__ . '/ImageEngine/Rewriter.php';
require_once __DIR__ . '/ImageEngine/ImageCDN.php';

defined('ABSPATH') || exit();

define('IMAGE_CDN_FILE', __FILE__);
define('IMAGE_CDN_DIR', __DIR__);
define('IMAGE_CDN_BASE', plugin_basename(__FILE__));
define('IMAGE_CDN_MIN_WP', '3.8');

add_action('plugins_loaded', [ImageEngine\ImageCDN::class, 'instance']);
register_uninstall_hook(__FILE__, [ImageEngine\ImageCDN::class, 'handle_uninstall_hook']);
register_activation_hook(__FILE__, [ImageEngine\ImageCDN::class, 'handle_activation_hook']);