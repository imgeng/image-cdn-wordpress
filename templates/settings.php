<?php
/**
 * Settings page HTML template
 *
 * @package ImageCDN
 */

use ImageEngine\Settings;

?>

<style>
	.tab-container {
		.tab-content {
			display: none;
			padding: 20px 16px;

			&.active {
				display: block;
			}

			p {
				margin-top: 0;
				font-size: 14px;
			}
		}
	}

	@media screen and (max-width: 782px) {
		.form-table td select {
			width: auto !important;
		}

		#login_username, #login_passowrd, #register_username, #register_passowrd {
			width: 100%;
			margin-bottom: 10px;
		}
	}

	.button .button-loader {
		display: none;
	}
	.button:disabled .button-loader {
		display: inline-flex;
	}
	.button-loader {
		margin-right: 5px;
		width: 12px;
		height: 12px;
		border: 5px solid #cacaca;
		border-bottom-color: transparent;
		border-radius: 50%;
		display: inline-block;
		box-sizing: border-box;
		animation: rotation 1s linear infinite;
	}

	.spinner-loader {
		margin-bottom: 20px;
		width: 36px;
		height: 36px;
		border: 5px solid #32325d;
		border-bottom-color: transparent;
		border-radius: 50%;
		display: inline-block;
		box-sizing: border-box;
		animation: rotation 1s linear infinite;
	}
	@keyframes rotation {
		0% {
			transform: rotate(0deg);
		}
		100% {
			transform: rotate(360deg);
		}
	}


</style>

<div class="wrap">
	<img style="max-width: 300px" src="<?php echo esc_attr( plugin_dir_url( IMAGE_CDN_FILE ) ); ?>assets/logo.svg" />
	<h2>
		<?php
		printf(
			// translators: %s is the plugin version number.
			'Image CDN by ImageEngine Settings (version %s)',
			esc_attr( IMAGE_CDN_VERSION )
		)
		?>
	</h2>
	<?php if ( $options['enabled'] && ! $is_runnable ) { ?>
		<div class="notice notice-error">
			<p>
				<?php esc_html_e( 'ImageEngine is disabled because there is something wrong with your configuration.  Please verify the URL below.', 'image-cdn' ); ?>
			</p>
		</div>
	<?php } ?>
	<?php settings_errors(); ?>

	<?php if ( Settings::client()->isLoggedIn() ) : ?>
		<?php
		if ( is_array( $notices = Settings::notices() ) ) {
			$pleas_chose_notice = array_filter(
				$notices,
				function ( $notice ) {
				return $notice['type'] == 'success' && $notice['message'] == __("Please choose a delivery address bellow!", "image-cdn");
			});
		}
		if ( ! empty( $pleas_chose_notice ) ) {
			?>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				setTimeout(() => {
					toggleDropdown();
				}, 100);
			});
		</script>
			<?php
		}

		Settings::admin_notices();
		?>
		<form method="post" id="logout-form" action="/wp-admin/admin-post.php">
			<input type="hidden" name="action" value="logout">
			<?php wp_nonce_field( 'image_cdn_logout_nonce', '_wpnonce_logout' ); ?>
		</form>
	<?php else : ?>

		<div class="tab-container">
			<?php
			$tabA = $options['url'] && $options['enabled'] ? 'login' : 'register';

			if ( is_array( $notices = Settings::notices() ) ) {
				$notice = array_filter(
					$notices,
					function ( $notice ) {
						return $notice['type'] == 'error' && ! empty( $notice['tab'] );
					}
				);
			}

			$tabA = ! empty( $notice ) ? $notice[0]['tab'] : $tabA;

			Settings::admin_notices();
			?>
			<nav class="nav-tab-wrapper wp-clearfix">
				<a href="#" class="nav-tab <?php echo $tabA == "register" ? "nav-tab-active" : "" ?>"
				   data-tab="register"><?php esc_html_e("Claim you Delivery Address", "image-cdn") ?></a>
				<a href="#" class="nav-tab <?php echo $tabA == "login" ? "nav-tab-active" : "" ?>"
				   data-tab="login"><?php esc_html_e("Log in if you already got a Delivery Address", "image-cdn") ?></a>
			</nav>

			<div class="tab-content <?php echo $tabA == "register" ? "active" : "" ?>" data-tab="register">

				<p><?php echo sprintf(__("If you don't already have an ImageEngine account, claim your Delivery Address
					for %s and get optimized in minutes!", "image-cdn"), get_home_url()) ?></p>

	<form method="post" action="/wp-admin/admin-post.php" >
					<?php wp_nonce_field( 'image_cdn_register_nonce', '_wpnonce_register' ); ?>

					<input type="hidden" name="action" value="register"/>

					<input type="email" name="register_username" id="register_username"
						   placeholder="<?php esc_html_e("Email", "image-cdn") ?>"/>
					<input type="password" name="register_password" id="register_passowrd"
						   placeholder="<?php esc_html_e("Password", "image-cdn") ?>"/>

					<div style="height: 1em;"></div>

					<p style="margin-bottom: 10px;"><?php echo sprintf(__("Select plan: (%s)", "image-cdn"),
						'<a href="https://imageengine.io/pricing/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" 
										target="_blank">'.__("Learn more", "image-cdn").'</a>') ?></p>

					<div style="margin-bottom: 5px;">
						<label>
							<input type="radio" name="register_plan" id="register_plan_trial" value="trial" checked="checked" />
							<?php echo __("Free trial for 30 days, no commitments.", "image-cdn") ?>
						</label>
					</div>

					<div style="margin-bottom: 18px;">
						<label>
							<input type="radio" name="register_plan" id="register_plan_free" value="free" />
							<?php echo __("Free forever for developers(max 10GB per month, no commercial use)", "image-cdn") ?>
						</label>
					</div>

					<button type="submit" name="register" id="register" class="button button-primary" onclick="this.disabled = true; this.parentElement.submit();"
						><span class="button-loader"></span><?php esc_html_e("Register", "image-cdn") ?></button>
				</form>
			</div>
			<div class="tab-content <?php echo $tabA == "login" ? "active" : "" ?>" data-tab="login">

				<?php if ( $options['url'] && $options['enabled'] ) : ?>
					<p><?php echo __("If you have an ImageEngine account, login here to view your stats!", "image-cdn") ?></p>
				<?php else : ?>
					<p><?php echo __("If you have an ImageEngine account, login here to get you Delivery Address!", "image-cdn") ?></p>
				<?php endif; ?>

				<form method="post" action="/wp-admin/admin-post.php">

					<?php wp_nonce_field( 'image_cdn_login_nonce', '_wpnonce_login' ); ?>

					<input type="hidden" name="action" value="login"/>

					<input type="email" name="login_username" id="login_username"
						   placeholder="<?php esc_html_e("Email", "image-cdn") ?>"/>
					<input type="password" name="login_password" id="login_passowrd"
						   placeholder="<?php esc_html_e("Password", "image-cdn") ?>"/>

					<button type="submit" name="login" id="login" class="button button-primary" onclick="this.disabled = true; this.parentElement.submit();"
						><span class="button-loader"></span><?php esc_html_e("Login", "image-cdn") ?></button>
	</form>
			</div>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<div class="tab-container">
			<?php
			$tabB = Settings::client()->isLoggedIn() && $options['enabled'] ? 'analytics' : 'setup';
			?>
			<nav class="nav-tab-wrapper wp-clearfix">
				<a href="#" class="nav-tab <?php echo $tabB == "setup" ? "nav-tab-active" : "" ?>"
				   data-tab="setup"><?php esc_html_e("Setup", "image-cdn") ?></a>
				<?php if ( Settings::client()->isLoggedIn() ) : ?>
					<a href="#" id="analytics-tab" class="nav-tab <?php echo $tabB == "analytics" ? "nav-tab-active" : "" ?>"
					   data-tab="analytics"><?php esc_html_e("Analytics", "image-cdn") ?></a>
				<?php endif; ?>
				<a href="#" class="nav-tab <?php echo $tabB == "advanced" ? "nav-tab-active" : "" ?>"
				   data-tab="advanced"><?php esc_html_e("Advanced", "image-cdn") ?></a>
			</nav>

		<?php
		if ( $options['url'] ) {
			$parts    = wp_parse_url( $options['url'] );
			$url_host = $parts['host'];
			if ( ! empty( $parts['port'] ) ) {
				$url_host .= ':' . $parts['port'];
			}

			if ( ! empty( $parts['path'] ) ) {
				$url_host .= '/' . trim( $parts['path'], '/' );
			}

			$url_scheme = $parts['scheme'];
			if ( empty( $url_scheme ) ) {
				$url_scheme = 'https';
			}
			$is_https_int     = 'https' === $url_scheme ? '1' : '0';
			$options['https'] = $is_https_int;
		} else {
			$url_scheme   = is_ssl() ? 'https' : 'http';
			$is_https_int = 'https' === $url_scheme ? '1' : '0';
		}

		settings_fields( 'image_cdn' );
		?>
		<input type="hidden" name="image_cdn[url]" id="image_cdn_url" value="<?php echo esc_attr( $options['url'] ); ?>" />
		<input type="hidden" name="image_cdn[https]" id="image_cdn_https" value="<?php echo esc_attr( $is_https_int ); ?>" />

			<div class="tab-content <?php echo $tabB == "setup" ? "active" : "" ?>" data-tab="setup">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Delivery Address', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
								<label for="image_cdn_url"
										style="position: relative; display: inline-flex; width: auto; border-radius: 4px; border: 1px solid #8c8f94; background-color: #fff; color: #2c3338; margin-top: 0 !important;">
							<select name="image_cdn[scheme]" id="image_cdn_scheme" class="code" style="border: none; padding: 0px 0px 0px 20px; margin: 0px 0px 0px 5px; background-position: left; border-radius: 0;">
								<option value="https://"
								<?php
								if ( 'https' === $url_scheme ) {
									echo 'selected';
								}
								?>
								>https://</option>
								<option value="http://"
								<?php
								if ( 'http' === $url_scheme ) {
									echo 'selected';
								}
								?>
								>http://</option>
							</select>

									<?php

									try {
										$delivery_addresses = Settings::client()->getStoredDeliveryAddresses();
									} catch ( Exception $e ) {
										$delivery_addresses = null;
									}

									if ( is_array( $delivery_addresses ) && count( $delivery_addresses ) > 1 ) :
										?>
										<input type="text" name="image_cdn[host]" id="image_cdn_host"
											   value="<?php echo esc_attr( $url_host ?? "" ); ?>" size="64"
												class="regular-text code"
												style="border: none; border-radius: 0; margin-left: 0; padding-left: 0; vertical-align: middle;"/>
										<div id="image_cdn_host_container">
											<select name="image_cdn[host]" id="image_cdn_host_select" class="code"
													style="display: none; border: none; width: 350px; padding: 0px 20px 0px 0px; margin: 0px 0px 0px 0px;
											background-position: right 5px center; border-radius: 0;">
												<option value=""><?php echo esc_html( __( 'Show registered delivery addresses', 'image-cdn' ) ); ?></option>
												<?php
												foreach ( $delivery_addresses as $addr ) :
													$addr .= ".imgeng.in";
													?>
													<option value="<?php echo esc_attr( $addr ) ?>"
														<?php
														if ( ! empty( $url_host ) && $addr === $url_host ) {
															echo 'selected';
														}
														?>
													><?php echo esc_html( $addr ) ?>
													</option>
												<?php endforeach; ?>
											</select>
											<?php require_once("_custom_dropdown.php"); ?>

										</div>
									<?php else : ?>
										<input type="text" name="image_cdn[host]" id="image_cdn_host"
											   value="<?php echo esc_attr($url_host ?? ""); ?>" size="64"
												class="regular-text code"
												style="border: none; border-radius: 0; margin-left: 0; padding-left: 0; vertical-align: middle;"/>
									<?php endif; ?>


									<select name="image_cdn[enabled]" id="image_cdn_enabled" class="code"
											style="border: none; padding: 0px 25px 0px 10px; margin: 0px 0px 0px 0px;
													color: #fff;
													background: <?php echo $options['enabled'] ? 'green' : 'gray' ?> url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%206l5%205%205-5%202%201-7%207-7-7%202-1z%22%20fill%3D%22%23FFF%22%2F%3E%3C%2Fsvg%3E') no-repeat right 5px top 55%;
													border-top-right-radius: 3px;
													border-bottom-right-radius: 3px;">
										<option value="1"
							<?php
							if ( $options['enabled'] ) {
								echo 'selected';
							}
							?>
										><?php echo __( 'Enabled', 'image-cdn' ); ?>
										</option>
										<option value="0"

											<?php
											if ( ! $options['enabled'] ) {
												echo 'selected';
											}
											?>
										><?php echo __( 'Disabled', 'image-cdn' ); ?>
										</option>
									</select>
						</label>
					</fieldset>
				</td>
			</tr>

		</table>
			</div>

			<?php if ( Settings::client()->isLoggedIn() ) : ?>
				<div class="tab-content <?php echo $tabB == "analytics" ? "active" : "" ?>" data-tab="analytics">

					<div id="analytics">
						<?php
						$cname  = $options['url'] ?? '';
						$cname  = str_replace( 'https://', '', $cname );
						$cname  = str_replace( '.imgeng.in', '', $cname );
						$cached = get_transient( 'image_cdn_analytics_' . $cname );
						if ( $cached ) {
							echo $cached;
							?>
							<script>
								document.addEventListener('DOMContentLoaded', function () {
									const analyticsTab = document.getElementById('analytics-tab')
									if (analyticsTab) {
										analyticsTab.dataset.loaded = 'true';
									}
								});
							</script>
							<?php
						}
						// require_once('_analytics.php');
						?>
					</div>
					<p>Find more information on <a href="https://control.imageengine.io?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">control.imageengine.io</a>.
					</p>
				</div>
			<?php endif; ?>

			<div class="tab-content <?php echo $tabB == "advanced" ? "active" : "" ?>" data-tab="advanced">

			<p>Please contact us at <a href="mailto:support@imageengine.io?subject=Assitance required with <?php echo esc_attr( $options['url'] ); ?>">support@imageengine.io</a> for help with these settings.</p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<?php esc_html_e( 'Included Directories', 'image-cdn' ); ?>
					</th>
					<td>
						<fieldset>
							<label for="image_cdn_dirs">
								<input type="text" name="image_cdn[dirs]" id="image_cdn_dirs" value="<?php echo esc_attr( $options['dirs'] ); ?>" size="64" class="regular-text code" />
								<?php esc_html_e( 'Default:', 'image-cdn' ); ?> <code><?php echo esc_html( $defaults['dirs'] ); ?></code>
							</label>

							<p class="description">
								<?php esc_html_e( 'Assets in these directories will be served by the CDN. Enter the directories separated by', 'image-cdn' ); ?> <code>,</code>
							</p>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php esc_html_e( 'Exclusions', 'image-cdn' ); ?>
					</th>
					<td>
						<fieldset>
							<label for="image_cdn_excludes">
								<input type="text" name="image_cdn[excludes]" id="image_cdn_excludes" value="<?php echo esc_attr( $options['excludes'] ); ?>" size="64" class="regular-text code" />
								<?php esc_html_e( 'Default', 'image-cdn' ); ?>: <code>.php</code>
							</label>

							<p class="description">
								<?php esc_html_e( 'Enter the exclusions (directories or extensions) separated by', 'image-cdn' ); ?> <code>,</code>
							</p>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php esc_html_e( 'Relative Path', 'image-cdn' ); ?>
					</th>
					<td>
						<fieldset>
							<label for="image_cdn_relative">
								<input type="checkbox" name="image_cdn[relative]" id="image_cdn_relative" value="1" <?php checked( 1, $options['relative'] ); ?> />
								<?php esc_html_e( 'Enable CDN for relative paths (default: enabled).', 'image-cdn' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php esc_html_e( 'ImageEngine Directives', 'image-cdn' ); ?>
					</th>
					<td>
						<fieldset>
							<label for="image_cdn_directives">
								<input type="text" name="image_cdn[directives]" id="image_cdn_directives" value="<?php echo esc_attr( $options['directives'] ); ?>" size="64" class="regular-text code" />
								<?php esc_html_e( 'Optional', 'image-cdn' ); ?>
							</label>

							<p class="description">
								<?php
								echo wp_kses(
									sprintf(
										// translators: %s URL to ImageEngine directives.
										__(
											'Enter the <a href="%s" target="_blank">ImageEngine Directives</a> to apply to all images.',
											'image-cdn'
										),
										esc_url( 'https://imageengine.io/docs/implementation/directives/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine' )
									),
									array( 'a' )
								);
								?>

								<?php esc_html_e( 'Example', 'image-cdn' ); ?>: <code>/cmpr_10/s_0</code> (<?php esc_html_e( 'sets the compression to 10% and disables sharpening', 'image-cdn' ); ?>)
							</p>
						</fieldset>
					</td>
				</tr>

					<?php if ( Settings::client()->isLoggedIn() ) : ?>
						<tr valign="top">
							<th scope="row">
								<?php esc_html_e( 'ImageEngine ApiKey', 'image-cdn' ); ?>
							</th>
							<td>
								<b><?php echo Settings::client()->getObfuscatedApiKey(); ?></b>
							</td>
						</tr>
					<?php endif; ?>

			</table>
		</div>
		<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary"
						value="<?php esc_html_e( 'Save Changes', 'image-cdn' ); ?>">
				<input type="button" name="check-cdn" id="check-cdn" class="button button-secondary"
						value="<?php esc_html_e( 'Test Configuration', 'image-cdn' ); ?>">
				<?php if ( Settings::client()->isLoggedIn() ) : ?>
					<input type="button" name="logout" id="logout" class="button button-secondary"
							value="<?php esc_html_e( 'Logout', 'image-cdn' ); ?>">
				<?php endif; ?>
		</p>
		</div>
	</form>
</div>

<div class="image-cdn-test notice notice-info hidden">
	<h3><?php esc_html_e( 'Test Results', 'image-cdn' ); ?></h3>
	<p><?php esc_html_e( 'Testing CDN configuration', 'image-cdn' ); ?> ...</p>
</div>
<div class="image-cdn-test notice notice-success hidden">
	<h3><?php esc_html_e( 'Test Results', 'image-cdn' ); ?></h3>
	<p><?php esc_html_e( 'Configuration test successful!', 'image-cdn' ); ?></p>
	<div class="hidden" id="recommend-section">
		<strong>The following changes are recommended:</strong>
		<ul class="ul-disc recommend-options">
			<li class="hidden" data-target="image_cdn_enabled" data-value="true"><?php esc_html_e( 'Enable CDN Support', 'image-cdn' ); ?></li>
			<li class="hidden" data-target="image_cdn_https" data-value="true"><?php esc_html_e( 'Enable HTTPS Support', 'image-cdn' ); ?></li>
		</ul>
		<input type="button" name="recommend-apply" id="recommend-apply" class="button button-secondary" value="Apply Changes">
		<p> </p>
	</div>
</div>
<div class="image-cdn-test notice notice-warning hidden">
	<h3><?php esc_html_e( 'Test Results', 'image-cdn' ); ?></h3>
	<p>
		<?php esc_html_e( 'Configuration test', 'image-cdn' ); ?> <?php esc_html_e( 'warning', 'image-cdn' ); ?>: <em class="image-cdn-result"></em><br>
		<?php esc_html_e( 'Local Test URL', 'image-cdn' ); ?>: <code class="image-cdn-local-url"></code><br>
		<?php esc_html_e( 'CDN Test URL', 'image-cdn' ); ?>: <code class="image-cdn-remote-url"></code>
	</p>
</div>
<div class="image-cdn-test notice notice-error hidden">
	<h3><?php esc_html_e( 'Test Results', 'image-cdn' ); ?></h3>
	<p>
		<?php esc_html_e( 'Configuration test', 'image-cdn' ); ?> <?php esc_html_e( 'failed', 'image-cdn' ); ?>: <em class="image-cdn-result"></em><br>
		<?php esc_html_e( 'Local Test URL', 'image-cdn' ); ?>: <code class="image-cdn-local-url"></code><br>
		<?php esc_html_e( 'CDN Test URL', 'image-cdn' ); ?>: <code class="image-cdn-remote-url"></code>
	</p>
</div>
<script>
	// Handle the dynamically-updated form fields.
	const schemeEl = document.getElementById('image_cdn_scheme')
	const hostEl = document.getElementById('image_cdn_host')
	const urlEl = document.getElementById('image_cdn_url')
	const httpsEl = document.getElementById('image_cdn_https')

	const updateURL = () => {
		urlEl.value = ''
		hostEl.value = hostEl.value.trim()
		if ( '' != hostEl.value ) {
			urlEl.value = schemeEl.value + hostEl.value
		}
		httpsEl.value = 'https://' === schemeEl.value ? '1' : '0'
	}

	schemeEl.addEventListener('change', updateURL)
	hostEl.addEventListener('change', updateURL)

	//logout
	const logout = document.getElementById('logout');
	if (logout) {
		document.getElementById('logout').addEventListener("click", function () {
			document.getElementById('logout-form').submit();
		});
		}

	//tabs
	document.querySelectorAll('.nav-tab').forEach(tab => {
		tab.addEventListener('click', function (e) {
			e.preventDefault();
			container = tab.closest('.tab-container');
			container.querySelectorAll('.nav-tab').forEach(nt => nt.classList.remove('nav-tab-active'));
			container.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
			this.classList.add('nav-tab-active');
			container.querySelector('.tab-content[data-tab=' + this.dataset.tab + ']').classList.add('active');
		})
	})

	//ebable disable
	document.getElementById("image_cdn_enabled").addEventListener("change", function () {
		this.style.backgroundColor = this.value == "1" ? "green" : "gray";
	})
</script>
