<?php

namespace Tests\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function requestProvider(): Generator
    {
        $payload = [
            'name' => '::name::',
            'email' => 'unique@email.com',
            'phone' => '0123456789',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        yield from [
            'missing name' =>  [
                'payload' => Arr::except($payload, 'name'),
                'key' => 'name'
            ],

            'missing email and phone' =>  [
                'payload' => Arr::except($payload, ['email', 'phone']),
                'key' => ['email', 'phone']
            ],

            'missing password' =>  [
                'payload' => Arr::except($payload, 'password'),
                'key' => 'password'
            ],

            'missing password_confirmation' =>  [
                'payload' => Arr::except($payload, 'password'),
                'key' => 'password'
            ],
        ];
    }

    /**
     * testing validation
     *
     * @dataProvider requestProvider
     */
    public function test_class_store_validation($payload, string|array $key)
    {
        $response = $this->postJson(route('register'), $payload);

        $response->assertJsonValidationErrors($key);
    }

    public function test_customer_registration()
    {
        $response = $this->postJson(route('register'), [
            'name' => '::name::',
            'email' => 'unique@email.com',
            'phone' => '0123456789',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message', 'user'
        ]);
    }

    public function test_customer_registration_with_email_only()
    {
        $response = $this->postJson(route('register'), [
            'name' => '::name::',
            'email' => 'unique1@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message', 'user'
        ]);
    }

    public function test_customer_registration_with_phone_only()
    {
        $response = $this->postJson(route('register'), [
            'name' => '::name::',
            'phone' => '0123456788',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message', 'user'
        ]);
    }
}
