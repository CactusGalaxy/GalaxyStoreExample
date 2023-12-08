<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BannerFactory extends Factory
{
    use FactoryWithTranslations;

    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'position' => $this->faker->randomNumber(),
            'status' => $this->faker->boolean(),
            'image' => $this->faker->imageUrl(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    protected function getTitleTranslation(int $words = 6): string
    {
        return $this->faker->sentence(2);
    }

    protected function getDescriptionTranslation(int $nb = 3): string
    {
        return $this->faker->sentence(10);
    }
}
