<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilteringProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_only_see_his_products()
    {
        Product::factory()->count(10)->create();

        $seller = Seller::first();

        $user = User::factory()->create(['user_type' => $seller::class, 'user_id' => $seller->id]);

        $response = $this->actingAs($user)->getJson('/api/products');

        $this->assertCount(1, $response->json('data'));
    }

    public function test_customer_see_all_products()
    {
        Product::factory()->count(10)->create();

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson('/api/products');

        $this->assertCount(10, $response->json('data'));
    }

    public function test_customer_cannot_see_disabled_products()
    {
        Product::factory()->count(10)->create();

        Product::first()->update(['status' => 'disabled']);

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson('/api/products');

        $this->assertCount(9, $response->json('data'));
    }

    public function test_customer_cannot_see_out_of_stock_products_by_default()
    {
        Product::factory()->count(10)->create();

        Product::first()->update(['quantity' => '0']);

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson('/api/products');

        $this->assertCount(9, $response->json('data'));
    }

    public function test_customer_can_see_out_of_stock_products_by_allowing_in_stock_filter()
    {
        Product::factory()->count(10)->create();

        Product::first()->update(['quantity' => '0']);

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson('/api/products?filter[in_stock]=false');

        $this->assertCount(10, $response->json('data'));
    }

    public function test_filter_products_by_ratings()
    {
        Product::factory()->count(10)->create();

        $product = Product::first();

        Rating::factory()->count(10)->create([
            'stars' => 3,
            'product_id' => $product->id
        ]);

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson("/api/products?filter[rating]=3");

        $this->assertCount(1, $response->json('data'));
    }

    public function test_filter_products_by_name()
    {
        Product::factory()->count(10)->create();
        Product::factory()->create(['name' => 'someName']);

        $product = Product::first();

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson("/api/products?filter[name]=someName");

        $this->assertCount(1, $response->json('data'));
    }

    public function test_filter_products_by_option_name()
    {
        Product::factory()->count(2)->create([
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

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson("/api/products?filter[options]['name']=color");

        $this->assertCount(1, $response->json('data'));
    }

    public function test_filter_products_by_option_value()
    {
        Product::factory()->count(2)->create();
        Product::factory()->create([
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

        $customer = Customer::factory()->create();

        $user = User::factory()->create(['user_type' => $customer::class, 'user_id' => $customer->id]);

        $response = $this->actingAs($user)->getJson("/api/products?filter[options]['value']=yellow");

        $this->assertCount(1, $response->json('data'));
    }
}
