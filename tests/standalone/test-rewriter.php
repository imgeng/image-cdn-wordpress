<?php
/**
 * This file contains standalone tests for the rewriter functionality
 *
 * @package ImageCDN
 */

use ImageEngine\Rewriter;

/**
 * The RewriterTest class tests the Rewriter.
 */
class RewriterTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test assets that should be excluded.
	 */
	public function testExcludeAsset() {
		$blog_url   = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes';
		$excludes   = array( '.php', '.woff2' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		$this->assertEquals( true,  $rewrite->exclude_asset( '/wp-includes/bar.php' ) );
		$this->assertEquals( true,  $rewrite->exclude_asset( '/wp-includes/bar.woff2' ) );
		$this->assertEquals( false, $rewrite->exclude_asset( '/wp-includes/bar.woff' ) );
		$this->assertEquals( true,  $rewrite->exclude_asset( '/wp-includes/bar.php/foo.jpg' ) );
		$this->assertEquals( false, $rewrite->exclude_asset( '/wp-includes/bar.jpg' ) );
	}

	/**
	 * Test whether relative URLs are transformed properly.
	 */
	public function testRelativeURL() {
		$blog_url   = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		$this->assertEquals( '//foo.com/wp-includes/bar.jpg', $rewrite->relative_url( 'http://foo.com/wp-includes/bar.jpg' ) );
		$this->assertEquals( '//foo.com/wp-includes/bar/blah/baz.jpg', $rewrite->relative_url( 'http://foo.com/wp-includes/bar/blah/baz.jpg' ) );
	}

	/**
	 * Test if URLs are transformed properly via rewrite_url().
	 */
	public function testRewriteURL() {
		$blog_url   = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		$test_urls = array(
			// This one is excluded because it contains '.php'.
			'http://foo.com/wp-includes/bar/blah/baz.php' => 'http://foo.com/wp-includes/bar/blah/baz.php',
			'http://foo.com/wp-includes/bar/blah/baz.jpg' => 'http://my.cdn/wp-includes/bar/blah/baz.jpg?imgeng=/cmpr_20',
			'//foo.com/wp-includes/bar/blah/baz.jpg'      => 'http://my.cdn/wp-includes/bar/blah/baz.jpg?imgeng=/cmpr_20',
			'/wp-includes/bar/blah/baz.png'               => 'http://my.cdn/wp-includes/bar/blah/baz.png?imgeng=/cmpr_20',
		);

		foreach ( $test_urls as $input => $expected ) {
			$actual = $rewrite->rewrite_url( $input );
			$this->assertEquals( $expected, $actual );
		}
	}

	/**
	 * Test whether ImageEngine directives are added properly.
	 */
	public function testAddDirectives() {
		$blog_url   = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20/w_240/h_180/f_avif';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		$test_urls = array(
			'http://foo.com/wp-includes/bar/blah/baz.php' => 'http://foo.com/wp-includes/bar/blah/baz.php?imgeng=/cmpr_20/w_240/h_180/f_avif',
			'http://foo.com/wp-includes/bar/blah/baz.jpg' => 'http://foo.com/wp-includes/bar/blah/baz.jpg?imgeng=/cmpr_20/w_240/h_180/f_avif',
			'//foo.com/wp-includes/bar/blah/baz.jpg'      => '//foo.com/wp-includes/bar/blah/baz.jpg?imgeng=/cmpr_20/w_240/h_180/f_avif',
			'/wp-includes/bar/blah/baz.png'               => '/wp-includes/bar/blah/baz.png?imgeng=/cmpr_20/w_240/h_180/f_avif',
			// Directives are merged.
			'/wp-includes/baz.png?imgeng=/s_10'           => '/wp-includes/baz.png?imgeng=/cmpr_20/w_240/h_180/f_avif/s_10',
			// Old query params are preserved.
			'/wp-includes/baz.png?foo=bar'                => '/wp-includes/baz.png?foo=bar&imgeng=/cmpr_20/w_240/h_180/f_avif',
			// Special chars are preserved.
			'/wp-includes/baz.png?name=steve%20kam'       => '/wp-includes/baz.png?name=steve%20kam&imgeng=/cmpr_20/w_240/h_180/f_avif',
		);

		foreach ( $test_urls as $input => $expected ) {
			$actual = $rewrite->add_directives( $input );
			$this->assertEquals( $expected, $actual );
		}
	}

	/**
	 * Ensure the dir_scope() function is working as expected.
	 */
	public function testGetDirScope() {
		$blog_url   = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes,wp-content';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		$this->assertEquals( 'wp\-includes|wp\-content', $rewrite->get_dir_scope() );
	}

	/**
	 * Test if URLs are transformed properly via rewrite().
	 */
	public function testRewrite() {
		$blog_url   = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes,wp-content';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		$input    = '<html><body><img src="http://ignore.me/wp-includes/test.jpg"/></body></html>';
		$expected = '<html><body><img src="http://ignore.me/wp-includes/test.jpg"/></body></html>';
		$actual   = $rewrite->rewrite( $input );
		$this->assertEquals( $expected, $actual );

		$input    = '<html><body><img src="http://foo.com/wp-includes/test.jpg"/></body></html>';
		$expected = '<html><body><img src="http://my.cdn/wp-includes/test.jpg?imgeng=/cmpr_20"/></body></html>';
		$actual   = $rewrite->rewrite( $input );
		$this->assertEquals( $expected, $actual );

		$input = <<<EOF
<html>
<head>
	<title>http://foo.com/wp-includes/test1.jpg</title>
	<script src="http://foo.com/wp-includes/test2.js"></script>
	<style>
	.foo {
		background-image: url("/wp-includes/css1.jpg"), url('/wp-includes/css2.jpg');
	}
	.bar {
		background-image: url(/wp-includes/look-mom-no-quotes.jpg);
	}
	</style>
</head>
<body>
	<img src="/wp-includes/test3.jpg"/>
	<img src="http://foo.com/wp-includes/test4.jpg"/>
	<img src='/wp-includes/test5.jpg' alt='something'/>
	<img src='http://foo.com/wp-includes/test6.jpg' alt='something'/>
	<div style="background-image: url('/wp-includes/loader.gif')"> </div>
	<picture>
		<source media="(min-width: 650px)" srcset="/wp-includes/test7.jpg?imgeng=/w_1024">
		<source media="(min-width: 465px)" srcset="/wp-includes/test8.jpg?imgeng=/w_650">
		<img src="/wp-includes/test9.jpg">
	</picture>
	<img srcset="/wp-includes/test10.jpg  1024w,
		/wp-includes/test11.jpg 640w,
		/wp-includes/test12.jpg  320w"
		sizes="(min-width: 36em) 33.3vw,
		100vw"
		src="/wp-includes/test13.jpg"
		alt="A crazy syntax!" />
	<img srcset="/wp-includes/test14.jpg  1024w,
		/wp-includes/test15.jpg"
		sizes="(min-width: 36em) 33.3vw,
		100vw"
		alt="A crazy syntax2!" />
</body>
</html>
EOF;

		$expected = <<<EOF
<html>
<head>
	<title>http://foo.com/wp-includes/test1.jpg</title>
	<script src="http://my.cdn/wp-includes/test2.js?imgeng=/cmpr_20"></script>
	<style>
	.foo {
		background-image: url("http://my.cdn/wp-includes/css1.jpg?imgeng=/cmpr_20"), url('http://my.cdn/wp-includes/css2.jpg?imgeng=/cmpr_20');
	}
	.bar {
		background-image: url(http://my.cdn/wp-includes/look-mom-no-quotes.jpg?imgeng=/cmpr_20);
	}
	</style>
</head>
<body>
	<img src="http://my.cdn/wp-includes/test3.jpg?imgeng=/cmpr_20"/>
	<img src="http://my.cdn/wp-includes/test4.jpg?imgeng=/cmpr_20"/>
	<img src='http://my.cdn/wp-includes/test5.jpg?imgeng=/cmpr_20' alt='something'/>
	<img src='http://my.cdn/wp-includes/test6.jpg?imgeng=/cmpr_20' alt='something'/>
	<div style="background-image: url('http://my.cdn/wp-includes/loader.gif?imgeng=/cmpr_20')"> </div>
	<picture>
		<source media="(min-width: 650px)" srcset="http://my.cdn/wp-includes/test7.jpg?imgeng=/cmpr_20/w_1024">
		<source media="(min-width: 465px)" srcset="http://my.cdn/wp-includes/test8.jpg?imgeng=/cmpr_20/w_650">
		<img src="http://my.cdn/wp-includes/test9.jpg?imgeng=/cmpr_20">
	</picture>
	<img srcset="http://my.cdn/wp-includes/test10.jpg?imgeng=/cmpr_20 1024w,
		http://my.cdn/wp-includes/test11.jpg?imgeng=/cmpr_20 640w,
		http://my.cdn/wp-includes/test12.jpg?imgeng=/cmpr_20 320w"
		sizes="(min-width: 36em) 33.3vw,
		100vw"
		src="http://my.cdn/wp-includes/test13.jpg?imgeng=/cmpr_20"
		alt="A crazy syntax!" />
	<img srcset="http://my.cdn/wp-includes/test14.jpg?imgeng=/cmpr_20 1024w,
		http://my.cdn/wp-includes/test15.jpg?imgeng=/cmpr_20"
		sizes="(min-width: 36em) 33.3vw,
		100vw"
		alt="A crazy syntax2!" />
</body>
</html>
EOF;

		$actual = $rewrite->rewrite( $input );

		$this->assertEquals( $expected, $actual );
	}

	public function testRewriteRegexBadMatches() {
		 $blog_url  = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes,wp-content';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );
		$regex   = $rewrite->generate_regex();

		$inputs = array(
			// Missmatched starting and ending delimiters.
			'"http://foo.com/wp-includes/test2.js\'',
			'(http://foo.com/wp-includes/test2.js(',
			// Escaped ending delimiter.
			'"http://foo.com/wp-includes/test2.js\\"',
			'"a.b"',
		);

		foreach ( $inputs as $input ) {
			$expected = $input;
			$actual   = $rewrite->rewrite( $input );
			$this->assertEquals( $expected, $actual );
		}
	}

	public function testRewriteRegexCodeRegression() {
		 $blog_url  = 'http://foo.com';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes,wp-content';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );
		$regex   = $rewrite->generate_regex();

		// This JS string was found in the Divi builder for WordPress when editing a page
		// and was previously being altered erroneously.
		$input    = <<<EOF
c=Function.prototype,f=Object.prototype,s=c.toString,p=f.hasOwnProperty,l=RegExp("^"+s.call(p).replace(/[\\^$.*+?()[\]{}|]/g,"\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g,"$1.*?")+"$");n.exports=function(n){return!(!i(n)||o(n))&&(e(n)?l:a).test(u(n))}},
EOF;
		$expected = $input;
		$actual   = $rewrite->rewrite( $input );

		$this->assertEquals( $expected, $actual );
	}

	public function testRewriteLargePages() {
		$blog_url   = 'http://34.228.82.33';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes,wp-content';
		$excludes   = array( '.php' );
		$relative   = true;
		$https      = true;
		$directives = '';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		foreach ( glob( __DIR__ . '/pages/test*-original.html' ) as $original_file ) {
			$expected_file = str_replace( '-original', '-expected', $original_file );

			$original = file_get_contents( $original_file );
			$expected = file_get_contents( $expected_file );

			$actual = $rewrite->rewrite( $original );
			$this->assertEquals( $expected, $actual );
		}
	}

	public function testRewriteLargePagesNotRelative() {
		$blog_url   = 'http://34.228.82.33';
		$cdn_url    = 'http://my.cdn';
		$path       = '';
		$dirs       = 'wp-includes,wp-content';
		$excludes   = array( '.php' );
		$relative   = false;
		$https      = true;
		$directives = '';

		$rewrite = new Rewriter( $blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives );

		foreach ( glob( __DIR__ . '/pages/test*-original.html' ) as $original_file ) {
			$expected_file = str_replace( '-original', '-expected', $original_file );

			$original = file_get_contents( $original_file );
			$expected = file_get_contents( $expected_file );

			$actual = $rewrite->rewrite( $original );
			$this->assertEquals( $expected, $actual );
		}
	}

}
