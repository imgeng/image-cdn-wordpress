<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Flow;

use Http\Client\Curl\Client;
use ImageEngine\PhpSdk\ClientBuilder;
use ImageEngine\PhpSdk\IEClient;
use ImageEngine\PhpSdk\Options;
use ImageEngine\PhpSdk\Storage\FileStorage;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Client $httpClient;

    protected FileStorage $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new Client();
        $this->storage = new FileStorage(__DIR__ . '/../../../../storage/test/');
    }

    protected function givenClient(): IEClient
    {
        return new IEClient($this->storage, new Options([
            'client_builder' => new ClientBuilder($this->httpClient),
        ]));
    }
}
