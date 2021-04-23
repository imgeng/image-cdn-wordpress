<?php
/**
 * Settings page HTML template
 *
 * @package ImageCDN
 */

?>
<div class="wrap">
	<img style="max-width: 400px" src="<?php echo esc_attr( plugin_dir_url( IMAGE_CDN_FILE ) ); ?>assets/logo.svg" />
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

	<form method="post" action="options.php">
		<?php
		var_dump($options);
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

		settings_fields( 'image_cdn' );
		?>
		<input type="hidden" name="image_cdn[url]" id="image_cdn_url" value="<?php echo esc_attr( $options['url'] ); ?>" />
		<input type="hidden" name="image_cdn[https]" id="image_cdn_https" value="<?php echo esc_attr( $is_https_int ); ?>" />

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Delivery Address', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_url" style="border-radius: 4px; border: 1px solid #8c8f94; background-color: #fff; color: #2c3338; margin-top: 0 !important;">
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
							<input type="text" name="image_cdn[host]" id="image_cdn_host" value="<?php echo esc_attr( $url_host ); ?>" size="64" class="regular-text code" style="border: none; border-radius: 0; margin-left: 0; padding-left: 0; vertical-align: middle;" />
						</label>

						<p class="description">
							<?php
							printf(
								// translators: 1: Link to account control panel.
								esc_html__( 'Enter your ImageEngine (or other Image CDN) Delivery Address. For ImageEngine, this can be found in your %1$s. In most cases, this will be like', 'image-cdn' ),
								'<a href="https://my.scientiamobile.com/" target="_blank">account control panel</a>'
							);
							?>
							<code>my-site.cdn.imgeng.in</code>.
						</p>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Enabled', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_enabled">
							<input type="checkbox" name="image_cdn[enabled]" id="image_cdn_enabled" value="1" <?php checked( 1, $options['enabled'] ); ?> />
							<?php esc_html_e( 'Enable ImageEngine', 'image-cdn' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<span id="toggle-advanced" style="cursor: pointer;"><?php esc_html_e( 'Show Advanced Settings ▸', 'image-cdn' ); ?></span>
				</th>
				<td></td>
			</tr>
		</table>

		<div id="ie-advanced" style="max-height: 0; overflow: hidden; transition: 0.2s ease-in-out;">
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
			</table>
		</div>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
			<input type="button" name="check-cdn" id="check-cdn" class="button button-secondary" value="Test Configuration">
		</p>
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
		urlEl.value = schemeEl.value + hostEl.value
		httpsEl.value = 'https://' === schemeEl.value ? '1' : '0'
	}

	schemeEl.addEventListener('change', updateURL)
	hostEl.addEventListener('change', updateURL)

	// Handle the advanced settings panel.
	document.getElementById('toggle-advanced').addEventListener("click", function() {
		const panel = document.getElementById('ie-advanced')
		if (panel.style.maxHeight != '0px') {
			panel.style.maxHeight = '0px'
			this.innerHTML = " <?php esc_html_e( 'Show advanced settings ▸', 'image-cdn' ); ?>"
		} else {
			panel.style.maxHeight = panel.scrollHeight + "px"
			this.innerHTML = " <?php esc_html_e( 'Hide advanced settings ▾', 'image-cdn' ); ?>"
		}
	})
</script>
