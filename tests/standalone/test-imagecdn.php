<?php
/**
 * This file contains standalone tests for the ImageCDN functionality
 *
 * @package ImageCDN
 */

use ImageEngine\ImageCDN;

/**
 * The ImageCDNTest class tests the ImageCDN class.
 */
class ImageCDNTest extends PHPUnit_Framework_TestCase {

    public function testAddHeaders() {
        ImageCDN::$tests_running = true;
        ImageCDN::$test_headers_written = [];
        ImageCDN::$test_options = [
            'url' => 'https://foo.com',
        ];

        ImageCDN::add_headers();

        $expected = [
            'Accept-CH: viewport-width, width, dpr, ect',
            'Link: <https://foo.com>; rel=preconnect',
            'Feature-Policy: ch-viewport-width https://foo.com; ch-width https://foo.com; ch-dpr https://foo.com; ch-ect https://foo.com',
            'Permissions-Policy: ch-viewport-width=("https://foo.com"), ch-width=("https://foo.com"), ch-dpr=("https://foo.com"), ch-ect=("https://foo.com")',
        ];
        $this->assertSame($expected, ImageCDN::$test_headers_written);
    }
}