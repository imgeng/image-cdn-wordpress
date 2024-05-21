<?php

use ImageEngine\PhpSdk\Sdk;

require_once __DIR__ . '/../vendor/autoload.php';

$sdk = new Sdk();

try {
	$response = $sdk->authentication()->login('test@example.com', 'poiepodldfkijtPOED9');

	$response = $sdk->microservice()->signup('test2@example.com','test2@example.com', 'poiepodldfkijtPOED9');

	$response = $sdk->authentication()->login('test@example.com wrong', 'poiepodldfkijtPOED9');
} catch (\Exception $e) {
    ray($e);
}
