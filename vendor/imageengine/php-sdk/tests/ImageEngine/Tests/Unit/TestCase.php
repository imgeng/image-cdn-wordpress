<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Unit;

use Http\Mock\Client;
use ImageEngine\PhpSdk\ClientBuilder;
use ImageEngine\PhpSdk\Options;
use ImageEngine\PhpSdk\Sdk;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Client $mockClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = new Client();
    }

    protected function givenSdk(): Sdk
    {
        return new Sdk(new Options([
            'client_builder' => new ClientBuilder($this->mockClient),
        ]));
    }
}
