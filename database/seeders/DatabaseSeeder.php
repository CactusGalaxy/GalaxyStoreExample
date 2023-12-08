<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
    }
}
