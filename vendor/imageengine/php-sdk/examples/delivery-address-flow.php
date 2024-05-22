<?php

use ImageEngine\PhpSdk\IEClient;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new IEClient();

try {
    $response = $client->deliveryAddress();
    ray($response);
} catch (\Exception $e) {
    ray($e);
}
