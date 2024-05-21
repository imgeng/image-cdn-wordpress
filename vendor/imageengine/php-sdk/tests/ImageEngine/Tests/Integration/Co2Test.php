<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

final class Co2Test extends TestCase
{
    public function testCo2(): void
    {
        $sdk = $this->givenSdk();

        $response = $sdk->co2()->co2(1024 * 1024 * 1024);
        ray($response);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('carbonFootprintGrams', $response);
        $this->assertArrayHasKey('carbonFootprintKilos', $response);
        $this->assertArrayHasKey('carbonFootprintTonnes', $response);
        $this->assertArrayHasKey('sameAs', $response);
    }
}
