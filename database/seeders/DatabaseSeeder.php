<?php

namespace Database\Seeders;

use App\Data\HomePage\HeroSection;
use App\Data\HomePage\PromoSection;
use App\Data\Image;
use App\Data\Localised;
use App\Models\HomePageInfo;
use App\Models\User;
use File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $storagePath = storage_path('app/public/samples');

        File::deleteDirectory($storagePath);
        File::ensureDirectoryExists($storagePath);

        User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => 'admin',
            'is_admin' => true,
        ]);

        $this->call([
            BannerSeeder::class,
            ShopSeeder::class,
        ]);

        $this->crateHomePageInfo();
    }

    private function crateHomePageInfo(): void
    {
        HomePageInfo::truncate();
        HomePageInfo::create([
            'hero_section' => new HeroSection(
                image: new Image(path: $this->getImage()),
                textLeft: new Localised(translations: [
                    'uk' => fake('uk')->words(asText: true),
                    'en' => fake()->words(asText: true),
                ]),
                textRight: new Localised(translations: [
                    'uk' => fake('uk')->words(asText: true),
                    'en' => fake()->words(asText: true),
                ]),
                quote: new Localised(translations: [
                    'uk' => fake('uk')->paragraph,
                    'en' => fake()->paragraph,
                ]),
            ),
            'promo_section' => new PromoSection(
                mainImage: new Image(path: $this->getImage()),
                title: new Localised(translations: [
                    'uk' => fake('uk')->words(asText: true),
                    'en' => fake()->words(asText: true),
                ]),
                description: new Localised(translations: [
                    'uk' => fake('uk')->paragraph,
                    'en' => fake()->paragraph,
                ]),
                slider: collect([
                    new Image(path: $this->getImage()),
                    new Image(path: $this->getImage()),
                    new Image(path: $this->getImage()),
                ]),
            ),
        ]);
    }

    protected function getImage(): string
    {
        $imagePath = fake()->image(storage_path('app/public/samples'));

        return Str::after($imagePath, 'public/');
    }
}
