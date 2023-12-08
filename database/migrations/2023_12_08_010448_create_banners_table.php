<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->integer('position')->default(1);
            $table->boolean('status')->default(1);

            $table->string('image')->nullable();

            $table->timestamps();
        });

        Schema::create('banner_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->foreignId('banner_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('title');
            $table->text('description')->nullable();

            $table->unique(['locale', 'banner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_translations');
        Schema::dropIfExists('banners');
    }
};
