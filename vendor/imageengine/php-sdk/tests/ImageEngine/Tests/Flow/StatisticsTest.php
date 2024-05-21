<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Flow;

final class StatisticsTest extends TestCase
{
    public function testCanLoginAndGetDeliveryAddresses(): void
    {
        $client = $this->givenClient();

        $userId = "31623";
        $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMHU3';

        $this->storage->set('userId', $userId);
        $this->storage->set('apiKey', $key);

        $cname = '62x6yb00.cdn'; //one address for user test@example.com
        //$cname = 'kl5ikhrq.cdn'; //more addresses for user teste@example.com


        $statistics = $client->statistics($cname);
        ray($statistics);

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('metadata', $statistics);
        $this->assertArrayHasKey('cache_hit_ratio', $statistics);
        $this->assertArrayHasKey('data_over_time', $statistics);
        $this->assertArrayHasKey('requests_over_time', $statistics);
        $this->assertArrayHasKey('bytes_by_image_format', $statistics);
        $this->assertArrayHasKey('carbon_emissions_saved', $statistics);
        $this->assertArrayHasKey('smart_bytes', $statistics);
        $this->assertArrayHasKey('payload_reduction', $statistics);
    }
}
