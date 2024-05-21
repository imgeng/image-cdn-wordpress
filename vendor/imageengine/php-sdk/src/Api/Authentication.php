<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk\Api;

use Exception;
use ImageEngine\PhpSdk\Config\APIData;
use ImageEngine\PhpSdk\Sdk;

final class Authentication extends AbstractApi
{
    protected Sdk $sdk;

    private const TOKEN = APIData::TOKEN;

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
        $this->sdk->setBaseUri('sm-auth-service');
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function login(string $username, string $password)
    {
        return $this->post('/api/v1/login', [
            'username' => $username,
            'password' => $password,
            'token' => self::TOKEN,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function token(string $refresh_token)
    {
        return $this->post('/api/v1/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
        ]);
    }
}
