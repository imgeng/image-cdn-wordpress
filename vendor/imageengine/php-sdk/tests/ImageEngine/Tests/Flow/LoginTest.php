<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Flow;

final class LoginTest extends TestCase
{
    public function testCanLoginAndGetDeliveryAddresses(): void
    {
        $client = $this->givenClient();

        $username = "test@example.com";
        $password = "poiepodldfkijtPOED9";

        $response = $client->login($username, $password);
        ray($response);

        $cname = null;
        if (is_string($response)) {
            $cname = $response;
        }
        if (is_array($response) && count($response) > 0) {
            $cname = $response[0];
        }

        $this->assertIsString($cname);
    }

    public function testCannotLoginWithInvalidPassword(): void
    {
        $client = $this->givenClient();

        $username = "test@example.com";
        $password = "";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to login user');

        $client->login($username, $password);
    }
}
