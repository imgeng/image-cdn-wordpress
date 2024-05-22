<?php
/**
 * This file contains the Settings class
 *
 * @package ImageCDN
 */

namespace ImageEngine;

use ImageEngine\PhpSdk\IEClient;

/**
 * The Settings class manages all of the validation and administration of the plugin settings.
 */
class Settings {

	public static $_notices = array();

	public static $_client;

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
	 *
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

		if ( ! isset( $data['url'] ) ) {
			add_settings_error( 'url', 'url', 'The Delivery Address is required' );
		} else {
			$parts = wp_parse_url( $data['url'] );
			if ( ! isset( $parts['host'] ) ) {
				add_settings_error( 'url', 'url', 'Delivery Address is required' );
			} elseif ( $parts['host'] === parse_url( get_site_url() )['host'] ) {
				add_settings_error( 'url', 'url', 'You entered the domain of your website. Please enter the ImageEngine delivery address to test the configuration.' );
			} else {
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
	 *
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
	 *
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
		$icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1NTMuODIgNDMwLjgxIiBpZD0ic3ZnX3Jlc2l6ZSIgd2lkdGg9IjY3IiBoZWlnaHQ9IjY3Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PGcgaWQ9IkxheWVyXzIiIGRhdGEtbmFtZT0iTGF5ZXIgMiI+PGcgaWQ9IkxheWVyXzEtMiIgZGF0YS1uYW1lPSJMYXllciAxIj48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0zNzMuMTMsMjIuMTZWNTAuMzNhNiw2LDAsMCwxLTYsNkgzMjIuNzlhNiw2LDAsMCwxLTYtNlYyMy4yQTIwNC42MywyMDQuNjMsMCwwLDAsMTk2LjY0LDg4LjI4aC4yNGE2Ljk0LDYuOTQsMCwwLDEsNi45NCw2Ljk0djQxLjdjMCwyLjQ0LjU1LDEwLDUsMTMuODcsNC4yNCwzLjY2LDEwLjExLDQuMTksMTIuNDIsNC4yNWg1Ni42YTguNDYsOC40NiwwLDAsMSw4LjQ2LDguNDV2NjIuNzdhOC40NSw4LjQ1LDAsMCwxLTguNDYsOC40NUgyMTUuMTFhOC40NSw4LjQ1LDAsMCwxLTguNDUtOC40NVYxNjguMTdBMTQuMjksMTQuMjksMCwwLDAsMTkzLjA5LDE1NGEyMC42OSwyMC42OSwwLDAsMC0yLjc4LS4zMUgxNTYuNzdhMjA0LjU5LDIwNC41OSwwLDAsMC0xMy4wNSw3Mi4xMWMwLDExMy4yNSw5MS44LDIwNS4wNSwyMDUuMDUsMjA1LjA1czIwNS4wNS05MS44LDIwNS4wNS0yMDVDNTUzLjgyLDEyMC43Niw0NzQuODksMzQuMjEsMzczLjEzLDIyLjE2Wm0tODcuMzcsOTBIMjU2LjQzYTQsNCwwLDAsMS00LTRWNzguODdhNCw0LDAsMCwxLDQtNGgyOS4zM2E0LDQsMCwwLDEsMy45NSw0VjEwOC4yQTQsNCwwLDAsMSwyODUuNzYsMTEyLjE1Wk0yMjguMDgsMzAwYTQsNCwwLDAsMS00LDMuOTVIMTk0LjhhNCw0LDAsMCwxLTQtMy45NVYyNzAuNjRhNCw0LDAsMCwxLDQtMy45NWgyOS4zM2E0LDQsMCwwLDEsNCwzLjk1Wm04My40MSw3Mi45YTYsNiwwLDAsMS02LDZIMjYxLjE2YTYsNiwwLDAsMS02LTZWMzI4LjUxYTYsNiwwLDAsMSw2LTZoNDQuMzZhNiw2LDAsMCwxLDYsNlptODguNzUtMjk0YTQsNCwwLDAsMSwzLjk1LTRoMjkuMzNhNCw0LDAsMCwxLDQsNFYxMDguMmE0LDQsMCwwLDEtNCw0SDQwNC4xOWE0LDQsMCwwLDEtMy45NS00Wk0zNzUuODMsMzAwYTQsNCwwLDAsMS0zLjk1LDMuOTVIMzQyLjU1QTQsNCwwLDAsMSwzMzguNiwzMDBWMjcwLjY0YTQsNCwwLDAsMSwzLjk1LTMuOTVoMjkuMzNhNCw0LDAsMCwxLDMuOTUsMy45NVptNzcuNDktNzAuNjVhNS4zOSw1LjM5LDAsMCwxLTUuMzksNS4zOWgtNDBhNS4zOSw1LjM5LDAsMCwxLTUuMzktNS4zOVYyMDkuNzlBNDYsNDYsMCwwLDAsMzU4Ljc0LDE2NGgtOS44MmE0LDQsMCwwLDEtMy45NS0zLjk1VjEzMC43NWE0LDQsMCwwLDEsMy45NS00aDI5LjMzYTQsNCwwLDAsMSwzLjk1LDR2Ny4zNEE0Niw0NiwwLDAsMCw0MjgsMTgzLjg5aDE5LjkyYTUuMzksNS4zOSwwLDAsMSw1LjM5LDUuNFptLTI2My4xMy0xNzNIMTQ1LjgzYTYsNiwwLDAsMS02LTZWNmE2LDYsMCwwLDEsNi02aDQ0LjM2YTYsNiwwLDAsMSw2LDZWNTAuMzNBNiw2LDAsMCwxLDE5MC4xOSw1Ni4zMVpNMTA0LjYzLDMwMy45Mkg3NS4zYTQsNCwwLDAsMS00LTMuOTVWMjcwLjY0YTQsNCwwLDAsMSw0LTMuOTVoMjkuMzNhNCw0LDAsMCwxLDQsMy45NVYzMDBBNCw0LDAsMCwxLDEwNC42MywzMDMuOTJabTAtMTkxLjc3SDc1LjNhNCw0LDAsMCwxLTQtNFY3OC44N2E0LDQsMCwwLDEsNC00aDI5LjMzYTQsNCwwLDAsMSw0LDRWMTA4LjJBNCw0LDAsMCwxLDEwNC42MywxMTIuMTVaTTEwMywxODMuODlIODMuMDVhNDYsNDYsMCwwLDEtNDUuODItNDUuOHYtNy4zNGE0LDQsMCwwLDAtMy45NS00SDRhNCw0LDAsMCwwLTMuOTUsNHYyOS4zM0E0LDQsMCwwLDAsNCwxNjRoOS44MkE0Niw0NiwwLDAsMSw1Ny41MywyMDkuOHYxLjY5aDB2MTcuODNhNS4zOSw1LjM5LDAsMCwwLDUuMzksNS4zOWg0MGE1LjM5LDUuMzksMCwwLDAsNS4zOS01LjM5di00MEE1LjM5LDUuMzksMCwwLDAsMTAzLDE4My44OVoiPjwvcGF0aD48L2c+PC9nPjwvc3ZnPg==';
		add_menu_page( 'Image CDN by ImageEngine', 'ImageEngine', 'manage_options', 'image_cdn', array( self::class, 'settings_page' ), $icon, 100 );
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

						if (!(document.getElementById('image_cdn_enabled').value=="1")) {
							// The user has CDN support disabled.
							recommends.push('image_cdn_enabled')
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

				document.getElementById('check-cdn')?.addEventListener('click', () => {
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

				document.getElementById('recommend-apply')?.addEventListener('click', () => {
					document.querySelectorAll('.recommend-options > li').forEach(el => {
						if (el.classList.contains('hidden')) {
							return
						}

						const target = el.dataset.target
						const value = el.dataset.value

						switch (target) {
							// Checkboxes
							case 'image_cdn_enabled':
								const enabled = document.getElementById(target)
								enabled.value = (value === 'true') ? '1' : '0'
								enabled.dispatchEvent(new Event('change'))
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

		if ( parse_url( $_POST['cdn_url'] ) === parse_url( get_site_url() ) ) {
			$out['message'] = 'You entered the domain of your website. Please enter the ImageEngine delivery address to test the configuration.';
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

		$cdn_server = $cdn_res['headers']['server'];
		if ( strpos( $cdn_server, 'ScientiaMobile ImageEngine' ) === false ) {
			$out['type']    = 'warning';
			$out['message'] = 'The provided delivery address is not served by ImageEngine';
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
	 *
	 * @return string The haystack without the needle prefix, or the original haystack if no match.
	 */
	protected static function remove_prefix( $haystack, $needle ) {
		$has_prefix = substr( $haystack, 0, strlen( $needle ) ) === $needle;
		if ( $has_prefix ) {
			return substr( $haystack, strlen( $needle ) );
		}

		return $haystack;
	}

	/**
	 * Get an instance of the ImageEngine client.
	 */
	public static function client() {
		if ( ! isset( self::$_client ) ) {
			self::$_client = new IEClient( new OptionStorage() );
		}
		return self::$_client;
	}

	/**
	 * Register with ImageEngine API and retrieve Delivery Address.
	 */
	public static function register() {
		$nonce = sanitize_text_field( $_POST['_wpnonce_register'] );
		if ( ! wp_verify_nonce( $nonce, 'image_cdn_register_nonce' ) || ! current_user_can( 'administrator' ) ) {
			self::add_error( __( 'Unauthorized', 'image-cdn' ), 'register' );
			wp_redirect( add_query_arg( 'register-error', 'Unauthorized', admin_url( '/admin.php?page=image_cdn' ) ) );
		}

		if ( ! isset( $_POST['register_username'] ) || ! is_email( $_POST['register_username'] ) ) {
			self::add_error( __( 'Invalid username', 'image-cdn' ), 'register' );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}

		if ( ! isset( $_POST['register_password'] ) ) {
			self::add_error( __( 'Invalid password', 'image-cdn' ), 'register' );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}

		try {
			$response = self::client()->register( $_POST['register_username'], $_POST['register_password'], $_POST['register_plan'] );

			$message = ImageCDN::update_delivery_address( $response );
			if ( is_string( $message ) ) {
				self::add_success( $message );
				wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
			}
		} catch ( \Exception $e ) {
			self::add_error( __( 'An error occurred!', 'image-cdn' ), 'register' );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}
	}

	/**
	 * Login with ImageEngine API and retrieve Delivery Addresses.
	 */
	public static function login() {
		$nonce = sanitize_text_field( $_POST['_wpnonce_login'] );
		if ( ! wp_verify_nonce( $nonce, 'image_cdn_login_nonce' ) || ! current_user_can( 'administrator' ) ) {
			self::add_error( __( 'Unauthorized', 'image-cdn' ), 'login' );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}

		if ( ! isset( $_POST['login_username'] ) || ! is_email( $_POST['login_username'] ) ) {
			self::add_error( __( 'Invalid username', 'image-cdn' ), 'login' );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}

		if ( ! isset( $_POST['login_password'] ) ) {
			self::add_error( __( 'Invalid password', 'image-cdn' ), 'login' );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}

		try {
			$response = self::client()->login( $_POST['login_username'], $_POST['login_password'] );

			$message = ImageCDN::update_delivery_address( $response );
			if ( is_string( $message ) ) {
				self::add_success( $message );
				wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
			}
		} catch ( \Exception $e ) {
			self::add_error( __( 'An error occurred!', 'image-cdn' ), 'login' );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}
	}

	/**
	 * Logout from ImageEngine API.
	 */
	public static function logout() {
		$nonce = sanitize_text_field( $_POST['_wpnonce_logout'] );
		if ( ! wp_verify_nonce( $nonce, 'image_cdn_logout_nonce' ) || ! current_user_can( 'administrator' ) ) {
			self::add_error( __( 'Unauthorized', 'image-cdn' ) );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}

		try {
			self::client()->logout();

			self::add_success( __("Logged out!", 'image-cdn') );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		} catch ( \Exception $e ) {
			self::add_error( __( 'An error occurred!', 'image-cdn' ) );
			wp_redirect( admin_url( '/admin.php?page=image_cdn' ) );
		}
	}

	/**
	 * Add an error message
	 */
	public static function add_error( $text, $tab = null ) {
		self::$_notices[] = [
			'type'    => 'error',
			'message' => $text,
			'tab'     => $tab,
		];
		update_option( 'image_cdn_notices', self::$_notices );
	}

	/**
	 * Add a success message
	 */
	public static function add_success( $text, $tab = null ) {
		self::$_notices[] = [
			'type'    => 'success',
			'message' => $text,
			'tab'     => $tab,
		];
		update_option( 'image_cdn_notices', self::$_notices );
	}

	/**
	 * Get all stored notices
	 */
	public static function notices() {
		self::$_notices = maybe_unserialize( get_option( 'image_cdn_notices' ) );
		return self::$_notices;
	}

	/**
	 * Show any stored error messages
	 */
	public static function admin_notices() {
		$notices = maybe_unserialize( get_option( 'image_cdn_notices' ) );

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				wp_admin_notice( "<b>" . $notice['message'] . "</b>", [
						'type'        => $notice['type'],
					'dismissible' => true
				] );
			}
		}

		// Clear
		delete_option( 'image_cdn_notices' );
	}

	/**
	 * Registers the analytics javascript helpers.
	 */
	public static function register_analytics() {
		if ( ! self::client()->isLoggedIn() ) {
			return;
		}
		$nonce   = wp_create_nonce( 'image-cdn-analytics' );
		$options = ImageCDN::get_options();
		?>
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				const show_analytics = res => {
					// res.type can be 'error', 'warning' or 'success'.
					if (res.type === 'success') {
						document.getElementById('analytics').innerHTML = res.message
						document.getElementById('analytics-tab').dataset.loaded = 'true'
					}
				}

				document.getElementById('analytics-tab')?.addEventListener('click', (el) => {
					if(el.target.dataset.loaded === 'true') {
						return
					}

					document.getElementById('analytics').innerHTML = '<div class="spinner-loader"></div>'

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
							'action': 'image_cdn_analytics',
							'nonce': '<?php echo esc_js( $nonce ); ?>',
							'cdn_url': document.getElementById('image_cdn_url').value,
						}),
					})
						.then(res => res.json())
						.then(res => show_analytics(res.data))
						.catch(err => {
							show_analytics('error', 'unable to load analytics: ' + err)
							console.error(err)
						})
				})

				<?php if (Settings::client()->isLoggedIn() && $options['enabled']): ?>
				document.getElementById('analytics-tab')?.click()
				<?php endif; ?>
			})
		</script>
		<?php
	}

	/**
	 * Display and cache (for 1 h) analytics.
	 */
	public static function analytics() {
		check_ajax_referer( 'image-cdn-analytics', 'nonce' );

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

		$options = ImageCDN::get_options();

		$cname = $options['url'] ?? '';
		$cname = str_replace( 'https://', '', $cname );
		$cname = str_replace( '.imgeng.in', '', $cname );

		$cached      = get_transient( 'image_cdn_analytics_' . $cname );
		$out['type'] = 'success';
		if ( empty( $cached ) ) {
			ob_start();
			include __DIR__ . '/../templates/_analytics.php';
			$out['message'] = ob_get_clean();
			ob_end_clean();
			set_transient( 'image_cdn_analytics_' . $cname, $out['message'], HOUR_IN_SECONDS );
		} else {
			$out['message'] = $cached;
		}
		wp_send_json_success( $out );
	}
}