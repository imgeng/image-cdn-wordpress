<?php

namespace ImageEngine\PhpSdk;

use Exception;
use ImageEngine\PhpSdk\Storage\FileStorage;
use ImageEngine\PhpSdk\Storage\StorageInterface;

final class IEClient
{
    private Sdk $sdk;

    private StorageInterface $storage;

    /**
     *
     * @param StorageInterface|null $storage
     * @param Options|null $options
     *
     */
    public function __construct(StorageInterface $storage = null, Options $options = null)
    {
        $this->sdk = new Sdk($options);

        $this->storage = $storage ?? new FileStorage();
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception|\Http\Client\Exception
     *
     */
    public function register(string $username, string $password, string $plan = 'trial')
    {
        try {
            $registerResponse = $this->sdk->microservice()->signup($username, $username, $password);
            if (!is_array($registerResponse) || !isset($registerResponse['id'])) {
                if (
                    (is_array($registerResponse)
                        && isset($registerResponse['success'])
                        && !$registerResponse['success']
                        && isset($registerResponse['message'])
                        && $registerResponse['message'] == 'Duplicate entry: This user already exists')
                ) {
                    return $this->login($username, $password, $plan);
                }
                throw new Exception("Failed signup");
            }
            return $this->login($username, $password, $plan);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception|\Http\Client\Exception
     *
     */
    public function login(string $username, string $password, string $plan = 'trial')
    {
        try {
            $loginResponse = $this->sdk->authentication()->login($username, $password);
            if (!is_array($loginResponse) || !isset($loginResponse['token'])) {
                if(is_array($loginResponse) && isset($loginResponse['message']) && $loginResponse['message'] == 'unconfirmed-email') {
                    throw new \Exception("User email address needs verification!");
                }
                throw new Exception("Failed login");
            }

            $refresh_token = $loginResponse['token'];
            $tokenResponse = $this->sdk->authentication()->token($refresh_token);
            if (!is_array($tokenResponse) || !isset($tokenResponse['token'])) {
                throw new Exception("Failed token");
            }

            $token = $tokenResponse['token'];
            $user_id = $tokenResponse['user_id'];
            $apiKeyResponse = $this->sdk->microservice()->apiKeys($user_id, $token);
            if (!is_array($apiKeyResponse) || !isset($apiKeyResponse['key'])) {
                throw new Exception("Failed api keys");
            }

            $key = $apiKeyResponse['key'];
            $this->storage->set('userId', $user_id);
            $this->storage->set('apiKey', $key);

            return $this->deliveryAddress($plan);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception|\Http\Client\Exception
     *
     */
    public function deliveryAddress(string $plan = 'trial')
    {
        try {
            $api_key = $this->storage->get('apiKey');
            $user_id = (int)$this->storage->get('userId');

            if (!$api_key || !$user_id) {
                throw new Exception("User not logged in");
            }

            $subscriptionsResponse = $this->sdk->microservice()->subscriptions($user_id, $api_key);

            if (is_array($subscriptionsResponse)) {
                $subscriptionsResponse = array_values(array_filter($subscriptionsResponse, function ($subscription) {
                    return $subscription['status'] != "CANCELED";
                }));
            }

            if (is_array($subscriptionsResponse) && count($subscriptionsResponse) < 1) {
                $this->sdk->microservice()->createSubscription($user_id, $api_key, $plan);
                $subscriptionsResponse = $this->sdk->microservice()->subscriptions($user_id, $api_key);

                if (is_array($subscriptionsResponse)) {
                    $subscriptionsResponse = array_values(
                        array_filter($subscriptionsResponse, function ($subscription) {
                            return $subscription['status'] != "CANCELED";
                        })
                    );
                }
            }

            $engines = [];

            if (is_array($subscriptionsResponse) && count($subscriptionsResponse) > 0) {
                foreach ($subscriptionsResponse as $subscription) {
                    if (isset($subscription['engines'])) {
                        foreach ($subscription['engines'] as $engine) {
                            $engines[] = $engine['cname'];
                        }
                    }
                }

                if (count($engines) > 0) {
                    $encodedEngines = json_encode($engines);
                    $this->storage->set('deliveryAddresses', $encodedEngines ? $encodedEngines : '');
                    return count($engines) > 1 ? $engines : $engines[0];
                }

                $subscription_id = $subscriptionsResponse[0]['id'];

                $response = $this->sdk->microservice()->createEngine($subscription_id, $api_key);

                if (is_array($response) && isset($response['cname'])) {
                    return $response['cname'];
                }
            }
            throw new Exception("Failed delivery address");
        } catch (Exception $e) {
            throw new Exception("Failed to get delivery address");
        }
    }

    /**
     *
     * @return bool
     *
     * @throws Exception
     */
    public function logout(): bool
    {
        $this->storage->delete('userId');
        $this->storage->delete('apiKey');
        $this->storage->delete('deliveryAddresses');
        return true;
    }

    /**
     *
     * @return bool
     *
     * @throws Exception
     */
    public function isLoggedIn(): bool
    {
        $user_id = (int)$this->storage->get('userId');
        return $user_id > 0;
    }

    /**
     *
     * @throws Exception
     *
     */
    public function getStoredDeliveryAddresses(): ?array
    {
        try {
            $deliveryAddresses = $this->storage->get('deliveryAddresses');
            if (!$deliveryAddresses) {
                return null;
            }
            return json_decode($deliveryAddresses);
        } catch (Exception $e) {
            throw new Exception("Failed to get stored delivery addresses");
        }
    }

    /**
     *
     * @return string
     *
     * @throws Exception
     */
    public function getObfuscatedApiKey(): string
    {
        $result = $this->storage->get('apiKey');
        if (!$result) {
            return '';
        }
        //obfuscated (first 10 characters)
        return substr($result, 0, 10) . str_repeat('*', strlen($result) - 10);
    }

    /**
     *
     * Bytes formats bytes into human-readable format
     *
     * @param int|float $bytes
     * @param int $decimals
     *
     * @return string
     */
    public function bytesNice($bytes, int $decimals = 1): string
    {
        $mod = 1000;
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];
        $index = 0;
        for ($index; $bytes > $mod && $index < count($units) - 1; $index++) {
            $bytes /= $mod;
        }

        return trim(number_format($bytes, $decimals) . ' ' . $units[$index]);
    }

    /**
     *
     * @param array $statistics
     *
     * @return string
     */
    private function smartBytes(array $statistics): string
    {
        $smart_bytes = 0;
        if (! empty($statistics['data_over_time']) && is_array($statistics['data_over_time'])) {
            foreach ($statistics['data_over_time'] as $data) {
                $smart_bytes += $data['bytes']['final'];

                if (!empty($data['non_image_bytes'])) {
                    $smart_bytes += $data['non_image_bytes'];
                }
            }
        }
        return $this->bytesNice($smart_bytes, 0);
    }

    /**
     *
     * @param array $statistics
     *
     * @return array
     */
    private function computeDataOverTime(array $statistics): array
    {
        $original = 0;
        $saved    = 0;
        $final    = 0;
        $dataKey = 'data_over_time';
        $bytesKey = 'bytes';
        if (! empty($statistics[$dataKey]) && is_array($statistics[$dataKey])) {
            foreach ($statistics[$dataKey] as $data) {
                if (isset($data[$bytesKey])) {
                    $original += $data[$bytesKey]['original'];
                    $saved += $data[$bytesKey]['saved'];
                    $final += $data[$bytesKey]['final'];
                }
            }
        }
        return [ $original, $saved, $final ];
    }

    /**
     *
     * @param array $statistics
     *
     * @return array
     */
    private function payloadReduction(array $statistics): array
    {
        list($original, $saved, $final)  = $this->computeDataOverTime($statistics);

        return [
            'percent' => ($original) ? round(( $saved / $original ) * 100, 2) : 0,
            'original' => $this->bytesNice($original, 0),
            'optimized' => $this->bytesNice($final, 0),
            'saved' => $this->bytesNice($saved, 0)
        ];
    }

    /**
     *
     * @param int|float $number
     *
     * @return string
     */
    private function numberNice($number): string
    {
        if (empty($number)) {
            return "0";
        }
        return $number > 1000 ?
            number_format($number / 1000, 1) . ' k' :
            number_format($number, 1) . ' ';
    }

    /**
     *
     * @param array $statistics
     *
     * @return array
     */
    private function cacheHitRatioNice(array $statistics): array
    {
        $cache_hit_ratio_nice = [];
        $cache_hit_ratio_nice['hits'] = $this->numberNice($statistics['cache_hit_ratio']['hits']);
        $cache_hit_ratio_nice['misses'] = $this->numberNice($statistics['cache_hit_ratio']['misses']);
        $cache_hit_ratio_nice['hit_percentage'] = $statistics['cache_hit_ratio']['hit_percentage'];
        $cache_hit_ratio_nice['miss_percentage'] = $statistics['cache_hit_ratio']['miss_percentage'];
        return $cache_hit_ratio_nice;
    }

    /**
     * @throws Exception|\Http\Client\Exception
     */
    private function carbonEmissionSaved(array $statistics): array
    {
        list($original, $saved, $final)  = $this->computeDataOverTime($statistics);

        try {
            return [
                'original' => $this->co2($original),
                'saved' => $this->co2($saved),
                'final' => $this->co2($final),
            ];
        } catch (Exception $e) {
            throw new Exception("Failed to get co2");
        }
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception|\Http\Client\Exception
     *
     */
    public function statistics(string $cname)
    {
        try {
            $api_key = $this->storage->get('apiKey');
            $user_id = (int)$this->storage->get('userId');
            if (!$api_key || !$user_id) {
                throw new Exception("User not logged in");
            }

            $subscriptionsResponse = $this->sdk->microservice()->subscriptions($user_id, $api_key);

            if (is_array($subscriptionsResponse)) {
                $subscriptionsResponse = array_values(array_filter($subscriptionsResponse, function ($subscription) {
                    return $subscription['status'] != "CANCELED";
                }));
            }

            if (is_array($subscriptionsResponse)) {
                foreach ($subscriptionsResponse as $subscription) {
                    if (isset($subscription['engines'])) {
                        foreach ($subscription['engines'] as $engine) {
                            if ($engine['cname'] == $cname) {
                                break 2;
                            }
                        }
                    }
                }
            }
            if (empty($subscription)) {
                throw new Exception("Failed to get subscription");
            }

            $statisticsResponse = $this->sdk->microservice()
                ->statistics(
                    $cname,
                    $api_key,
                    $subscription,
                    is_array($subscriptionsResponse) ? $subscriptionsResponse : null
                );

            if (!is_array($statisticsResponse)) {
                throw new Exception("Failed statistics");
            }

            $statisticsResponse['carbon_emissions_saved'] = $this->carbonEmissionSaved($statisticsResponse);

            $statisticsResponse['smart_bytes'] = $this->smartBytes($statisticsResponse);

            $statisticsResponse['payload_reduction'] = $this->payloadReduction($statisticsResponse);

            $statisticsResponse['cache_hit_ratio_nice'] = $this->cacheHitRatioNice($statisticsResponse);

            return $statisticsResponse;
        } catch (Exception $e) {
            throw new Exception("Failed to get statistics");
        }
    }

    /**
     *
     * @param int|float $bytes
     *
     * @return array|string
     *
     * @throws Exception|\Http\Client\Exception
     *
     */
    public function co2($bytes)
    {
        try {
            $co2Response = $this->sdk->co2()->co2($bytes);

            if (!is_array($co2Response)) {
                throw new Exception("Failed co2");
            }

            if ($co2Response['carbonFootprintTonnes'] > 1) {
                $v = round($co2Response['carbonFootprintTonnes'], 0);
                $co2Response['nice'] = $v > 1 ? $v . ' tonnes' : $v . ' tonne';
            } elseif ($co2Response['carbonFootprintKilos'] > 1) {
                $v = round($co2Response['carbonFootprintKilos'], 0);
                $co2Response['nice'] = $v > 1 ? $v . ' kilos' : $v . ' kilo';
            } else {
                $v = round($co2Response['carbonFootprintGrams'], 0);
                $co2Response['nice'] = $v > 1 ? $v . ' grams' : $v . ' gram';
            }

            return $co2Response;
        } catch (Exception $e) {
            throw new Exception("Failed to get co2");
        }
    }
}
