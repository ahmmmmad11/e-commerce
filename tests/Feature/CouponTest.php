<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponProduct;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    private Seller $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = Seller::factory()->create();

        $user = User::factory()->create([
            'user_type' => $this->seller::class,
            'user_id' => $this->seller->id
        ]);

        $this->actingAs($user);
    }

    public function requestProvider(): Generator
    {
        $payload = [
            'coupon' => '::name::',
            'type' => 'value',
            'amount' => 9,
            'end_at' => '2023/04/01',
        ];

        yield from [
            'missing coupon' =>  [
                'payload' => Arr::except($payload, 'coupon'),
                'key' => 'coupon'
            ],

            'missing type' =>  [
                'payload' => Arr::set($payload, 'type', 'any'),
                'key' => 'type'
            ],

            'missing amount' =>  [
                'payload' => Arr::except($payload, 'amount'),
                'key' => 'amount'
            ],
        ];
    }

    /**
     * testing validation
     *
     * @dataProvider requestProvider
     */
    public function test_store_coupon_validation($payload, string|array $key)
    {
        $response = $this->postJson(route('coupons.store'), $payload);

        $response->assertJsonValidationErrors($key);
    }

    public function test_store_coupon()
    {
        $payload = [
            'coupon' => '::name::',
            'type' => 'value',
            'amount' => 9,
            'end_at' => '2023/04/01',
        ];

        $response = $this->postJson(route('coupons.store'), $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas(Coupon::class, $payload);
    }

    public function test_update_coupon()
    {
        $coupon = Coupon::factory()->create(['seller_id' => $this->seller->id]);

        $payload = [
            'coupon' => 'new',
            'type' => 'percent',
            'amount' => 9,
            'end_at' => '2023/04/01',
        ];

        $response = $this->putJson(route('coupons.update', $coupon->id), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas(Coupon::class, $payload);
    }

    public function test_update_coupon_status()
    {
        $coupon = Coupon::factory()->create(['seller_id' => $this->seller->id]);

        $payload = [
            'status' => 'disabled',
        ];

        $response = $this->putJson(route('coupon.status.update', $coupon->id), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas(Coupon::class, $payload);
    }

    public function test_delete_coupon()
    {
        $coupon = Coupon::factory()->create(['seller_id' => $this->seller->id]);

        $response = $this->deleteJson(route('coupons.destroy', $coupon->id));

        $response->assertOk();

        $response->assertJsonStructure(['message']);

        $this->assertDatabaseMissing(Coupon::class, ['id' => $coupon->id]);
    }

    public function test_add_products_to_coupon()
    {
        $coupon = Coupon::factory()->create(['amount' => 10, 'seller_id' => $this->seller->id]);

        $products = Product::factory()->count(10)->create(['price' => 20, 'seller_id' => $this->seller->id]);

        $response = $this->postJson(route('coupon.products.store'), [
            'products' => $products->pluck('id'),
            'coupon_id' => $coupon->id
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas(CouponProduct::class, ['coupon_id' => $coupon->id]);
    }
}
