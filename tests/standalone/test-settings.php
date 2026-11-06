<?php
/**
 * Standalone tests for the admin settings handlers.
 *
 * @package ImageCDN
 */

use ImageEngine\ImageCDN;
use ImageEngine\Settings;

/**
 * Tests permission checks and outbound requests in ImageEngine\Settings.
 */
class SettingsTest extends \PHPUnit\Framework\TestCase {

	protected function setUp(): void {
		image_cdn_test_reset();
		ImageCDN::$tests_running = true;
		ImageCDN::$test_options  = array( 'url' => 'https://images.example' );
		$_POST                   = array();
	}

	/**
	 * Calls a handler that ends by sending JSON and returns the captured response.
	 */
	private function capture_json( callable $handler ) {
		try {
			$handler();
		} catch ( Image_CDN_Test_JSON_Response $response ) {
			return $response;
		}
		$this->fail( 'Handler did not send a JSON response' );
	}

	public function testTestConfigRejectsUserWithoutManageOptions() {
		$_POST['cdn_url'] = 'https://images.example';

		$response = $this->capture_json( array( Settings::class, 'test_config' ) );

		$this->assertFalse( $response->success );
		$this->assertSame( 403, $response->status );
		$this->assertSame( array(), $GLOBALS['image_cdn_test']['remote_calls'], 'No outbound request may be made' );
	}

	public function testAnalyticsRejectsUserWithoutManageOptions() {
		$_POST['cdn_url'] = 'https://images.example';

		$response = $this->capture_json( array( Settings::class, 'analytics' ) );

		$this->assertFalse( $response->success );
		$this->assertSame( 403, $response->status );
	}

	public function testTestConfigUsesSafeRequestForDeliveryAddress() {
		$GLOBALS['image_cdn_test']['caps'] = array( 'manage_options' );
		$_POST['cdn_url']                  = 'https://images.example';

		$response = $this->capture_json( array( Settings::class, 'test_config' ) );

		$calls = $GLOBALS['image_cdn_test']['remote_calls'];
		$this->assertSame( 'wp_safe_remote_get', end( $calls )[0] );
		$this->assertStringStartsWith( 'https://images.example/', end( $calls )[1] );
		$this->assertFalse( $response->success, 'Stubbed safe request fails, so the test reports an error' );
	}

	public function testTestConfigScriptOnlyOnSettingsScreen() {
		$GLOBALS['image_cdn_test']['caps'] = array( 'manage_options' );

		$GLOBALS['image_cdn_test']['screen'] = 'profile';
		$this->assertSame( '', $this->render( array( Settings::class, 'register_test_config' ) ) );

		$GLOBALS['image_cdn_test']['screen'] = Settings::SETTINGS_SCREEN_ID;
		$this->assertStringContainsString( 'nonce-image-cdn-test-config', $this->render( array( Settings::class, 'register_test_config' ) ) );
	}

	public function testTestConfigScriptNotPrintedForUserWithoutManageOptions() {
		$GLOBALS['image_cdn_test']['screen'] = Settings::SETTINGS_SCREEN_ID;

		$this->assertSame( '', $this->render( array( Settings::class, 'register_test_config' ) ) );
	}

	public function testAnalyticsScriptNotPrintedOffSettingsScreen() {
		$GLOBALS['image_cdn_test']['caps']   = array( 'manage_options' );
		$GLOBALS['image_cdn_test']['screen'] = 'dashboard';

		$this->assertSame( '', $this->render( array( Settings::class, 'register_analytics' ) ) );
	}

	public function testAdminNoticesAreEscaped() {
		$GLOBALS['image_cdn_test']['options']['image_cdn_notices'] = array(
			array(
				'type'    => 'error',
				'message' => 'Check <script>alert(1)</script>',
				'tab'     => null,
			),
		);

		Settings::admin_notices();

		$this->assertSame(
			array( '<b>Check &lt;script&gt;alert(1)&lt;/script&gt;</b>' ),
			$GLOBALS['image_cdn_test']['admin_notices']
		);
	}

	private function render( callable $callback ) {
		ob_start();
		$callback();
		return ob_get_clean();
	}
}
