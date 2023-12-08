<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductFactory extends Factory
{
    use FactoryWithTranslations;

    protected $model = Product::class;

    public function configure(): static
    {
        return $this
            ->extendsConfigure()
            ->afterMaking(function (Product $product) {
                $product->fill([
                    'uk' => [
                        'characteristics' => [
                            'Вага' => '1 кг',
                            'Розміри' => '10x10x10 см',
                        ],
                    ],
                    'en' => [
                        'characteristics' => [
                            'Weight' => '1 kg',
                            'Dimensions' => '10x10x10 cm',
                        ],
                    ],
                ]);
            });
    }

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'sku' => $this->faker->numerify('#####'),
            'image' => $this->faker->imageUrl(600, 700),
            'sliders' => [
                [
                    'image' => $this->faker->imageUrl(600, 700),
                ],
                [
                    'image' => $this->faker->imageUrl(600, 700),
                ],
            ],
            'price' => $this->faker->numberBetween(3000, 6000),
            'quantity' => $this->faker->numberBetween(1, 50),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'category_id' => Category::factory(),
        ];
    }
}
