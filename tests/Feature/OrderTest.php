<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $customer = Customer::factory()->create();

        $this->actingAs(User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]));
    }

    public function test_create_single_order()
    {
        $this->singleProduct();

        $response = $this->postJson(route('orders.store'), [
            'products' => [
                [
                    'id' => 1,
                    'quantity' => 2,
                    'options' => [
                        'color' => 'red',
                        'size' => 'M'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'products'
            ]
        ]);

        $this->assertEquals(3, Product::find(1)->options
            ->where('name', 'color')
            ->pluck('options')
            ->flatten(1)
            ->where('value', 'red')
            ->first()['quantity']
        );
    }

    public function test_create_single_order_with_coupon()
    {
        $product = $this->singleProduct();

        $coupon = Coupon::factory()->create([
            'seller_id' => $product->seller_id,
            'type' => 'value',
            'amount' => 2
        ]);

        $response = $this->postJson(route('orders.store'), [
            'products' => [
                [
                    'id' => 1,
                    'quantity' => 2,
                    'coupon' => $coupon->coupon,
                    'options' => [
                        'color' => 'red',
                        'size' => 'M'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'products'
            ]
        ]);

        $this->assertEquals(3, Product::find(1)->options
            ->where('name', 'color')
            ->pluck('options')
            ->flatten(1)
            ->where('value', 'red')
            ->first()['quantity']
        );
    }

    private function singleProduct()
    {
        return Product::factory()->create([
            'quantity' => 9,
            'options' => [
                [
                    'name' => 'color',
                    'options' => [
                        [
                            'value' => 'red',
                            'quantity' => 5,
                            'price' => 5
                        ],
                        [
                            'value' => 'blue',
                            'quantity' => 4,
                            'price' => 6
                        ]
                    ]
                ],
                [
                    'name' => 'size',
                    'options' => [
                        [
                            'value' => 'M',
                            'quantity' => 5,
                            'price' => 5
                        ],
                        [
                            'value' => 'L',
                            'quantity' => 4,
                            'price' => 6
                        ]
                    ]
                ]
            ]
        ]);
    }
}
