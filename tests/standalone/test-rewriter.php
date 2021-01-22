<?php

use ImageEngine\Rewriter;

class RewriterTest extends PHPUnit_Framework_TestCase
{
	function testExcludeAsset()
	{
		$blog_url = 'http://foo.com';
		$cdn_url = 'http://my.cdn';
		$path = '/';
		$dirs = 'wp-includes';
		$excludes = ['.php'];
		$relative = true;
		$https = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter($blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives);

		$this->assertEquals(true, $rewrite->exclude_asset("/wp-includes/bar.php"));
		$this->assertEquals(true, $rewrite->exclude_asset("/wp-includes/bar.php/foo.jpg"));
		$this->assertEquals(false, $rewrite->exclude_asset("/wp-includes/bar.jpg"));
	}


	function testRelativeURL()
	{
		$blog_url = 'http://foo.com';
		$cdn_url = 'http://my.cdn';
		$path = '/';
		$dirs = 'wp-includes';
		$excludes = ['.php'];
		$relative = true;
		$https = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter($blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives);

		$this->assertEquals("//foo.com/wp-includes/bar.jpg", $rewrite->relative_url("http://foo.com/wp-includes/bar.jpg"));
		$this->assertEquals("//foo.com/wp-includes/bar/blah/baz.jpg", $rewrite->relative_url("http://foo.com/wp-includes/bar/blah/baz.jpg"));
	}

	function testRewriteURL()
	{
		$blog_url = 'http://foo.com';
		$cdn_url = 'http://my.cdn';
		$path = '';
		$dirs = 'wp-includes';
		$excludes = ['.php'];
		$relative = true;
		$https = true;
		$directives = '/cmpr_20';

		$rewrite = new Rewriter($blog_url, $cdn_url, $path, $dirs, $excludes, $relative, $https, $directives);

		$test_urls = [
			// This one is excluded because it contains '.php'
			'http://foo.com/wp-includes/bar/blah/baz.php' => 'http://foo.com/wp-includes/bar/blah/baz.php',
			'http://foo.com/wp-includes/bar/blah/baz.jpg' => 'http://my.cdn/wp-includes/bar/blah/baz.jpg?imgeng=/cmpr_20',
			'//foo.com/wp-includes/bar/blah/baz.jpg' => 'http://my.cdn/wp-includes/bar/blah/baz.jpg?imgeng=/cmpr_20',
			'/wp-includes/bar/blah/baz.png' => 'http://my.cdn/wp-includes/bar/blah/baz.png?imgeng=/cmpr_20',
		];

		foreach ($test_urls as $input => $expected) {
			$actual = $rewrite->rewrite_url([$input]);
			$this->assertEquals($expected, $actual);
		}
	}
}
