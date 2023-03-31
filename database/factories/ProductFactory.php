<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomDigitNotZero();

        $options = [
            [
                [
                    'name' => 'color',
                    'options' => [
                        [
                            'value' => 'red',
                            'quantity' => $color_quantity = rand(0, $quantity),
                            'price' => $this->faker->randomDigitNotZero()
                        ],
                        [
                            'value' => 'blue',
                            'quantity' => rand(0, $quantity) - $color_quantity,
                            'price' => $this->faker->randomDigitNotZero()
                        ]
                    ],
                ],
                [
                    'name' => 'size',
                    'options' => [
                        [
                            'value' => 'M',
                            'quantity' => $quantity
                        ],
                        [
                            'value' => 'l',
                            'quantity' => $quantity,
                            'price' => $this->faker->randomDigitNotZero()
                        ]
                    ],
                ],
                [
                    'name' => 'weight',
                    'options' => [
                        'value' => '50 G'
                    ]
                ]
            ],

            [
                [
                    'name' => 'RAM',
                    'options' => [
                        [
                            'value' => '8 GB',
                            'quantity' => $color_quantity = rand(0, $quantity),
                        ],
                        [
                            'value' => '16 GB',
                            'quantity' => rand(0, $quantity) - $color_quantity,
                            'price' => $this->faker->randomDigitNotZero()
                        ]
                    ],
                ],
                [
                    'name' => 'ROM',
                    'options' => [
                        [
                            'value' => '250 GB',
                            'quantity' => $quantity
                        ],
                        [
                            'value' => '500 GB',
                            'quantity' => $quantity,
                            'price' => $this->faker->randomDigitNotZero()
                        ]
                    ],
                ],
                [
                    'name' => 'weight',
                    'options' => [
                        'value' => '2 KG'
                    ]
                ]
            ],
        ];

        return [
            'category_id' => Category::factory()->create()->id,
            'seller_id' => Seller::factory()->create()->id,
            'name' => $this->faker->word(),
            'price' => $this->faker->randomDigitNotZero(),
            'quantity' => $quantity,
            'image' => $this->faker->imageUrl,
            'options' => $this->faker->randomElement($options)
        ];
    }
}
