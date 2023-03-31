<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Seller;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    public function requestProvider(): Generator
    {
        $payload = [
            'country' => 'country',
            'state' => 'state',
            'province' => 'province',
            'city' => 'city',
            'st_1' => 'st_1',
            'st_2' => 'st_2',
            'description' => 'description',
        ];

        yield from [
            'missing city' =>  [
                'payload' => Arr::except($payload, 'city'),
                'key' => 'city'
            ],

            'missing state and province' =>  [
                'payload' => Arr::except($payload, ['state', 'province']),
                'key' => ['state', 'province']
            ],

            'missing st1' =>  [
                'payload' => Arr::except($payload, 'st_1'),
                'key' => 'st_1'
            ],
        ];
    }

    /**
     * testing validation
     *
     * @dataProvider requestProvider
     */
    public function test_store_address_validation($payload, string|array $key)
    {
        $response = $this->postJson(route('addresses.store'), $payload);

        $response->assertJsonValidationErrors($key);
    }

    public function test_store_new_address()
    {
        $response = $this->postJson(route('addresses.store'), [
            'country' => 'country',
            'state' => 'state',
            'province' => 'province',
            'city' => 'city',
            'st_1' => 'st_1',
            'st_2' => 'st_2',
            'description' => 'description',
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'country',
                'city',
                'st_1'
            ],
            'message'
        ]);
    }

    public function test_update_user_address()
    {
        $address = Address::factory()->create([
            'addressable_type' => $this->user->user::class,
            'addressable_id' => $this->user->user->id,
        ]);

        $response = $this->putJson(route('addresses.update', $address->id), [
            'country' => 'country',
            'state' => 'state',
            'province' => 'province',
            'city' => 'city',
            'st_1' => 'st_1',
            'st_2' => 'st_2',
            'description' => 'description',
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'country',
                'city',
                'st_1'
            ],
            'message'
        ]);
    }

    public function test_user_cannot_update_other_user_address()
    {
        $seller = Seller::factory()->create();

        $address = Address::factory()->create([
            'addressable_type' => $seller::class,
            'addressable_id' => '2',
        ]);

        $response = $this->putJson(route('addresses.update', $address->id), [
            'country' => 'country',
            'state' => 'state',
            'province' => 'province',
            'city' => 'city',
            'st_1' => 'st_1',
            'st_2' => 'st_2',
            'description' => 'description',
        ]);

        $response->assertStatus(403);
    }
}
