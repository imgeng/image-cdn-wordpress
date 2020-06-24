<?php
/**
 * Settings page HTML template
 *
 * @package ImageCDN
 */

?>
<div class="wrap">
	<img src="<?php echo esc_attr( plugin_dir_url( IMAGE_CDN_FILE ) ); ?>assets/logo.png" />
	<div class="notice notice-info">
		<p>
			<?php
			printf(
				// translators: %s is a link to the ImageEngine site.
				esc_html__( 'This plugin is best used with %s, but will also work with most other CDNs.', 'image-cdn' ),
				'<a href="https://imageengine.io/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">ImageEngine</a>'
			);
			?>
		</p>
		<p><?php esc_html_e( 'To obtain an ImageEngine CDN hostname' ); ?>:</p>
		<ol>
			<li><a target="_blank" href="https://imageengine.io/signup/?website=<?php echo esc_attr( rawurlencode( get_site_url() ) ); ?>&?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine">Sign up for an ImageEngine account</a></li>
			<li>
			<?php
				printf(
					// translators: 1: http code example 2: https code example.
					esc_html__( 'Enter the assigned ImageEngine hostname (including %1$s or %2$s) in the "CDN URL" option below.', 'image-cdn' ),
					'<code>http://</code>',
					'<code>https://</code>'
				);
				?>
			</li>
		</ol>
		<p>See <a href="https://imageengine.io/docs/setup/quick-start/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">full documentation.</a></p>
	</div>
	<h2><?php esc_html_e( 'Image CDN Settings', 'image-cdn' ); ?></h2>
	<?php if ( $options['enabled'] && ! $is_runnable ) { ?>
		<div class="notice notice-error">
			<p>
				<?php esc_html_e( 'Image CDN support is disabled because there is something wrong with your configuration.  Please verify the URL below.', 'image-cdn' ); ?>
			</p>
		</div>
	<?php } ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'image_cdn' ); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Enabled', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_enabled">
							<input type="checkbox" name="image_cdn[enabled]" id="image_cdn_enabled" value="1" <?php checked( 1, $options['enabled'] ); ?> />
							<?php esc_html_e( 'Enable CDN support.', 'image-cdn' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'CDN URL', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_url">
							<input type="text" name="image_cdn[url]" id="image_cdn_url" value="<?php echo esc_attr( $options['url'] ); ?>" size="64" class="regular-text code" />
						</label>

						<p class="description">
							<?php
							printf(
								// translators: 1: Link to account control panel
								esc_html__( 'Enter your ImageEngine (or other Image CDN) URL. For ImageEngine, this can be found in your %1$s. In most cases, this will be a scheme and a hostname, like', 'image-cdn' ),
								'<a href="https://my.scientiamobile.com/" target="_blank">account control panel</a>'
							);
							?>
							<code>https://my-site.cdn.imgeng.in</code>.
						</p>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'WordPress URL Path', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_path">
							<input type="text" name="image_cdn[path]" id="image_cdn_path" value="<?php echo esc_attr( $options['path'] ); ?>" size="64" class="regular-text code" />
							<?php esc_html_e( 'Optional', 'image-cdn' ); ?>
						</label>

						<p class="description">
							<?php
							printf(
								// translators: 1: URL code example 2: Path component code example.
								esc_html__( 'Path/subdirectory that WordPress is installed at.  For example, if WordPress is installed at %1$s, you would enter %2$s.  This is normally auto-detected properly, and is usually empty.', 'image-cdn' ),
								'<code>https://foo.bar.com/blog</code>',
								'<code>blog</code>'
							);
							?>
						</p>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Included Directories', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_dirs">
							<input type="text" name="image_cdn[dirs]" id="image_cdn_dirs" value="<?php echo esc_attr( $options['dirs'] ); ?>" size="64" class="regular-text code" />
							<?php esc_html_e( 'Default', 'image-cdn' ); ?>: <code><?php echo esc_html( $defaults['dirs'] ); ?></code>
						</label>

						<p class="description">
							<?php esc_html_e( 'Assets in these directories will be pointed to the CDN URL. Enter the directories separated by', 'image-cdn' ); ?> <code>,</code>
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
					<?php esc_html_e( 'CDN HTTPS Support', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_https">
							<input type="checkbox" name="image_cdn[https]" id="image_cdn_https" value="1" <?php checked( 1, $options['https'] ); ?> />
							<?php esc_html_e( 'Enable CDN for HTTPS connections (default: disabled).', 'image-cdn' ); ?>
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
	<p><?php esc_html_e( 'Configuration test', 'image-cdn' ); ?> <?php esc_html_e( 'successful!', 'image-cdn' ); ?> ...</p>
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
