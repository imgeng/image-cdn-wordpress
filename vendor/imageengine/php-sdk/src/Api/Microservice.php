<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk\Api;

use Exception;
use ImageEngine\PhpSdk\Config\APIData;
use ImageEngine\PhpSdk\Sdk;

final class Microservice extends AbstractApi
{
    protected Sdk $sdk;

    private const TOKEN = APIData::TOKEN;

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
        $this->sdk->setBaseUri('ie-microservice');
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function signup(string $email, string $username, string $password)
    {
        return $this->post('/api/v1/users', [
            'email'     => $email,
            'username'  => $username,
            'password'  => $password,
            "confirmed" => true,
            "enabled"   => true,
            'token' => self::TOKEN,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function apiKeys(int $user_id, string $token)
    {
        return $this->post('/api/v1/users/' . $user_id . '/api_keys', [
            'token' => $token,
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function user(int $user_id, string $key)
    {
        return $this->get('/api/v1/users/' . $user_id, [], [
            'X-Api-Key' => $key,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function subscriptions(int $user_id, string $key)
    {
        return $this->get('/api/v1/users/' . $user_id . '/subscriptions', [], [
            'X-Api-Key' => $key,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function createSubscription(int $user_id, string $key, string $plan = 'trial')
    {
        $user = $this->user($user_id, $key);

        if (!is_array($user) || !isset($user['username'])) {
            throw new \Exception("User not found");
        }

        $username = $user['username'];

        $host = $this->host();

        $paymentType = $plan == 'trial' ? 'TRIAL' : 'FREE';
        $paymentPlan = $plan == 'trial' ? 'IMAGEENGINE_STARTER' : 'IMAGEENGINE_DEV';

        return $this->post('/api/v2/users/' . $user_id . '/subscriptions', [
            'payment_plan' => $paymentPlan,
            'payment_type' => $paymentType,
            'account_name' => "$host $username"
        ], [
            'X-Api-Key' => $key,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function origins(int $subscription_id, string $key)
    {
        return $this->get('/api/v1/subscriptions/' . $subscription_id . '/origins', [], [
            'X-Api-Key' => $key,
        ]);
    }


	private function host(): string
	{
		$site_url = parse_url($this->sdk->getOptions()->getSiteUrl());
		return isset($site_url['host']) && $site_url['host'] ? $site_url['host'] : 'example.com';
	}


	private function scheme(): string
	{
		$site_url = parse_url($this->sdk->getOptions()->getSiteUrl());
		return isset($site_url['scheme']) && $site_url['scheme'] ? $site_url['scheme'] : 'https';
	}


    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function createEngine(int $subscription_id, string $key)
    {
        $host = $this->host();
        $scheme = $this->scheme();

        return $this->post('/api/v1/subscriptions/' . $subscription_id . '/engines', [
            'origin' => [
                'name' => $host,
                'hostname' => $host,
                'url_type' => $scheme,
            ]
        ], [
            'X-Api-Key' => $key,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function getSubscriptionBillingPeriods(int $subscription_id, string $key)
    {

        return $this->get('/api/v1/subscriptions/' . $subscription_id . '/billing/periods', [], [
            'X-Api-Key' => $key,
        ]);
    }

    /**
     *
     * @return array|string
     *
     * @throws Exception
     *
     */
    public function statistics(string $cname, string $key, array $subscription = null, array $subscriptionsResponse = null)
    {
        $start = strtotime(date('Y-m-01'));

        if($subscription &&
            !($subscription['payment_type'] == 'EXTERNAL' && $subscription['payment_plan'] == 'IMAGEENGINE_PRO')
        ) {
            try {
                $billingPeriods = $this->getSubscriptionBillingPeriods($subscription['id'], $key);
            } catch (Exception $e) {
                $billingPeriods = null;
            }

            $period = null;
            if(is_array($billingPeriods) && !empty($billingPeriods[0]["start_date"]) && is_array($subscriptionsResponse)) {
                foreach($subscriptionsResponse as $k => $subs) {
                    foreach ($subs['engines'] as $engine) {
                        if ($engine['cname'] == $cname) {
                            if(!empty($billingPeriods[$k])) {
                                $period = $billingPeriods[$k];
                                break 2;
                            }
                        }
                    }
                }
            }

            if($period && !empty($period["start_date"])) {
                $start_date = strtotime($period["start_date"]);
                $dayStart =  date('d', $start_date);
                $dayStarts = $dayStart < 10 ? '0' . $dayStart : $dayStart;

                $time = time();
                $day = date('d', $time);

                if($dayStart > $day) {
                    $start = strtotime(date("Y-m-$dayStarts", strtotime('-1 month')));
                } else {
                    $start = strtotime(date("Y-m-$dayStarts"));
                }
            }
        }

        return $this->get('/api/v1/statistics', [
            'cname' => $cname,
            'start' => $start,
            'end'   => time(),
        ], [
            'X-Api-Key' => $key,
        ]);
    }
}
