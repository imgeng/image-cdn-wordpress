<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk\Api;

use Exception;
use ImageEngine\PhpSdk\Sdk;

final class Co2 extends AbstractApi
{
    protected Sdk $sdk;

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
        $this->sdk->setBaseUri('co2');
    }

    /**
     *
     * @param int|float $bytes
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function co2($bytes)
    {
        return $this->get('/co2', [
            'bytes'     => $bytes,
            'greenHost' => "false",
        ]);
    }
}
