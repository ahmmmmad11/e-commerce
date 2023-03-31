<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Seller $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = Seller::factory()->create();

        $this->user = User::factory()->create([
            'user_type' => $this->seller::class,
            'user_id' => $this->seller->id
        ]);

        File::create(['name' => 'image.png']);

        $this->actingAs($this->user);
    }

    public function requestProvider(): Generator
    {
        $payload = [
            'category_id' => 1,
            'name' => '::name::',
            'price' => '50',
            'quantity' => 9,
            'image' => 'image.png',
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
                ]
            ],
        ];

        yield from [
            'missing category_id' =>  [
                'payload' => Arr::except($payload, 'category_id'),
                'key' => 'category_id'
            ],

            'missing name' =>  [
                'payload' => Arr::except($payload, 'name'),
                'key' => 'name'
            ],

            'missing price' =>  [
                'payload' => Arr::except($payload, 'price'),
                'key' => 'price'
            ],

            'missing image' =>  [
                'payload' => Arr::except($payload, 'image'),
                'key' => 'image'
            ],

            'missing options' =>  [
                'payload' => Arr::except($payload, 'options'),
                'key' => 'options'
            ],

            'required attribute not exists' =>  [
                'payload' => Arr::set($payload, 'options', [
                    [
                        'name' => 'size',
                        'options' => [
                            [
                                'value' => 'M',
                                'quantity' => 5,
                                'price' => 5
                            ],
                            [
                                'value' => 'X',
                                'quantity' => 4,
                                'price' => 6
                            ]
                        ]
                    ]
                ]),
                'key' => 'options'
            ],
        ];
    }

    /**
     * testing validation
     *
     * @dataProvider requestProvider
     */
    public function test_store_product_validation($payload, string|array $key)
    {
        if ($key !== 'category_id') {
            Category::factory()->create();
        }

        $response = $this->postJson(route('products.store'), $payload);

        $response->assertJsonValidationErrors($key);
    }

    public function test_store_product()
    {
        Category::factory()->create();

        $response = $this->postJson(route('products.store'), [
            'category_id' => 1,
            'name' => '::name::',
            'price' => '50',
            'quantity' => 9,
            'image' => 'image.png',
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
                ]
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas(Product::class, Arr::only($response->json()['data'], ['category_id', 'name']));
    }

    public function test_store_product_with_required_options()
    {
        $category = Category::factory()->create(['properties' => [
            [
                'name' => 'color',
                'required' => true,
            ],
            [
                'name' => 'weight',
                'required' => true
            ]
        ]]);

        $response = $this->postJson(route('products.store'), [
            'category_id' => $category->id,
            'name' => '::name::',
            'price' => '50',
            'quantity' => 9,
            'image' => 'image.png',
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
                            'value' => 'green',
                            'quantity' => 4,
                            'price' => 6
                        ]
                    ]
                ],
                [
                    'name' => 'weight',
                    'options' => [
                        [
                            'value' => '5Kg',
                            'quantity' => 5,
                            'price' => 5
                        ],
                    ]
                ]
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas(Product::class, Arr::only($response->json()['data'], ['category_id', 'name']));
    }

    public function test_cannot_store_product_if_required_options_not_existed_in_request_body()
    {
        $category = Category::factory()->create(['properties' => [
            [
                'name' => 'color',
                'required' => true,
            ],
            [
                'name' => 'weight',
                'required' => true
            ]
        ]]);

        $response = $this->postJson(route('products.store'), [
            'category_id' => $category->id,
            'name' => '::name::',
            'price' => '50',
            'quantity' => 9,
            'image' => 'image.png',
            'options' => [
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
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor('options');
    }

    public function test_cannot_store_product_if_restricted_option_values_not_provided()
    {
        $category = Category::factory()->create(['properties' => [
            [
                'name' => 'color',
                'required' => true,
                'options' => ['red', 'green', 'blue'],
                'restricted' => true
            ],
        ]]);

        $response = $this->postJson(route('products.store'), [
            'category_id' => $category->id,
            'name' => '::name::',
            'price' => '50',
            'quantity' => 9,
            'image' => 'image.png',
            'options' => [
                [
                    'name' => 'color',
                    'options' => [
                        [
                            'value' => 'blue',
                            'quantity' => 5,
                            'price' => 5
                        ],
                        [
                            'value' => 'brown',
                            'quantity' => 4,
                            'price' => 6
                        ]
                    ]
                ]
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor('options');
    }

    public function test_show_product()
    {
        $product = Product::factory()->create(['seller_id' => $this->seller->id]);

        $response = $this->getJson(route('products.show', $product->id));

        $response->assertOk();

        $response->assertJsonStructure(['data']);
    }

    public function test_update_product()
    {
        $product = Product::factory()->create(['seller_id' => $this->seller->id]);

        $response = $this->putJson(route('products.update', $product->id), [
            'category_id' => 1,
            'name' => '::name::',
            'price' => '50',
            'quantity' => 9,
            'image' => 'image.png',
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
                ]
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_cannot_update_product_not_belong_to_the_seller()
    {
        $product = Product::factory()->create();

        $response = $this->putJson(route('products.update', $product->id), [
            'category_id' => 1,
            'name' => '::name::',
            'price' => '50',
            'quantity' => 9,
            'image' => 'image.png',
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
                ]
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_product()
    {
        $product = Product::factory()->create(['seller_id' => $this->seller->id]);

        $response = $this->deleteJson(route('products.destroy', $product->id));

        $response->assertOk();

        $response->assertJsonStructure(['message']);

        $this->assertDatabaseMissing(Product::class, $product->toArray());
    }

    public function test_cannot_delete_product_not_belong_to_the_seller()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson(route('products.destroy', $product->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas(Product::class, ['id' => $product->id]);
    }
}
