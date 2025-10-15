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

            // Basic Product Info
            $table->string('name');
            $table->string('sku', 100)->nullable()->unique();
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();

            // Product type
            $table->enum('product_type', ['physical', 'digital', 'subscription', 'service', 'gift_card'])->default('physical');
            $table->enum('variant_type', ['simple', 'variable'])->default('simple'); // simple = one variant, variable = multiple

            // Pricing
            $table->boolean('tax_included')->default(true);
            $table->decimal('tax_percentage', 5, 2)->nullable();

            // Digital Products
            $table->string('download_url')->nullable();
            $table->string('license_key')->nullable();

            // Subscription Products
            $table->string('subscription_interval', 50)->nullable();

            // Product Identifiers
            $table->string('mpn')->nullable();
            $table->string('gtin8')->nullable();
            $table->string('gtin13')->nullable();
            $table->string('gtin14')->nullable();

            // Backorder Option
            $table->enum('allow_backorders', ['no', 'notify', 'yes'])->default('no');

            // Return & Policies
            $table->text('return_policy')->nullable();
            $table->integer('return_days')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Lifecycle & publish
            $table->enum('status', ['active', 'inactive', 'discontinued', 'out_of_stock'])->default('active');
            $table->timestamp('publish_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);

            // Relationships
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('product_sub_categories')->onDelete('cascade');
            $table->foreignId('child_category_id')->nullable()->constrained('product_child_categories')->onDelete('cascade');
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('cascade');

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
