<?php

use ImageEngine\PhpSdk\IEClient;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new IEClient();

try {

    $username = "teste@example.com";
    $password = "poiepodldfkijtPOED9";

    $response = $client->login($username, $password);
    ray($response);
} catch (\Exception $e) {
    ray($e);
}
