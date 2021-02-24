<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Image_Cdn
 */

// Load plugin files.
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../imageengine/class-settings.php';
require_once __DIR__ . '/../../imageengine/class-rewriter.php';
require_once __DIR__ . '/../../imageengine/class-imagecdn.php';

/**
 * WordPress API stubs for standalone testing.
 */
function is_admin_bar_showing() {
	return false;
}

function wp_parse_url($url, $flags) {
	return parse_url($url, $flags);
}

function is_ssl() {
	return true;
}