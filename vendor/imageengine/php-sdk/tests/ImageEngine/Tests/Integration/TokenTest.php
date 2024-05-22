<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

final class TokenTest extends TestCase
{
    public function testCanRequestToken(): void
    {
        $username = "test@example.com";
        $password = "poiepodldfkijtPOED9";

        $sdk = $this->givenSdk();
        $response = $sdk->authentication()->login($username, $password);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('token', $response);

        $response = $sdk->authentication()->token($response['token']);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('user_id', $response);
    }

    public function testCannotRequestTokenIfWrong(): void
    {
        $sdk = $this->givenSdk();
        $wrongToken = " wrong token ";

        $response = $sdk->authentication()->token($wrongToken);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
        $this->assertEquals('invalid refresh_token', $response['message']);
    }

    public function testCannotRequestTokenIfExpired(): void
    {
        $sdk = $this->givenSdk();
        $expiredToken = "Q8tXw0OgKczOjbH4sHUw9wFf6SCSgrQMGDaasnNNsKV"; //valid until 2024-03-26 09:32:24

        $response = $sdk->authentication()->token($expiredToken);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
        $this->assertEquals('invalid refresh_token', $response['message']);
    }
}
