<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Flow;

final class RegisterTest extends TestCase
{
    public function testCanRegisterAndGetDeliveryAddress(): void
    {
        $client = $this->givenClient();

        $username = "test@example.com";
        $password = "poiepodldfkijtPOED9";

        $response = $client->register($username, $password);
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

    public function testCannotRegisterWithInvalidPassword(): void
    {
        $client = $this->givenClient();

        $username = "test@example.com";
        $password = "";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to register user');

        $client->register($username, $password);
    }
}
