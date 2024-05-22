<?php

use ImageEngine\PhpSdk\Sdk;

require_once __DIR__ . '/../vendor/autoload.php';

$sdk = new Sdk();

try {
    $user_id = 31623;
    $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMHU3';
    ray(\Exception::class);
    $response = $sdk->microservice()->user($user_id, $key);
    ray($response);
} catch (\Exception $e) {
    ray($e);
}
