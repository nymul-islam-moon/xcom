<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Array of sample product categories
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => Str::slug('Electronics'),
                'is_active' => true,
                'description' => 'Devices and gadgets that use electrical power.'
            ],
            [
                'name' => 'Fashion',
                'slug' => Str::slug('Fashion'),
                'is_active' => true,
                'description' => 'Clothing, accessories, and trends.'
            ],
            [
                'name' => 'Home & Furniture',
                'slug' => Str::slug('Home & Furniture'),
                'is_active' => true,
                'description' => 'Furniture and home decor products.'
            ],
            [
                'name' => 'Beauty & Health',
                'slug' => Str::slug('Beauty & Health'),
                'is_active' => true,
                'description' => 'Beauty products, health supplements, and personal care items.'
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => Str::slug('Sports & Outdoors'),
                'is_active' => true,
                'description' => 'Sports equipment, outdoor gear, and fitness products.'
            ],
        ];

        // Insert the categories into the database
        DB::table('product_categories')->insert($categories);
    }
}
