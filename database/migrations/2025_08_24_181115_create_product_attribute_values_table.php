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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_attribute_id')
                ->constrained('product_attributes')
                ->onDelete('cascade');

            $table->string('value'); // e.g., Red, Blue, Small
            $table->string('slug');  // e.g., red, blue, small

            // Ensure (attribute_id + slug) is unique
            $table->unique(['product_attribute_id', 'slug'], 'product_attr_values_slug_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
