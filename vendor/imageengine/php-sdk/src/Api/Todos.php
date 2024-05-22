<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk\Api;

use Http\Client\Exception;
use ImageEngine\PhpSdk\HttpClient\Message\ResponseMediator;
use ImageEngine\PhpSdk\Sdk;
use Psr\Http\Message\ResponseInterface;

final class Todos
{
    private Sdk $sdk;

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }

    public function allRaw(): ResponseInterface
    {
        return $this->sdk->getHttpClient()->get('/todos');
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function all()
    {
        return ResponseMediator::getContent($this->sdk->getHttpClient()->get('/todos'));
        // [{userId: 1,id: 1,title: "delectus aut autem",completed: false},
        //{userId: 1,id: 2,title: "quis ut nam facilis et officia qui",completed: false}]
    }
}
