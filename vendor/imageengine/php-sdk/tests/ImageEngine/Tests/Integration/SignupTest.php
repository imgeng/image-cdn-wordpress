<?php

declare(strict_types=1);

namespace ImageEngine\Tests\ImageEngine\Tests\Integration;

final class SignupTest extends TestCase
{
    public function testCanSignup(): void
    {
        $faker = \Faker\Factory::create();
        $email = $faker->email;
        $username = $email;
        $password = $faker->password;

        $sdk = $this->givenSdk();
        $response = $sdk->microservice()->signup($email, $username, $password);

        $this->canSignup($response, $email, $username);
    }


    /**
     * @param array|string $response
     * @param string $email
     * @param string $username
     */
    public function canSignup($response, $email, $username): void
    {
        $this->assertIsArray($response);
        $this->assertArrayHasKey('id', $response);

        $this->assertArrayHasKey('email', $response);
        $this->assertEquals($email, $response['email']);

        $this->assertArrayHasKey('username', $response);
        $this->assertEquals($username, $response['username']);

        $this->assertArrayHasKey('confirmed', $response);
        $this->assertTrue($response['confirmed']);

        $this->assertArrayHasKey('enabled', $response);
        $this->assertTrue($response['enabled']);
    }

    public function testCannotSignupTwice(): void
    {
        $faker = \Faker\Factory::create();
        $email = $faker->email;
        $username = $email;
        $password = $faker->password;

        $sdk = $this->givenSdk();
        $response = $sdk->microservice()->signup($email, $username, $password);

        $this->canSignup($response, $email, $username);

        $response = $sdk->microservice()->signup($email, $username, $password);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Duplicate entry: This user already exists', $response['message']);
    }
}
