<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = ['White Nile', 'Khartoum', 'Blue Nile'];

        return [
            'state' => $this->faker->randomElement($states),
            'city' => $this->faker->city,
            'st_1' => $this->faker->streetName,
            'description' => $this->faker->streetAddress
        ];
    }
}
