<?php
/**
 * This file contains the Settings class
 *
 * @package ImageCDN
 */

namespace ImageEngine;

/**
 * The Settings class manages all of the validation and administration of the plugin settings.
 */
class Settings {



	/**
	 * Register settings.
	 */
	public static function register_settings() {
		register_setting( 'image_cdn', 'image_cdn', array( self::class, 'validate_settings' ) );
	}


	/**
	 * Validation of settings.
	 *
	 * @param   array $data  array with form data.
	 * @return  array         array with validated values.
	 */
	public static function validate_settings( $data ) {
		if ( ! isset( $data['relative'] ) ) {
			$data['relative'] = 0;
		}

		if ( ! isset( $data['https'] ) ) {
			$data['https'] = 0;
		}

		$data['relative'] = (bool) $data['relative'];
		$data['https']    = (bool) $data['https'];
		$data['enabled']  = (bool) $data['enabled'];

		$data['url'] = trim( rtrim( $data['url'], '/' ) );

		if ( '' === $data['url'] ) {
			add_settings_error( 'url', 'url', 'The Delivery Address is required' );
		} else {
			$parts = wp_parse_url( $data['url'] );
			if ( ! isset( $parts['scheme'] ) || ! isset( $parts['host'] ) ) {
				add_settings_error( 'url', 'url', 'Delivery Address must begin with <code>http://</code> or <code>https://</code>' );
			} else {

				// Make sure there is a valid scheme.
				if ( ! in_array( $parts['scheme'], array( 'http', 'https' ), true ) ) {
					add_settings_error( 'url', 'url', 'Delivery Address must begin with <code>http://</code> or <code>https://</code>' );
				}

				// Make sure the host is resolves.
				if ( ! filter_var( $parts['host'], FILTER_VALIDATE_IP ) ) {
					$ip = gethostbyname( $parts['host'] );
					if ( $ip === $parts['host'] ) {
						add_settings_error( 'url', 'url', 'Invalid URL: Could not resolve hostname' );
					}
				}
			}
		}

		return array(
			'url'        => esc_url_raw( $data['url'] ),
			'dirs'       => esc_attr( self::clean_list( $data['dirs'] ) ),
			'excludes'   => esc_attr( self::clean_list( $data['excludes'] ) ),
			'relative'   => $data['relative'],
			'https'      => $data['https'],
			'directives' => self::clean_directives( $data['directives'] ),
			'enabled'    => $data['enabled'],
		);
	}

	/**
	 * Cleans a $delimiter-separated list by trimming each element and rejoining them and removing empty and duplicate elements.
	 *
	 * @param string $list list of strings separated by $delimiter.
	 * @param string $delimiter delimiter.
	 * @return string list of strings.
	 */
	public static function clean_list( $list, $delimiter = ',' ) {
		$clean = array();
		foreach ( explode( $delimiter, $list ) as $dir ) {
			$dir = trim( $dir );
			if ( '' === $dir || in_array( $dir, $clean, true ) ) {
				continue;
			}
			$clean[] = $dir;
		}
		return implode( $delimiter, $clean );
	}

	/**
	 * Clean the ImageEngine Directives.
	 *
	 * @param string $directives ImageEngine Directives as a comma-separated list.
	 * @return string ImageEngine Directives.
	 */
	public static function clean_directives( $directives ) {
		$directives = preg_replace( '#.*imgeng=/+?#', '', $directives );
		$directives = trim( $directives );

		// Ensure there is one leading "/" and none trailing.
		$directives = trim( $directives, '/' );
		$directives = '/' . $directives;
		$directives = rtrim( $directives, '/' );

		return $directives;
	}


	/**
	 * Add settings page.
	 */
	public static function add_settings_page() {
		$page = add_options_page( 'Image CDN', 'Image CDN', 'manage_options', 'image_cdn', array( self::class, 'settings_page' ) );
	}


	/**
	 * Settings page.
	 */
	public static function settings_page() {
		$options     = ImageCDN::get_options();
		$defaults    = ImageCDN::default_options();
		$is_runnable = ImageCDN::should_rewrite();
		include __DIR__ . '/../templates/settings.php';
	}


	/**
	 * Registers the configuration test javascript helpers.
	 */
	public static function register_test_config() {
		$nonce = wp_create_nonce( 'image-cdn-test-config' );
		?>
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				const show_test_results = res => {
					// res.type can be 'error', 'warning' or 'success'.

					if (res.type === 'success') {
						// Recommendations to show the user.
						recommends = []

						if (!document.getElementById('image_cdn_enabled').checked) {
							// The user has CDN support disabled.
							recommends.push('image_cdn_enabled')
						}

						if (document.getElementById('image_cdn_url').value.match(/^https:/) && !document.getElementById('image_cdn_https').checked) {
							// The user has an HTTPS CDN URL but doesn't have HTTPS enabled.
							recommends.push('image_cdn_https')
						}

						// If there are recommendations to be made, show them.
						if (recommends.length > 0) {
							document.getElementById('recommend-section').classList.remove('hidden')
							document.querySelectorAll('.recommend-options > li').forEach(el => {
								if (recommends.includes(el.dataset.target)) {
									el.classList.remove('hidden')
								} else {
									el.classList.add('hidden')
								}
							})
						} else {
							// No recommendations, hide this section
							document.getElementById('recommend-section').classList.add('hidden')
						}
					}

					const class_name = 'notice-' + res.type

					document.querySelectorAll('.image-cdn-test').forEach(el => {
						if (!el.classList.contains(class_name)) {
							el.classList.add('hidden')
							return
						}

						el.classList.remove('hidden')
						if (res.type == 'warning' || res.type == 'error') {
							el.querySelector('.image-cdn-result').innerHTML = res.message
							el.querySelector('.image-cdn-local-url').innerHTML = res.local_url
							el.querySelector('.image-cdn-remote-url').innerHTML = res.cdn_url
						}
					})
				}

				document.getElementById('check-cdn').addEventListener('click', () => {
					show_test_results({
						'type': 'info'
					})

					window.scrollTo({
						top: 50,
						left: 0,
						behavior: 'smooth',
					})

					fetch(ajaxurl, {
							method: 'POST',
							credentials: 'same-origin',
							headers: new Headers({
								'accept': 'application/json',
								'content-type': 'application/x-www-form-urlencoded; charset=utf-8',
							}),
							body: new URLSearchParams({
								'action': 'image_cdn_test_config',
								'nonce': '<?php echo esc_js( $nonce ); ?>',
								'cdn_url': document.getElementById('image_cdn_url').value,
							}),
						})
						.then(res => res.json())
						.then(res => show_test_results(res.data))
						.catch(err => {
							show_test_results('error', 'unable to start test: ' + err)
							console.error(err)
						})
				})

				document.getElementById('recommend-apply').addEventListener('click', () => {
					document.querySelectorAll('.recommend-options > li').forEach(el => {
						if (el.classList.contains('hidden')) {
							return
						}

						const target = el.dataset.target
						const value = el.dataset.value

						switch (target) {
							// Checkboxes
							case 'image_cdn_enabled':
							case 'image_cdn_https':
								document.getElementById(target).checked = (value === 'true')
								break
							default:
								console.error(`Invalid recommendation target: ${target}`)
						}

						// Save the changes with a slight delay so you can see the checkbox(es)
						// being checked before the page refreshes.
						setTimeout(() => document.getElementById('submit').click(), 500)
					})
				})
			})
		</script>
		<?php
	}

	/**
	 * Runs the configuration test.
	 */
	public static function test_config() {
		check_ajax_referer( 'image-cdn-test-config', 'nonce' );

		$out = array(
			'type'      => 'error',
			'message'   => '',
			'local_url' => '',
			'cdn_url'   => '',
		);

		if ( ! isset( $_POST['cdn_url'] ) ) {
			$out['message'] = 'Malformed request';
			wp_send_json_error( $out );
		}

		// Make sure we can fetch this content from the local WordPress installation and via the CDN.
		$asset        = 'assets/logo.svg';
		$local_url    = plugin_dir_url( IMAGE_CDN_FILE ) . $asset;
		$cdn_base_url = trim( esc_url_raw( wp_unslash( $_POST['cdn_url'] ) ), '/' );

		$plugin_path = wp_parse_url( plugin_dir_url( IMAGE_CDN_FILE ), PHP_URL_PATH );

		$home_path   = trim( wp_parse_url( get_option( 'home' ), PHP_URL_PATH ), '/' );
		$plugin_path = trim( self::remove_prefix( $plugin_path, $home_path ), '/' );

		$parts = array(
			$cdn_base_url,
			$plugin_path,
			$asset,
		);

		$clean_parts = array();
		foreach ( $parts as $part ) {
			$part = trim( $part, '/' );
			if ( ! empty( $part ) ) {
				$clean_parts[] = $part;
			}
		}

		$cdn_url = implode( '/', $clean_parts );

		$out['home_path']   = $home_path;
		$out['plugin_path'] = $plugin_path;

		$out['local_url'] = $local_url;
		$out['cdn_url']   = $cdn_url;

		$local_res = wp_remote_get( $local_url, array( 'sslverify' => false ) );
		if ( is_wp_error( $local_res ) ) {
			$out['message'] = 'Unable to find a local resource to test: ' . $local_res->get_error_message();
			wp_send_json_error( $out );
		}

		if ( $local_res['response']['code'] >= 400 ) {
			$out['message'] = 'Unable to find a local resource to test: server responded with HTTP ' . $local_res['response']['code'];
			wp_send_json_error( $out );
		}

		$cdn_res = wp_remote_get( $cdn_url, array( 'sslverify' => true ) );
		if ( is_wp_error( $cdn_res ) ) {
			$out['message'] = 'Unable to fetch the URL through the CDN: ' . $cdn_res->get_error_message();
			wp_send_json_error( $out );
		}

		if ( 400 <= $cdn_res['response']['code'] ) {
			if ( 502 === $cdn_res['response']['code'] ) {
				if ( array_key_exists( 'x-origin-status', $cdn_res['headers'] ) ) {
					$status         = $cdn_res['headers']['x-origin-status'];
					$out['message'] = "Unable to fetch the URL through the CDN: server responded with HTTP $status";

					if ( array_key_exists( 'x-origin-reason', $cdn_res['headers'] ) ) {
						$reason          = $cdn_res['headers']['x-origin-reason'];
						$out['message'] .= " $reason";
					}
					wp_send_json_error( $out );
				}
			}

			$out['message'] = 'Unable to fetch the URL through the CDN: server responded with HTTP ' . $cdn_res['response']['code'];
			wp_send_json_error( $out );
		}

		if ( ! isset( $cdn_res['headers']['content-type'] ) ) {
			$out['type']    = 'warning';
			$out['message'] = 'Unable to confirm that the CDN is working properly because it didn\'t send Content-Type';
			wp_send_json_error( $out );
		}

		$cdn_type = $cdn_res['headers']['content-type'];
		if ( strpos( $cdn_type, 'image/svg' ) === false ) {
			$out['type']    = 'error';
			$out['message'] = "CDN returned the wrong content type (expected 'image/svg', got '$cdn_type')";
			wp_send_json_error( $out );
		}

		/**
		 * This check it commented out until we can confirm that it properly tests CORS functionality.
		 *
		 * $unsafe_hints = ImageCDN::get_unsafe_client_hints();
		 * if ( 0 < count( $unsafe_hints ) ) {
		 *     $cors_error = true;
		 *     if ( ! array_key_exists( 'access-control-allowed-headers', $cdn_res['headers'] ) ) {
		 *         $out['type']    = 'warning';
		 *         $out['message'] = 'Unable to confirm that the CDN supports client-hints because it didn\'t send Access-Control-Allow-Headers.  Fonts may not work if served from the CDN.';
		 *         wp_send_json_error( $out );
		 *     }
		 *
		 *     $allowed       = preg_split( '/ +/', trim( $cdn_res['headers']['access-control-allowed-headers'] ) );
		 *     $missing_hints = array_diff( $unsafe_hints, $allowed );
		 *     if ( 0 < count( $missing_hints ) ) {
		 *         $out['type']    = 'warning';
		 *         $out['message'] = 'Unable to confirm that the CDN supports advanced client-hints because it is missing some active client-hints in Access-Control-Allow-Headers (' . implode( ',', $missing_hints ) . ').  Fonts may not work if served from the CDN.';
		 *         wp_send_json_error( $out );
		 *     }
		 * }
		 */

		$out['type']    = 'success';
		$out['message'] = 'Test successful';
		wp_send_json_success( $out );
	}

	/**
	 * Removes the given prefix (needle) from the haystack.
	 *
	 * @param string $haystack The string which is to have it's prefix removed.
	 * @param string $needle the prefix to be removed.
	 * @return string The haystack without the needle prefix, or the original haystack if no match.
	 */
	protected static function remove_prefix( $haystack, $needle ) {
		$has_prefix = substr( $haystack, 0, strlen( $needle ) ) === $needle;
		if ( $has_prefix ) {
			return substr( $haystack, strlen( $needle ) );
		}

		return $haystack;
	}
}
