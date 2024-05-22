<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk;

use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use ImageEngine\PhpSdk\Api\Authentication;
use ImageEngine\PhpSdk\Api\Co2;
use ImageEngine\PhpSdk\Api\Microservice;
use ImageEngine\PhpSdk\Api\Todos;

final class Sdk
{
    private Options $options;

    private ClientBuilder $clientBuilder;

    public function __construct(Options $options = null)
    {
        $this->options = $options ?? new Options();

        $this->clientBuilder = $this->options->getClientBuilder();
        $this->clientBuilder->addPlugin(new BaseUriPlugin($this->options->getUri()));
        $this->clientBuilder->addPlugin(
            new HeaderDefaultsPlugin(
                [
                    'User-Agent' => 'ImageEngine PHP SDK',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            )
        );
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getHttpClient(): HttpMethodsClientInterface
    {
        return $this->clientBuilder->getHttpClient();
    }

    public function setBaseUri(string $for): void
    {
        switch ($for) {
            case 'ie-microservice':
                $uri = $this->options->getUriMicroservice();
                break;
            case 'sm-auth-service':
                $uri = $this->options->getUriSmAuthService();
                break;
            case 'co2':
                $uri = $this->options->getUriCo2();
                break;
            default:
                $uri = $this->options->getUri();
        }
        $this->clientBuilder->addPlugin(new BaseUriPlugin($uri));
    }

    public function todos(): Todos
    {
        return new Todos($this);
    }

    public function authentication(): Authentication
    {
        return new Authentication($this);
    }

    public function microservice(): Microservice
    {
        return new Microservice($this);
    }

    public function co2(): Co2
    {
        return new Co2($this);
    }
}
