<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

final class SubscriptionsTest extends TestCase
{
    public function testCanReadSubscriptions(): void
    {
        $sdk = $this->givenSdk();

        $user_id = 31623;
        $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMHU3';

        $response = $sdk->microservice()->subscriptions($user_id, $key);
        ray($response);

        $this->assertIsArray($response);
    }

    public function testCanCreateSubscription(): void
    {
        $sdk = $this->givenSdk();

        $user_id = 31752;
        $key = 'MTcxMDYwOnpLbnVWbFc2VGFYY3VQMU9YcnRHTDhwWjAzTG82VEpR';

        $response = $sdk->microservice()->createSubscription($user_id, $key);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('type', $response);
    }
}
