<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_page_infos', function (Blueprint $table) {
            $table->id();

            $table->json('hero_section')->nullable();
            $table->json('promo_section')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_page_infos');
    }
};
