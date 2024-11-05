<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('category_ref_id')->index();
            $table->integer('brand_ref_id')->index();
            $table->decimal('price')->nullable();
            $table->decimal('discount_price')->nullable();
            $table->text('benefits_content')->nullable();
            $table->text('ingredients_content')->nullable();
            $table->text('howtouse_content')->nullable();
            $table->string('product_size_id')->nullable();
            $table->tinyInteger('is_active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
