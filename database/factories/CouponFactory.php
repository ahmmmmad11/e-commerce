<?php

namespace Database\Factories;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['value', 'percentage']);

        $amount = $type == 'value' ? $this->faker->randomNumber() : $this->faker->randomNumber(2);

        return [
            'seller_id' => Seller::factory()->create()->id,
            'coupon' => Str::random(6),
            'type' => $type,
            'amount' => $amount,
            'end_at' => now()->addDays(7)
        ];
    }
}
