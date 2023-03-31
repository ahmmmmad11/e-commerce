<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilteringOrdersTest extends TestCase
{
    use RefreshDatabase;

    protected mixed $customer1;
    protected mixed $customer2;

    protected array $include_response_structure = [
        'data' => [
            0 => [
                'id',
                'customer_id',
                'status',
                'products' => [
                0 => [
                    'price',
                    'quantity',
                    'options',
                    'product' => [
                        'name',
                        'image'
                    ]
                ]
            ]
            ]
        ]
    ];

    public function test_seller_see_his_orders(): void
    {
        $this->createOrders();

        $seller = Seller::first();

        $user = User::factory()->create(['user_type' => $seller::class, 'user_id' => $seller->id]);

        $response = $this->actingAs($user)->get('/api/orders');

        $response->assertJsonStructure([
            'data' => [
                0 => ['id', 'customer_id', 'status']
            ]
        ]);

        $response->assertStatus(200);
    }

    public function test_seller_see_his_orders_with_included_products(): void
    {
        $this->createOrders();

        $seller = Seller::first();

        $user = User::factory()->create(['user_type' => $seller::class, 'user_id' => $seller->id]);

        $response = $this->actingAs($user)->get('/api/orders?include=products');

        $response->assertJsonStructure($this->include_response_structure);

        $response->assertStatus(200);
    }

    public function test_seller_with_no_orders_get_empty_array(): void
    {
        $this->createOrders();

        $seller = Seller::factory()->create();

        $user = User::factory()->create(['user_type' => $seller::class, 'user_id' => $seller->id]);

        $response = $this->actingAs($user)->get('/api/orders');

        $this->assertEmpty($response->json('data'));

        $response->assertStatus(200);
    }

    public function test_customer_see_his_orders(): void
    {
        $this->createOrders();

        $user = User::factory()->create(['user_type' => $this->customer1::class, 'user_id' => $this->customer1->id]);

        $response = $this->actingAs($user)->get('/api/orders');

        $response->assertJsonStructure([
            'data' => [
                0 => ['id', 'customer_id', 'status']
            ]
        ]);

        $response->assertStatus(200);
    }

    public function test_customer_see_his_orders_with_products(): void
    {
        $this->createOrders();

        $user = User::factory()->create(['user_type' => $this->customer1::class, 'user_id' => $this->customer1->id]);

        $response = $this->actingAs($user)->get('/api/orders?include=products');

        $response->assertJsonStructure($this->include_response_structure);

        $response->assertStatus(200);
    }

    public function test_customer_with_no_orders_get_empty_array(): void
    {
        $this->createOrders();

        $user = User::factory()->create(['user_type' => $this->customer2::class, 'user_id' => $this->customer2->id]);

        $response = $this->actingAs($user)->get('/api/orders');

        $this->assertEmpty($response->json('data'));

        $response->assertStatus(200);
    }

    public function createOrders()
    {
        Product::factory()->count(2)->create([
            'price' => 20,
            'options' => [
                [
                    'name' => 'size',
                    'options' => [
                        [
                            'value' => 'M',
                        ],
                        [
                            'value' => 'l',
                        ]
                    ],
                ]
            ]
        ]);

        Product::factory()->create([
            'price' => 10,
            'options' => [
                [
                    'name' => 'color',
                    'options' => [
                        [
                            'value' => 'brown',
                        ],
                        [
                            'value' => 'yellow',
                        ]
                    ],
                ],
            ]
        ]);

        $this->customer1 = Customer::factory()->create();
        $this->customer2 = Customer::factory()->create();

        $order = Order::create([
            'customer_id' => $this->customer1->id,
            'quantity' => 2,
            'amount' => 30,
            'total' => 30,
        ]);

        $order->products()->create(
            [
                'product_id' => 1,
                'price' => 20,
                'total' => 20,
                'quantity' => 1,
                'options' => [
                    'size' => 'M'
                ]
            ]
        );

        $order->products()->create(
            [
                'product_id' => 2,
                'price' => 10,
                'total' => 10,
                'quantity' => 1,
                'options' => [
                    'color' => 'red'
                ]
            ]
        );
    }
}
