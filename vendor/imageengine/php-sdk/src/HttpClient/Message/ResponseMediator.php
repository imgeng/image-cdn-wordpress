<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk\HttpClient\Message;

use Psr\Http\Message\ResponseInterface;

final class ResponseMediator
{
    /**
     * @param ResponseInterface $response
     *
     * @return array|string
     */
    public static function getContent(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 401) {
            throw new \Exception('Unauthorized');
        }
        if ($response->getStatusCode() === 403) {
            throw new \Exception('Forbidden');
        }
        if ($response->getStatusCode() === 404) {
            throw new \Exception('Not Found');
        }
        $body = $response->getBody()->__toString();
        //if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0
        //  || $response->getStatusCode()===201) {
            $content = json_decode($body, true);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $content;
        }
        //}
        return $body;
    }
}
