<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

use Http\Client\Curl\Client;
use ImageEngine\PhpSdk\ClientBuilder;
use ImageEngine\PhpSdk\Options;
use ImageEngine\PhpSdk\Sdk;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Client();
    }

    protected function givenSdk(): Sdk
    {
        return new Sdk(new Options([
            'client_builder' => new ClientBuilder($this->client),
        ]));
    }
}
