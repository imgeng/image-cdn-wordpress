<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

final class ApiKeysTest extends TestCase
{
    public function testCanGetApiKeyAndUseIt(): void
    {
        $username = "test@example.com";
        $password = "poiepodldfkijtPOED9";

        $sdk = $this->givenSdk();
        $response = $sdk->authentication()->login($username, $password);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('token', $response);

        $refresh_token = $response['token'];

        $response = $sdk->authentication()->token($refresh_token);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('user_id', $response);

        $user_id = $response['user_id'];
        $token = $response['token'];

        $response = $sdk->microservice()->apiKeys($user_id, $token);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('key', $response);
        $this->assertArrayHasKey('user_id', $response);
        $this->assertEquals($response['user_id'], $user_id);

        $key = $response['key']; //OTU1Mjc1OnVqOWVwUWNXTEhyQW5helN5VUF0UWRwclVSWFBUU2Jk

        $response = $sdk->microservice()->user($user_id, $key);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals($user_id, $response['id']);
    }

    public function testCanUseKey(): void
    {
        $sdk = $this->givenSdk();
        $user_id = 31623;
        $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMHU3';

        $response = $sdk->microservice()->user($user_id, $key);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals($user_id, $response['id']);
    }

    public function testCannotUseWrongKey(): void
    {
        $sdk = $this->givenSdk();
        $user_id = 31623;
        $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMH2';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized');

        $sdk->microservice()->user($user_id, $key);
    }

    public function testErrorOnForbiddenAction(): void
    {
        $sdk = $this->givenSdk();
        $user_id = 1;
        $key = 'NDY3MzcwOjBkMXFSeGpjcDE5M2RuTWJtRGFtNDIyQkFGaXNRMHU3';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Forbidden');

        $sdk->microservice()->user($user_id, $key);
    }
}
