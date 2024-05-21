<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

final class EnginesTest extends TestCase
{
    public function testCanReadDeliveryAddresses(): void
    {
        $sdk = $this->givenSdk();

        $user_id = 31623;
        $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMHU3';

        $response = $sdk->microservice()->subscriptions($user_id, $key);
        ray($response);

        $this->assertIsArray($response);
        $this->assertIsArray($response[0]['engines']);
    }

    public function testCanCreateEngine(): void
    {
        $sdk = $this->givenSdk();

        //$user_id = 31752;
        $subscription_id = 20997;
        $key = 'MTcxMDYwOnpLbnVWbFc2VGFYY3VQMU9YcnRHTDhwWjAzTG82VEpR';
        //$response = $sdk->microservice()->subscriptions($user_id, $key);

        $response = $sdk->microservice()->createEngine($subscription_id, $key);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('cname', $response);
    }
}
