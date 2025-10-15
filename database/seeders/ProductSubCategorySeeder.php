<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSubCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Array of sample product subcategories
        $subCategories = [
            // Electronics Subcategories
            [
                'name' => 'Mobile Phones',
                'slug' => Str::slug('Mobile Phones'),
                'is_active' => true,
                'description' => 'Smartphones and mobile devices.',
                'product_category_id' => 1, // Electronics
            ],
            [
                'name' => 'Laptops & Computers',
                'slug' => Str::slug('Laptops & Computers'),
                'is_active' => true,
                'description' => 'Laptops, desktops, and computer accessories.',
                'product_category_id' => 1, // Electronics
            ],

            // Fashion Subcategories
            [
                'name' => 'Men\'s Clothing',
                'slug' => Str::slug('Men\'s Clothing'),
                'is_active' => true,
                'description' => 'Clothing for men including shirts, trousers, etc.',
                'product_category_id' => 2, // Fashion
            ],
            [
                'name' => 'Women\'s Clothing',
                'slug' => Str::slug('Women\'s Clothing'),
                'is_active' => true,
                'description' => 'Clothing for women including dresses, tops, etc.',
                'product_category_id' => 2, // Fashion
            ],

            // Home & Furniture Subcategories
            [
                'name' => 'Living Room Furniture',
                'slug' => Str::slug('Living Room Furniture'),
                'is_active' => true,
                'description' => 'Furniture for the living room such as sofas and tables.',
                'product_category_id' => 3, // Home & Furniture
            ],
            [
                'name' => 'Bedroom Furniture',
                'slug' => Str::slug('Bedroom Furniture'),
                'is_active' => true,
                'description' => 'Furniture for the bedroom including beds, wardrobes, etc.',
                'product_category_id' => 3, // Home & Furniture
            ],

            // Beauty & Health Subcategories
            [
                'name' => 'Skin Care',
                'slug' => Str::slug('Skin Care'),
                'is_active' => true,
                'description' => 'Beauty products focused on skincare.',
                'product_category_id' => 4, // Beauty & Health
            ],
            [
                'name' => 'Health Supplements',
                'slug' => Str::slug('Health Supplements'),
                'is_active' => true,
                'description' => 'Vitamins and supplements for health and wellness.',
                'product_category_id' => 4, // Beauty & Health
            ],

            // Sports & Outdoors Subcategories
            [
                'name' => 'Outdoor Gear',
                'slug' => Str::slug('Outdoor Gear'),
                'is_active' => true,
                'description' => 'Camping, hiking, and other outdoor equipment.',
                'product_category_id' => 5, // Sports & Outdoors
            ],
            [
                'name' => 'Fitness Equipment',
                'slug' => Str::slug('Fitness Equipment'),
                'is_active' => true,
                'description' => 'Equipment for exercise and fitness routines.',
                'product_category_id' => 5, // Sports & Outdoors
            ],
        ];

        // Insert the subcategories into the database
        DB::table('product_sub_categories')->insert($subCategories);
    }
}
