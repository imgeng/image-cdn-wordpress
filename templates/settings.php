<div class="wrap">
	<img src="<?php echo plugin_dir_url( IMAGE_CDN_FILE ); ?>assets/logo.png" />
	<div class="notice notice-info">
		<p>
			This plugin is best used with <a href="https://imageengine.io/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">ImageEngine</a>,
			but will also work with most other CDNs.
		</p>
		<p>To obtain an ImageEngine CDN hostname:</p>
		<ol>
			<li><a target="_blank" href="https://imageengine.io/signup/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine">Sign up for an ImageEngine account</a></li>
			<li>Enter the assigned ImageEngine hostname (including <code>http://</code> or <code>https://</code>) in the "CDN URL" option below.</li>
		</ol>
		<p>See <a href="https://imageengine.io/docs/setup/quick-start/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine" target="_blank">full documentation.</a></p>
	</div>
	<h2><?php _e( 'Image CDN Settings', 'image-cdn' ); ?></h2>
	<?php if ( $options['enabled'] && ! $is_runnable ) { ?>
		<div class="notice notice-error">
			<p>
				<?php _e( 'Image CDN support is disabled because there is something wrong with your configuration.  Please verify the URL below.' ); ?>
			</p>
		</div>
	<?php } ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'image_cdn' ); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row">
					<?php _e( 'Enabled', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_enabled">
							<input type="checkbox" name="image_cdn[enabled]" id="image_cdn_enabled" value="1" <?php checked( 1, $options['enabled'] ); ?> />
							<?php _e( 'Enable CDN support.', 'image-cdn' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'CDN URL', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_url">
							<input type="text" name="image_cdn[url]" id="image_cdn_url" value="<?php echo $options['url']; ?>" size="64" class="regular-text code" />
						</label>

						<p class="description">
							<?php _e( 'Enter your ImageEngine (or other Image CDN) URL. For ImageEngine, this can be found in your customer vault. In most cases, this will be a scheme and a hostname, like <code>https://my-site.cdn.imgeng.in</code>.', 'image-cdn' ); ?>
						</p>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'WordPress URL Path', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_path">
							<input type="text" name="image_cdn[path]" id="image_cdn_path" value="<?php echo $options['path']; ?>" size="64" class="regular-text code" />
							<?php _e( 'Optional', 'image-cdn' ); ?>
						</label>

						<p class="description">
							<?php _e( 'Path/subdirectory that WordPress is installed at.  For example, if WordPress is installed at <code>https://foo.bar.com/blog</code>, you would enter <code>blog</code>.  This is normally auto-detected properly, and is usually empty.', 'image-cdn' ); ?>
						</p>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'Included Directories', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_dirs">
							<input type="text" name="image_cdn[dirs]" id="image_cdn_dirs" value="<?php echo $options['dirs']; ?>" size="64" class="regular-text code" />
							<?php printf( __( 'Default: <code>%s</code>', 'image-cdn' ), $defaults['dirs'] ); ?>
						</label>

						<p class="description">
							<?php _e( 'Assets in these directories will be pointed to the CDN URL. Enter the directories separated by', 'image-cdn' ); ?> <code>,</code>
						</p>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'Exclusions', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_excludes">
							<input type="text" name="image_cdn[excludes]" id="image_cdn_excludes" value="<?php echo $options['excludes']; ?>" size="64" class="regular-text code" />
							<?php _e( 'Default: <code>.php</code>', 'image-cdn' ); ?>
						</label>

						<p class="description">
							<?php _e( 'Enter the exclusions (directories or extensions) separated by', 'image-cdn' ); ?> <code>,</code>
						</p>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'Relative Path', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_relative">
							<input type="checkbox" name="image_cdn[relative]" id="image_cdn_relative" value="1" <?php checked( 1, $options['relative'] ); ?> />
							<?php _e( 'Enable CDN for relative paths (default: enabled).', 'image-cdn' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'CDN HTTPS Support', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_https">
							<input type="checkbox" name="image_cdn[https]" id="image_cdn_https" value="1" <?php checked( 1, $options['https'] ); ?> />
							<?php _e( 'Enable CDN for HTTPS connections (default: disabled).', 'image-cdn' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e( 'ImageEngine Directives', 'image-cdn' ); ?>
				</th>
				<td>
					<fieldset>
						<label for="image_cdn_directives">
							<input type="text" name="image_cdn[directives]" id="image_cdn_directives" value="<?php echo $options['directives']; ?>" size="64" class="regular-text code" />
							<?php _e( 'Optional', 'image-cdn' ); ?>
						</label>

						<p class="description">
							<?php
							printf(
								__(
									'Enter the <a href="%s" target="_blank">ImageEngine Directives</a> to apply to all images.',
									'image-cdn'
								),
								esc_url( 'https://imageengine.io/docs/implementation/directives/?utm_source=WP-plugin-settigns&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine' )
							);
							?>

							<?php _e( 'Example: <code>/cmpr_10/s_0</code> (sets the compression to 10% and disables sharpening)', 'image-cdn' ); ?>
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
	<h3>Test Results</h3>
	<p>Testing CDN configuration ...</p>
</div>
<div class="image-cdn-test notice notice-success hidden">
	<h3>Test Results</h3>
	<p>Configuration test successful!</p>
</div>
<div class="image-cdn-test notice notice-warning hidden">
	<h3>Test Results</h3>
	<p>
		Configuration test warning: <em class="image-cdn-result"></em><br>
		Local Test URL: <code class="image-cdn-local-url"></code><br>
		CDN Test URL: <code class="image-cdn-remote-url"></code>
	</p>
</div>
<div class="image-cdn-test notice notice-error hidden">
	<h3>Test Results</h3>
	<p>
		Configuration test failed: <em class="image-cdn-result"></em><br>
		Local Test URL: <code class="image-cdn-local-url"></code><br>
		CDN Test URL: <code class="image-cdn-remote-url"></code>
	</p>
</div>
