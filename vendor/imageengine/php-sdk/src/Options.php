<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk;

use ImageEngine\PhpSdk\Config\APIData;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Options
{
    private array $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'client_builder' => new ClientBuilder(),
            'uri_factory' => Psr17FactoryDiscovery::findUriFactory(),
            'uri-microservice' => APIData::IE_MICROSERVICE_URI,
            'uri-sm-auth-service' => APIData:: SM_AUTH_SERVICE_URI,
            'uri-co2' => APIData:: CO2_URI,
            'uri' => 'https://jsonplaceholder.typicode.com',
            'site_url' => !isset($_SERVER['HTTP_HOST']) ? "https://www.example.com/" :
                ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http")
                . "://" . $_SERVER['HTTP_HOST']
        ]);
    }

    public function getClientBuilder(): ClientBuilder
    {

        return $this->options['client_builder'];
    }

    public function getUriFactory(): UriFactoryInterface
    {

        return $this->options['uri_factory'];
    }

    public function getUriMicroservice(): UriInterface
    {
        return $this->getUriFactory()->createUri($this->options['uri-microservice']);
    }

    public function getUriSmAuthService(): UriInterface
    {
        return $this->getUriFactory()->createUri($this->options['uri-sm-auth-service']);
    }

    public function getUriCo2(): UriInterface
    {
        return $this->getUriFactory()->createUri($this->options['uri-co2']);
    }

    public function getUri(): UriInterface
    {
        return $this->getUriFactory()->createUri($this->options['uri']);
    }

    public function getSiteUrl(): string
    {
        return $this->options['site_url'];
    }
}
