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

/**
 * Controllable WordPress stubs for the Settings tests.
 *
 * Tests set $GLOBALS['image_cdn_test'] to control capabilities, the current
 * screen and options, and inspect recorded calls.
 */
class Image_CDN_Test_JSON_Response extends Exception {
	public $success;
	public $data;
	public $status;

	public function __construct( $success, $data, $status ) {
		parent::__construct( 'JSON response sent' );
		$this->success = $success;
		$this->data    = $data;
		$this->status  = $status;
	}
}

function image_cdn_test_reset() {
	$GLOBALS['image_cdn_test'] = array(
		'caps'          => array(),
		'screen'        => null,
		'options'       => array(),
		'remote_calls'  => array(),
		'admin_notices' => array(),
	);
}
image_cdn_test_reset();

function current_user_can( $cap ) {
	return in_array( $cap, $GLOBALS['image_cdn_test']['caps'], true );
}

function get_current_screen() {
	$id = $GLOBALS['image_cdn_test']['screen'];
	return null === $id ? null : (object) array( 'id' => $id );
}

function check_ajax_referer( $action, $query_arg ) {
	return 1;
}

function wp_create_nonce( $action ) {
	return 'nonce-' . $action;
}

function wp_send_json_error( $data = null, $status = null ) {
	throw new Image_CDN_Test_JSON_Response( false, $data, $status );
}

function wp_send_json_success( $data = null, $status = null ) {
	throw new Image_CDN_Test_JSON_Response( true, $data, $status );
}

function esc_js( $text ) {
	return $text;
}

function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function esc_url_raw( $url ) {
	return $url;
}

function wp_unslash( $value ) {
	return $value;
}

function get_site_url() {
	return 'https://blog.example';
}

function plugin_dir_url( $file ) {
	return 'https://blog.example/wp-content/plugins/image-cdn/';
}

function get_option( $name, $default = false ) {
	if ( 'home' === $name ) {
		return 'https://blog.example/';
	}
	return $GLOBALS['image_cdn_test']['options'][ $name ] ?? $default;
}

function delete_option( $name ) {
	unset( $GLOBALS['image_cdn_test']['options'][ $name ] );
	return true;
}

function maybe_unserialize( $value ) {
	return $value;
}

function wp_admin_notice( $message, $args = array() ) {
	$GLOBALS['image_cdn_test']['admin_notices'][] = $message;
}

function is_wp_error( $thing ) {
	return $thing instanceof WP_Error;
}

class WP_Error {
	public function get_error_message() {
		return 'stubbed request';
	}
}

function wp_remote_get( $url, $args = array() ) {
	$GLOBALS['image_cdn_test']['remote_calls'][] = array( 'wp_remote_get', $url );
	return array( 'response' => array( 'code' => 200 ), 'headers' => array() );
}

function wp_safe_remote_get( $url, $args = array() ) {
	$GLOBALS['image_cdn_test']['remote_calls'][] = array( 'wp_safe_remote_get', $url );
	return new WP_Error();
}

if ( ! defined( 'IMAGE_CDN_FILE' ) ) {
	define( 'IMAGE_CDN_FILE', __DIR__ . '/../../image-cdn.php' );
}
