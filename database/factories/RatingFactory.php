<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory()->create()->id,
            'customer_id' => Customer::factory()->create()->id,
            'stars' => $this->faker->biasedNumberBetween(1, 5),
            'comment' => $this->faker->sentence
        ];
    }
}
