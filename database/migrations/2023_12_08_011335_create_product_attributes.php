<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();

            $table->integer('position');
            $table->boolean('status')->default(true);

            $table->string('key')->nullable();
            $table->string('display_type_in_card');

            $table->timestamps();
        });

        Schema::create('product_attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('name');

            $table->unique(['locale', 'product_attribute_id'], 'prod_attr_transl_local_prod_attr_id_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_translations');
        Schema::dropIfExists('product_attributes');
    }
};
