<?php
/**
 * This file contains the ImageCDN class.
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
	 * Client hints.
	 *
	 * @var []string
	 */
	private static $client_hints = array(
		'sec-ch-dpr',
		'sec-ch-width',
		'sec-ch-viewport-width',
		/**
		 * Disabled for CORS compatibility:
		 * 'ECT',
		 * 'Device-Memory',
		 * 'RTT',
		 * 'Downlink',
		 */
	);

	/**
	 * Client hints that do not require CORS preflight confirmation.
	 *
	 * @var []string
	 */
	private static $safe_client_hints = array(
		'sec-ch-dpr',
		'sec-ch-width',
		'sec-ch-viewport-width',
	);

	/**
	 * Singleton Rewriter instance.
	 *
	 * @var Rewriter
	 */
	private static $rewriter;

	/**
	 * If true, some functionality will be augmented to facilitate testing.
	 *
	 * @internal
	 * @var bool
	 */
	public static $tests_running = false;

	/**
	 * Captures headers written during unit testing.
	 *
	 * @internal
	 * @var []string
	 */
	public static $test_headers_written = array();

	/**
	 * Options that will be used during unit testing.
	 *
	 * @internal
	 * @var []string
	 */
	public static $test_options = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Only enable the hooks if the configuration is valid and the plugin is enabled.
		if ( self::should_rewrite() ) {
			// Rewriter hook.
			add_action( 'template_redirect', array( self::class, 'handle_rewrite_hook' ) );

			// Rewrite rendered content in REST API.
			add_filter( 'the_content', array( self::class, 'rewrite_html' ), 100 );

			/**
			 * Resource hints.  Note that the 'wp_head' is disabled for the time being due to CORS incompatibility.
			 * add_action( 'wp_head', array( self::class, 'add_head_tags' ), 0 );
			 */
			add_action( 'send_headers', array( self::class, 'add_headers' ), 0 );

			// REST API hooks.
			add_filter( 'rest_post_dispatch', array( self::class, 'rewrite_rest_api' ), 10, 3 );

			// Custom filters that can be used in themes to update bare URLs and URLs in HTML.
			add_filter( 'image_cdn_url', array( self::class, 'rewrite_url' ) );
			add_filter( 'image_cdn_html', array( self::class, 'rewrite_html' ) );
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
	 * Outputs an HTTP header.
	 *
	 * @param string $key HTTP header key.
	 * @param string $value HTTP header value.
	 */
	private static function header( $key, $value ) {
		$val = "$key: $value";
		if ( self::$tests_running ) {
			self::$test_headers_written[] = $val;
			return;
		}

		header( $val );
	}

	/**
	 * Add http headers for Client Hints, Feature Policy and Preconnect Resource Hint.
	 */
	public static function add_headers() {
		self::header( 'Accept-CH', strtolower( implode( ', ', self::$client_hints ) ) );

		// Add resource hints and feature policy.
		$options = self::get_options();
		$host    = wp_parse_url( $options['url'], PHP_URL_HOST );
		if ( empty( $host ) ) {
			return;
		}

		$protocol = is_ssl() ? 'https' : 'http';

		// Add Preconnect header.
		self::header( 'Link', "<{$protocol}://{$host}>; rel=preconnect" );

		// Add Feature-Policy header.
		// @deprecated in favor of Permissions-Policy and will be removed once adaquate market
		// adoption has been reached (90-95%).
		$features = array();
		foreach ( self::$client_hints as $hint ) {
			$features[] = strtolower( "ch-{$hint} {$protocol}://{$host}" );
		}

		$permissions = array();
		foreach ( self::$client_hints as $hint ) {
			$permissions[] = strtolower( "sec-{$hint}=(\"{$protocol}://{$host}\")" );
		}
		// Add Permissions-Policy header.
		// This header replaced Feature-Policy in Chrome 88, released in January 2021.
		// @see https://github.com/w3c/webappsec-permissions-policy/blob/main/permissions-policy-explainer.md#appendix-big-changes-since-this-was-called-feature-policy .
		self::header( 'Permissions-Policy', strtolower( implode( ', ', $permissions ) ) );
	}


	/**
	 * Add meta tags for Client Hints and Preconnect Resource Hint.
	 */
	public static function add_head_tags() {
		// Add client hints.
		echo '    <meta http-equiv="Accept-CH" content="' . esc_attr( implode( ', ', self::$client_hints ) ) . '">' . "\n";

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
					add_query_arg( array( 'page' => 'image_cdn' ), admin_url( 'admin.php' ) ),
					__( 'Settings' )
				),
			)
		);
	}

	/**
	 * Gets the list of active client hints.
	 *
	 * @return array client hints.
	 */
	public static function get_client_hints() {
		return self::$client_hints;
	}

	/**
	 * Gets the list of safe client hints.
	 *
	 * @return array client hints.
	 */
	public static function get_safe_client_hints() {
		return self::$safe_client_hints;
	}

	/**
	 * Gets the list of active unsafe client hints.
	 *
	 * @return array client hints.
	 */
	public static function get_unsafe_client_hints() {
		return array_diff( self::$client_hints, self::$safe_client_hints );
	}


	/**
	 * Rewrite image URLs in REST API responses
	 *
	 * @param   mixed            $result  response to replace the requested version with. Can be anything a normal endpoint can return, or null to not hijack the request.
	 * @param   \WP_REST_Server  $server  REST API server instance.
	 * @param   \WP_REST_Request $request request used to generate the response.
	 * @return  array  $data  extended array with links.
	 */
	public static function rewrite_rest_api( $result, $server, $request ) {
		if ( ! ( $result instanceof \WP_REST_Response && is_array( $result->data ) ) ) {
			return $result;
		}

		$rewriter    = self::get_rewriter();
		$url_matcher = $rewriter->generate_regex_for_url();

		$url            = array_key_exists( 'REQUEST_URI', $_SERVER ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$is_woocommerce = strpos( $url, '/wp-json/wc/' ) !== false;

		foreach ( $result->data as &$item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			// Rewrite image URLs for Advanced Custom Fields REST API.
			if ( array_key_exists( 'acf', $item ) ) {
				foreach ( $item['acf'] as &$field ) {
					if ( ! is_array( $field ) ) {
						continue;
					}
					if ( array_key_exists( 'type', $field ) && 'image' === $field['type'] ) {

						// Main image.
						if ( preg_match( $url_matcher, $field['url'] ) ) {
							$field['url'] = $rewriter->rewrite_url( $field['url'] );
						}

						// Icon.
						if ( preg_match( $url_matcher, $field['icon'] ) ) {
							$field['icon'] = $rewriter->rewrite_url( $field['icon'] );
						}

						// Image variants.
						foreach ( $field['sizes'] as &$variant ) {
							if ( is_string( $variant ) && preg_match( $url_matcher, $variant ) ) {
								$variant = $rewriter->rewrite_url( $variant );
							}
						}
					}
				}
			}

			// Rewrite image URLs for WooCommerce REST API.
			if ( $is_woocommerce ) {
				// Product gallery images.
				if ( array_key_exists( 'images', $item ) && is_array( $item['images'] ) ) {
					foreach ( $item['images'] as &$image ) {
						if ( ! is_array( $image ) ) {
							continue;
						}

						if ( array_key_exists( 'src', $image ) && preg_match( $url_matcher, $image['src'] ) ) {
							$image['src'] = $rewriter->rewrite_url( $image['src'] );
						}
					}
				}

				// HTML fragments.
				foreach ( array( 'description', 'short_description', 'price_html' ) as $fragment ) {
					if ( array_key_exists( $fragment, $item ) && is_string( $item[ $fragment ] ) ) {
						$item[ $fragment ] = $rewriter->rewrite( $item[ $fragment ] );
					}
				}
			}
		}

		return $result;
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
		$content_path  = trim( wp_parse_url( content_url(), PHP_URL_PATH ), '/' );
		$includes_path = trim( wp_parse_url( includes_url(), PHP_URL_PATH ), '/' );

		return array(
			'url'        => '',
			'dirs'       => implode( ',', array( $content_path, $includes_path ) ),
			'excludes'   => '.php',
			'relative'   => true,
			'https'      => true,
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
	 * Redirect the user to the settings page after activation.
	 *
	 * @param string $plugin name of the activated plugin.
	 */
	public static function settings_redirect( $plugin ) {
		if ( ! defined( 'IMAGE_CDN_BASE' ) || IMAGE_CDN_BASE !== $plugin ) {
			return;
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'image_cdn' ), admin_url( 'admin.php' ) ) );
		exit;
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
		if ( self::$tests_running ) {
			return self::$test_options;
		}
		return wp_parse_args( get_option( 'image_cdn' ), self::default_options() );
	}

	/**
	 * Return new rewriter.
	 */
	public static function get_rewriter() {
		if ( ! ( self::$rewriter instanceof Rewriter ) ) {
			$options  = self::get_options();
			$excludes = array_map( 'trim', explode( ',', $options['excludes'] ) );

			self::$rewriter = new Rewriter(
				get_option( 'home' ),
				$options['url'],
				$options['dirs'],
				$excludes,
				$options['relative'],
				$options['https'],
				$options['directives']
			);
		}

		return self::$rewriter;
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
	public static function rewrite_html( $html ) {
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

	/**
	 * Rewrite URL.
	 *
	 * @param string $url The URL.
	 */
	public static function rewrite_url( $url ) {
		$rewriter = self::get_rewriter();
		return $rewriter->rewrite_url( $url );
	}

	/**
	 * Notifies the user in the settings page if they are not using ImageEngine.
	 *
	 * @return void
	 */
	public static function ie_admin_notice() {
		$options = self::get_options();
		$img_url = $options['url'] . wp_parse_url( plugin_dir_url( IMAGE_CDN_FILE ), PHP_URL_PATH ) . 'assets/logo.png';
		global $pagenow;

		// check if user is on the settings page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'image_cdn' === $_GET['page'] ) {
			if ( ! $options['enabled'] || false === self::is_server_imageengine( $img_url ) ) {
				?>
				<div class="notice notice-warning">
				<p>
					<?php
					printf(
						// translators: %s is a link to the ImageEngine site.
						esc_html__( 'This plugin is best used with %s, but will also work with most other CDNs.', 'image-cdn' ),
						'<a href="https://imageengine.io/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">ImageEngine</a>'
					);
					?>
				</p>
				<p><?php esc_html_e( 'To obtain an ImageEngine Delivery Address:' ); ?></p>
				<ol>
					<li><a target="_blank" href="https://control.imageengine.io/register/website/?website=<?php echo esc_attr( get_site_url() ); ?>&utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine">Sign up for an ImageEngine account</a></li>
					<li>
						<?php
						printf(
							// translators: 1: http code example 2: https code example.
							esc_html__( 'Enter the assigned ImageEngine Delivery Address (including %1$s or %2$s) in the "Delivery Address" option below.', 'image-cdn' ),
							'<code>http://</code>',
							'<code>https://</code>'
						);
						?>
					</li>
					<?php
					if ( ! $options['enabled'] ) {
						?>
						<li>Enable ImageEngine and hit "Test Configuration" before saving the changes.</li>
						<?php
					}
					?>
				</ol>
				<p>See <a href="https://support.imageengine.io/hc/en-us/articles/360059238371-Quick-Start/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">full documentation.</a></p>
				</div>
				<?php
			} else {
				?>
				<div class="notice notice-success">
					<p>
						<?php
						printf(
							// translators: %s is a the ImageEngine delivery address .
							esc_html__( '%s is a valid ImageEngine delivery address.', 'image-cdn' ),
							'<code>' . esc_html( $options['url'] ) . '</code>'
						);
						?>
					</p>
					<ul>
						<li><a href="https://control.imageengine.io/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">ImageEngine Control Panel</a></li>
						<li><a href="https://support.imageengine.io/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">ImageEngine Documentation</a></li>
					</ul>
				</div>
				<?php
			}
		}
	}

	/**
	 * Returns true if the URL is served by ImageEngine.
	 *
	 * @param string $url URL to be checked.
	 * @return bool whether or not the server is ImageEngine.
	 */
	public static function is_server_imageengine( $url ) {
		$cdn_res = wp_remote_get( $url, array( 'sslverify' => true ) );
		if ( is_wp_error( $cdn_res ) ) {
			return false;
		}

		if ( 'ScientiaMobile ImageEngine' === $cdn_res['headers']['server'] ) {
			return true;
		} else {
			return false;
		}
	}


}
