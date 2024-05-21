<?php

use ImageEngine\PhpSdk\IEClient;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new IEClient();

try {
    $response = $client->co2(12312321234213);
    ray($response);
} catch (\Exception $e) {
    ray($e);
}
