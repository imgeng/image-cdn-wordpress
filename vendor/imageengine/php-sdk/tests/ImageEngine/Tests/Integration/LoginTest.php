<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

final class LoginTest extends TestCase
{
    public function testCanLogin(): void
    {
        $username = "test@example.com";
        $password = "poiepodldfkijtPOED9";

        $sdk = $this->givenSdk();
        $response = $sdk->authentication()->login($username, $password);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('token', $response);
    }

    public function testWrongUsername(): void
    {
        $username = "test-wrong@example.com";
        $password = "poiepodldfkijtPOED9";

        $sdk = $this->givenSdk();
        $response = $sdk->authentication()->login($username, $password);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testWrongPassword(): void
    {
        $username = "test@example.com";
        $password = "poiepodldfkijtPOED9 wrong";

        $sdk = $this->givenSdk();
        $response = $sdk->authentication()->login($username, $password);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }
}
