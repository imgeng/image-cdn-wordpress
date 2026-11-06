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
class ImageCDNTest extends \PHPUnit\Framework\TestCase {

    public function testAddHeaders() {
        ImageCDN::$tests_running = true;
        ImageCDN::$test_headers_written = [];
        ImageCDN::$test_options = [
            'url' => 'https://foo.com',
        ];

        ImageCDN::add_headers();

        $expected = [
            'Accept-CH: sec-ch-dpr, sec-ch-width, sec-ch-viewport-width, ect, sec-ch-ect, sec-ch-ua-full-version, sec-ch-ua-full-version-list, sec-ch-ua-platform-version, sec-ch-ua-arch, sec-ch-ua-wow64, sec-ch-ua-bitness, sec-ch-ua-model',
            'Link: <https://foo.com>; rel=preconnect',
            'Permissions-Policy: ch-dpr=("https://foo.com"), ch-width=("https://foo.com"), ch-viewport-width=("https://foo.com"), ch-ect=("https://foo.com"), ch-ua-full-version=("https://foo.com"), ch-ua-full-version-list=("https://foo.com"), ch-ua-platform-version=("https://foo.com"), ch-ua-arch=("https://foo.com"), ch-ua-wow64=("https://foo.com"), ch-ua-bitness=("https://foo.com"), ch-ua-model=("https://foo.com")',
        ];
        $this->assertSame($expected, ImageCDN::$test_headers_written);
    }
}