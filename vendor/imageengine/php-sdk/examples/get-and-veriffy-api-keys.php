<?php

use ImageEngine\PhpSdk\Sdk;

require_once __DIR__ . '/../vendor/autoload.php';

$sdk = new Sdk();

try {
	$response = $sdk->authentication()->login('test@example.com', 'poiepodldfkijtPOED9');
	ray($response);

    if (isset($response['token'])) {
        $response = $sdk->authentication()->token($response['token']);
        ray($response);

        if (isset($response['token']) && isset($response['user_id'])) {
            $response = $sdk->microservice()->apiKeys($response['user_id'], $response['token']);
            ray($response);

            if (isset($response['key'])) {
                $response = $sdk->microservice()->user($response['user_id'], $response['key']);
                ray($response);
            }
        }
    }

} catch (\Exception $e) {
    ray($e);
}
