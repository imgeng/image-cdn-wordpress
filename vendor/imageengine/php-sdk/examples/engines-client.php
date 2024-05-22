<?php

use ImageEngine\PhpSdk\Sdk;

require_once __DIR__ . '/../vendor/autoload.php';

$sdk = new Sdk();

try {
    $user_id = 31623;
    $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMHU3';
    $response = $sdk->microservice()->subscriptions($user_id, $key);
    ray($response);
    if(is_array($response) && count($response) < 1) {
        $response = $sdk->microservice()->createSubscription($user_id, $key);
        ray($response);

        $response = $sdk->microservice()->subscriptions($user_id, $key);
        ray($response);
    }

    if(is_array($response) && count($response) > 0) {
        foreach($response as $subscription) {
            ray($subscription);

            if(is_array($subscription) && !isset($subscription['engines'])) {
                $subscription_id = $subscription['id'];
                $response = $sdk->microservice()->createEngine( $subscription_id, $key );
                ray($response);
            }
        }
    }
} catch (\Exception $e) {
    ray($e);
}
