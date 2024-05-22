<?php

use Http\Client\Common\Plugin\HeaderDefaultsPlugin;

use ImageEngine\PhpSdk\ClientBuilder;
use ImageEngine\PhpSdk\IEClient;
use ImageEngine\PhpSdk\Options;
use ImageEngine\PhpSdk\Storage\RedisStorage;

require_once __DIR__ . '/../vendor/autoload.php';

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379, 0.1);
} catch (Exception $e) {
    $redis = null;
}
if ($redis) {
    $storage = new RedisStorage($redis);
}

$clientBuilder = new ClientBuilder(new Http\Client\Curl\Client());
$clientBuilder->addPlugin(new HeaderDefaultsPlugin([
	'Accept' => 'application/json',
]));

// usage
$options = new Options([
	'client_builder' => $clientBuilder,
]);

$client = new IEClient($storage, $options);
try {
	$response = $client->register('test@example.com', 'password');
    ray($response);
} catch (\Exception $e) {
    ray($e);
}
