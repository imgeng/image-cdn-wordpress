<?php
/**
 * This file contains the Rewriter class
 *
 * @package ImageCDN
 */

namespace ImageEngine;

/**
 * The Rewriter class handles the actual rewriting of URLs in the HTML.
 */
class Rewriter {

	/**
	 * WordPress installation URL.
	 *
	 * @var string
	 */
	public $blog_url;

	/**
	 * CDN URL.
	 *
	 * @var str
	 */
	public $cdn_url;

	/**
	 * Path / subdirectory of the WordPress installation, if not '/'.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Included directories.
	 *
	 * @var string
	 */
	public $dirs;

	/**
	 * Excludes.
	 *
	 * @var arra
	 */
	public $excludes = array();

	/**
	 * Use CDN on relative paths.
	 *
	 * @var bool
	 */
	public $relative = false;

	/**
	 * Use CDN on HTTPS.
	 *
	 * @var bool
	 */
	public $https = false;

	/**
	 * ImageEngine Directives.
	 *
	 * @var string
	 */
	public $directives;

	/**
	 * Constructor.
	 *
	 * @param string $blog_url WordPress installation URL.
	 * @param string $cdn_url CDN URL.
	 * @param string $path Path / subdirectory of the WordPress installation, if not '/'.
	 * @param string $dirs Included directories.
	 * @param array  $excludes Excludes.
	 * @param bool   $relative Use CDN on relative paths.
	 * @param bool   $https Use CDN on HTTPS.
	 * @param string $directives ImageEngine Directives.
	 */
	public function __construct(
		$blog_url,
		$cdn_url,
		$path,
		$dirs,
		array $excludes,
		$relative,
		$https,
		$directives
	) {
		$this->blog_url   = $blog_url;
		$this->cdn_url    = $cdn_url;
		$this->path       = $path;
		$this->dirs       = $dirs;
		$this->excludes   = $excludes;
		$this->relative   = $relative;
		$this->https      = $https;
		$this->directives = $directives;
	}


	/**
	 * Exclude assets that should not be rewritten.
	 *
	 * @param   string $asset  current asset.
	 * @return  boolean  true if need to be excluded.
	 */
	protected function exclude_asset( $asset ) {
		// Excludes.
		foreach ( $this->excludes as $exclude ) {
			if ( $exclude && stripos( $asset, $exclude ) !== false ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Relative url.
	 *
	 * @param   string $url a full url.
	 * @return  string  protocol relative url.
	 */
	protected function relative_url( $url ) {
		return substr( $url, strpos( $url, '//' ) );
	}


	/**
	 * Rewrite url.
	 *
	 * @param   string $asset  current asset.
	 * @return  string  updated url if not excluded.
	 */
	protected function rewrite_url( $asset ) {
		$asset_url = $asset[0];

		if ( $this->exclude_asset( $asset_url ) ) {
			return $asset_url;
		}

		// Don't rewrite if in preview mode.
		if ( is_admin_bar_showing()
				&& array_key_exists( 'preview', $_GET ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				&& 'true' === $_GET['preview'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $asset_url;
		}

		$blog_url   = $this->relative_url( $this->blog_url );
		$subst_urls = array( 'http:' . $blog_url );

		// Rewrite both http and https URLs if we ticked 'enable CDN for HTTPS connections'.
		if ( $this->https ) {
			$subst_urls[] = 'https:' . $blog_url;
		}

		// Add ImageEngine directives, if any.
		$asset_url = $this->add_directives( $asset_url );

		// Prepend the path in case this installation is not at /.
		if ( strlen( $this->path ) !== 0 ) {
			$asset_url = '/' . trim( $this->path, '/' ) . $asset_url;
		}

		// Is it a relative-protocol URL?.
		if ( strpos( $asset_url, '//' ) === 0 ) {
			return str_replace( $blog_url, $this->cdn_url, $asset_url );
		}

		// Check if not a relative path.
		if ( ! $this->relative || strstr( $asset_url, $blog_url ) ) {
			return str_replace( $subst_urls, $this->cdn_url, $asset_url );
		}

		// Relative URL.
		return $this->cdn_url . $asset_url;
	}

	/**
	 * Add ImageEngine Directives to URL and return the new URL.
	 *
	 * @param string $url Input URL.
	 * @return string Input URL with the directives added.
	 */
	protected function add_directives( $url ) {
		// No directives, don't do anything.
		if ( '' === trim( $this->directives ) ) {
			return $url;
		}

		// No query string, add ours.
		if ( strpos( $url, '?' ) === false ) {
			return $url . '?imgeng=' . $this->directives;
		}

		// If there are already some directives, add the new ones.
		if ( strpos( $url, 'imgeng=' ) !== false ) {
			return preg_replace( '#(\?.*?imgeng=)/?#', '$1' . $this->directives . '/', $url );
		}

		return $url . '&imgeng=' . $this->directives;
	}

	/**
	 * Get directory scope.
	 *
	 * @return  string  directory scope.
	 */
	protected function get_dir_scope() {
		$dirs = trim( $this->dirs, ' ,' );
		if ( empty( $dirs ) ) {
			$default = ImageCDN::default_options();
			$dirs    = $default['dirs'];
		}

		$input = explode( ',', $dirs );

		return implode(
			'|',
			array_map(
				function ( $in ) {
					$in = trim( $in );
					$in = preg_quote( $in, '#' );
					return $in;
				},
				$input
			)
		);
	}

	/**
	 * Rewrite URL.
	 *
	 * @param   string $html  current raw HTML doc.
	 * @return  string  updated HTML doc with CDN links.
	 */
	public function rewrite( $html ) {
		// Check if HTTPS and use CDN over HTTPS enabled.
		if ( ! $this->https && isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) {
			return $html;
		}

		// Get dir scope in regex format.
		$dirs     = $this->get_dir_scope();
		$blog_url = $this->https
			? '(https?:|)' . $this->relative_url( preg_quote( $this->blog_url, '#' ) )
			: '(http:|)' . $this->relative_url( preg_quote( $this->blog_url, '#' ) );

		// Regex rule start.
		$regex_rule = '#(?<=[(\"\'])';

		// Check if relative paths.
		if ( $this->relative ) {
			$regex_rule .= '(?:' . $blog_url . ')?';
		} else {
			$regex_rule .= $blog_url;
		}

		// Regex rule end.
		$regex_rule .= '/(?:((?:' . $dirs . ')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';

		// Call the cdn rewriter callback.
		$cdn_html = preg_replace_callback( $regex_rule, array( $this, 'rewrite_url' ), $html );

		return $cdn_html;
	}
}
