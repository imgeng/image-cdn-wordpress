<?php
/**
 * This file contains the ImageCDN class
 *
 * @package ImageCDN
 */

namespace ImageEngine;

/**
 * The ImageCDN class manages plugin initialization (adding actions and filters, etc).
 */
class ImageCDN {


	/**
	 * Static constructor.
	 */
	public static function instance() {
		new self();
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Only enable the hooks if the configuration is valid and the plugin is enabled.
		if ( self::should_rewrite() ) {
			// Rewriter hook.
			add_action( 'template_redirect', array( self::class, 'handle_rewrite_hook', 0 ) );

			// Rewrite rendered content in REST API.
			add_filter( 'the_content', array( self::class, 'rewrite_the_content' ), 100 );

			// Resource hints.
			// add_action( 'wp_head', array( self::class, 'add_head_tags' ), 0 );
			add_action( 'send_headers', array( self::class, 'add_headers' ), 0 );
		}

		// Hooks.
		add_action( 'admin_init', array( self::class, 'register_textdomain' ) );
		add_action( 'admin_init', array( Settings::class, 'register_settings' ) );
		add_action( 'admin_menu', array( Settings::class, 'add_settings_page' ) );
		add_action( 'admin_footer', array( Settings::class, 'register_test_config' ) );
		add_action( 'wp_ajax_image_cdn_test_config', array( Settings::class, 'test_config' ) );
		add_filter( 'plugin_action_links_' . IMAGE_CDN_BASE, array( self::class, 'add_action_link' ) );
	}

	/**
	 * Add http headers for Client Hints, Feature Policy and Preconnect Resource Hint.
	 */
	public static function add_headers() {
		// Add client hints.
		header( 'Accept-CH: viewport-width, width, device-memory, dpr, rtt, downlink, ect' );

		// Add resource hints and feature policy.
		$options = self::get_options();
		$host    = wp_parse_url( $options['url'], PHP_URL_HOST );
		if ( ! empty( $host ) ) {
			$protocol = ( is_ssl() ) ? 'https://' : 'http://';
			header( 'Link: <' . $protocol . $host . '>; rel=preconnect' );
			header( 'Feature-Policy: ch-viewport-width ' . $protocol . $host . '; ch-width ' . $protocol . $host . '; ch device-memory ' . $protocol . $host . '; ch-dpr ' . $protocol . $host . '; ch-rtt ' . $protocol . $host . '; ch-downlink ' . $protocol . $host . '; ch-ect ' . $protocol . $host . ';' );
		}
	}


	/**
	 * Add meta tags for Client Hints and Preconnect Resource Hint.
	 */
	public static function add_head_tags() {
		// Add client hints.
		echo '    <meta http-equiv="Accept-CH" content="DPR, Viewport-Width, Width, Save-Data">' . "\n";

		// Add resource hints.
		$options = self::get_options();
		$host    = wp_parse_url( $options['url'], PHP_URL_HOST );
		if ( ! empty( $host ) ) {
			echo '    <link rel="preconnect" href="//' . esc_attr( $host ) . '">' . "\n";
		}
	}

	/**
	 * Add action links.
	 *
	 * @param   array $data  already existing links.
	 * @return  array  $data  extended array with links.
	 */
	public static function add_action_link( $data ) {
		// check permission.
		if ( ! current_user_can( 'manage_options' ) ) {
			return $data;
		}

		return array_merge(
			$data,
			array(
				sprintf(
					'<a href="%s">%s</a>',
					add_query_arg( array( 'page' => 'image_cdn' ), admin_url( 'options-general.php' ) ),
					__( 'Settings' )
				),
			)
		);
	}

	/**
	 * Run uninstall hook.
	 */
	public static function handle_uninstall_hook() {
		delete_option( 'image_cdn' );
	}

	/**
	 * Returns the default options for this plugin.
	 *
	 * @return array Default options.
	 */
	public static function default_options() {
		$url = self::get_url_path();

		$content_url   = self::get_url_path( content_url() );
		$includes_url  = self::get_url_path( includes_url() );
		$content_path  = trim( $content_url['path'], '/' );
		$includes_path = trim( $includes_url['path'], '/' );

		return array(
			'url'        => $url['base'],
			'path'       => $url['path'],
			'dirs'       => implode( ',', array( $content_path, $includes_path ) ),
			'excludes'   => '.php',
			'relative'   => true,
			'https'      => false,
			'directives' => '',
			'enabled'    => false,
		);
	}

	/**
	 * Run activation hook.
	 */
	public static function handle_activation_hook() {
		add_option( 'image_cdn', self::default_options() );
	}

	/**
	 * Check plugin requirements.
	 */
	public static function image_cdn_requirements_check() {
		// WordPress version check.
		if ( version_compare( $GLOBALS['wp_version'], IMAGE_CDN_MIN_WP . 'alpha', '<' ) ) {
			show_message(
				sprintf(
					'<div class="error"><p>%s</p></div>',
					sprintf(
						// translators: %s: WordPress version.
						__( 'The Image CDN plugin is optimized for WordPress %s. Please disable the plugin or upgrade your WordPress installation (recommended).', 'image-cdn' ),
						IMAGE_CDN_MIN_WP
					)
				)
			);
		}
	}

	/**
	 * Register textdomain.
	 */
	public static function register_textdomain() {
		load_plugin_textdomain( 'image-cdn', false, 'image-cdn/lang' );
	}

	/**
	 * Return plugin options.
	 *
	 * @return  array  $diff  data pairs.
	 */
	public static function get_options() {
		return wp_parse_args( get_option( 'image_cdn' ), self::default_options() );
	}

	/**
	 * Split the WP home URL into base URL and path components.
	 *
	 * @param string $url Input URL.
	 * @return array Array of components with keys 'url', 'base' and 'path'.
	 */
	public static function get_url_path( $url = '' ) {
		if ( '' === $url ) {
			$url = get_option( 'home' );
		}

		$base_url = $url;
		$path     = '';

		if ( preg_match( '#^(https?://[^/]+)(/.*)$#', $url, $matches ) ) {
			$base_url = $matches[1];
			$path     = $matches[2];
		}

		return array(
			'url'  => $url,
			'base' => $base_url,
			'path' => $path,
		);
	}

	/**
	 * Return new rewriter.
	 */
	public static function get_rewriter() {
		$options  = self::get_options();
		$excludes = array_map( 'trim', explode( ',', $options['excludes'] ) );

		return new Rewriter(
			get_option( 'home' ),
			$options['url'],
			$options['path'],
			$options['dirs'],
			$excludes,
			$options['relative'],
			$options['https'],
			$options['directives']
		);
	}

	/**
	 * Run rewrite hook.
	 */
	public static function handle_rewrite_hook() {
		$rewriter = self::get_rewriter();
		ob_start( array( $rewriter, 'rewrite' ) );
	}

	/**
	 * Rewrite html content.
	 *
	 * @param string $html The HTML content.
	 */
	public static function rewrite_the_content( $html ) {
		$rewriter = self::get_rewriter();
		return $rewriter->rewrite( $html );
	}

	/**
	 * Returns true if the content should be rewritten.
	 */
	public static function should_rewrite() {
		$options = self::get_options();

		if ( ! $options['enabled'] ) {
			return false;
		}

		if ( '' === $options['url'] ) {
			return false;
		}

		return true;
	}
}
