<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();

            $table->integer('position')->default(1);
            $table->boolean('status')->default(true);

            $table->string('image')->nullable();

            $table->timestamps();
        });

        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->unique(['locale', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_translation');
        Schema::dropIfExists('categories');
    }
};
