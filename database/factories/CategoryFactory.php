<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryFactory extends Factory
{
    use FactoryWithTranslations;

    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug(),
            'image' => $this->faker->imageUrl(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    protected function getTitleTranslation(int $words = 6): string
    {
        return $this->faker->words(2, true);
    }
}
