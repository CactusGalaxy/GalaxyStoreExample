<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('position')->nullable();
            $table->boolean('status')->default(true);

            $table->timestamps();
        });

        Schema::create('attribute_value_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('name');
            $table->string('value')->nullable();

            $table->unique(['locale', 'attribute_value_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_value_translations');
        Schema::dropIfExists('attribute_values');
    }
};
