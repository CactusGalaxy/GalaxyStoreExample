<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $this->attributes();
        $this->shopCategories();
        $this->productCards();
    }

    private function attributes(): void
    {
        $attributes = [
            // Колір
            [
                'display_type_in_card' => 'color',
                'key' => 'color',
                'uk' => ['name' => 'Колір'],
                'en' => ['name' => 'Color'],
                'values' => [
                    [
                        'uk' => ['name' => 'Red orange', 'value' => '#C93C20'],
                        'en' => ['name' => 'Red orange', 'value' => '#C93C20'],
                    ],
                    [
                        'uk' => ['name' => 'Graphite black', 'value' => '#1C1C1C'],
                        'en' => ['name' => 'Graphite black', 'value' => '#1C1C1C'],
                    ],
                    [
                        'uk' => ['name' => 'Broom yellow', 'value' => '#D6AE01'],
                        'en' => ['name' => 'Broom yellow', 'value' => '#D6AE01'],
                    ],
                    [
                        'uk' => ['name' => 'Beige', 'value' => '#C2B078'],
                        'en' => ['name' => 'Beige', 'value' => '#C2B078'],
                    ],
                    [
                        'uk' => ['name' => 'Black red', 'value' => '#412227'],
                        'en' => ['name' => 'Black red', 'value' => '#412227'],
                    ],
                    [
                        'uk' => ['name' => 'Grass green', 'value' => '#35682D'],
                        'en' => ['name' => 'Grass green', 'value' => '#35682D'],
                    ],
                    [
                        'uk' => ['name' => 'Steel blue', 'value' => '#231A24'],
                        'en' => ['name' => 'Steel blue', 'value' => '#231A24'],
                    ],
                    [
                        'uk' => ['name' => 'Olive drab', 'value' => '#25221B'],
                        'en' => ['name' => 'Olive drab', 'value' => '#25221B'],
                    ],
                    [
                        'uk' => ['name' => 'Luminous bright red', 'value' => '#FE0000'],
                        'en' => ['name' => 'Luminous bright red', 'value' => '#FE0000'],
                    ],
                    [
                        'uk' => ['name' => 'Blue green', 'value' => '#1F3A3D'],
                        'en' => ['name' => 'Blue green', 'value' => '#1F3A3D'],
                    ],
                    [
                        'uk' => ['name' => 'Lawn Green', 'value' => '#7CFC00'],
                        'en' => ['name' => 'Lawn Green', 'value' => '#7CFC00'],
                    ],
                    [
                        'uk' => ['name' => 'Linen', 'value' => '#FAF0E6'],
                        'en' => ['name' => 'Linen', 'value' => '#FAF0E6'],
                    ],
                    [
                        'uk' => ['name' => 'Magenta', 'value' => '#FF00FF'],
                        'en' => ['name' => 'Magenta', 'value' => '#FF00FF'],
                    ],
                    [
                        'uk' => ['name' => 'Moccasin', 'value' => '#FFE4B5'],
                        'en' => ['name' => 'Moccasin', 'value' => '#FFE4B5'],
                    ],
                    [
                        'uk' => ['name' => 'Wheat', 'value' => '#F5DEB3'],
                        'en' => ['name' => 'Wheat', 'value' => '#F5DEB3'],
                    ],
                    [
                        'uk' => ['name' => 'Chocolate', 'value' => '#D2691E'],
                        'en' => ['name' => 'Chocolate', 'value' => '#D2691E'],
                    ],
                    [
                        'uk' => ['name' => 'Light Grey', 'value' => '#D3D3D3'],
                        'en' => ['name' => 'Light Grey', 'value' => '#D3D3D3'],
                    ],
                    [
                        'uk' => ['name' => 'Salmon', 'value' => '#FA8072'],
                        'en' => ['name' => 'Salmon', 'value' => '#FA8072'],
                    ],
                    [
                        'uk' => ['name' => 'Purple', 'value' => '#800080'],
                        'en' => ['name' => 'Purple', 'value' => '#800080'],
                    ],
                    [
                        'uk' => ['name' => 'Black', 'value' => '#000000'],
                        'en' => ['name' => 'Black', 'value' => '#000000'],
                    ],
                ],
            ],
            // Розмір
            [
                'display_type_in_card' => 'size',
                'key' => 'size',
                'uk' => ['name' => 'Розмір'],
                'en' => ['name' => 'Size'],
                'values' => [
                    ['uk' => ['name' => 'XS'], 'en' => ['name' => 'XS']],
                    ['uk' => ['name' => 'S'], 'en' => ['name' => 'S']],
                    ['uk' => ['name' => 'M'], 'en' => ['name' => 'M']],
                    ['uk' => ['name' => 'L'], 'en' => ['name' => 'L']],
                    ['uk' => ['name' => 'XL'], 'en' => ['name' => 'XL']],
                ],
            ],
            // для кого
            [
                'display_type_in_card' => 'text',
                'key' => 'for_whom',
                'uk' => ['name' => 'Для кого'],
                'en' => ['name' => 'For whom'],
                'values' => [
                    ['uk' => ['name' => 'Чоловічі'], 'en' => ['name' => 'Men\'s']],
                    ['uk' => ['name' => 'Жіночі'], 'en' => ['name' => 'Women\'s']],
                    ['uk' => ['name' => 'Унісекс'], 'en' => ['name' => 'Unisex']],
                ],
            ],
        ];

        foreach ($attributes as $idx => $attribute) {
            $attributeModel = ProductAttribute::updateOrCreate([
                'key' => $attribute['key'],
            ],
                Arr::except([
                    ...$attribute,
                    'position' => $idx + 1,
                    'status' => 1,
                ], 'values'));

            $attributeModel->attributeValues()->delete();

            foreach ($attribute['values'] as $key => $value) {
                AttributeValue::create([
                    ...$value,
                    'product_attribute_id' => $attributeModel->id,
                    'position' => $key + 1,
                    'status' => 1,
                ]);
            }
        }
    }

    private function shopCategories(): void
    {
        Category::query()->delete();

        $categories = [
            // generate 2 specific
            [
                'slug' => 't-shirt',
                'position' => 11,
                'status' => 1,
                'uk' => ['title' => 'Футболки'],
                'en' => ['title' => 'T-shirts'],
            ],
            [
                'slug' => 'pants',
                'position' => 12,
                'status' => 1,
                'uk' => ['title' => 'Штани'],
                'en' => ['title' => 'Pants'],
            ],
            // and 10 random
            ...collect(range(1, 10))->map(fn ($idx) => [
                'slug' => 'category-' . $idx,
                'position' => $idx,
                'status' => 1,
                'uk' => ['title' => 'Категорія ' . $idx],
                'en' => ['title' => 'Category ' . $idx],
            ])->toArray(),
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate([
                'slug' => $category['slug'],
            ], $category);
        }
    }

    private function productCards(): void
    {
        // generate cards only for first two categories
        $shopCategories = Category::query()
            ->limit(2)
            ->get();

        // use general attributes for card as characteristic
        $productAttributes = ProductAttribute::query()
            ->with('attributeValues')
            ->get();

        $shopCategories
            ->each(fn (Category $Category) => Product::factory()
                ->count(20)
                ->for($Category)
                ->create()
                ->each(function (Product $product) use ($productAttributes) {
                    foreach ($productAttributes as $productAttribute) {
                        $product->productAttributeValues()->create([
                            'product_attribute_id' => $productAttribute->id,
                            'attribute_value_id' => $productAttribute->attributeValues->random()->id,
                        ]);
                    }
                }));
    }
}
