<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductChildCategorySeeder extends Seeder
{
    public function run(): void
    {
        $childCategories = [
            // Mobile Phones child categories
            [
                'name' => 'Android Phones',
                'slug' => Str::slug('Android Phones'),
                'is_active' => true,
                'description' => 'Smartphones running the Android operating system.',
                'product_sub_category_id' => 1, // Mobile Phones
            ],
            [
                'name' => 'iPhones',
                'slug' => Str::slug('iPhones'),
                'is_active' => true,
                'description' => 'Apple iPhones and accessories.',
                'product_sub_category_id' => 1, // Mobile Phones
            ],

            // Laptops & Computers child categories
            [
                'name' => 'Gaming Laptops',
                'slug' => Str::slug('Gaming Laptops'),
                'is_active' => true,
                'description' => 'High-performance laptops designed for gaming.',
                'product_sub_category_id' => 2, // Laptops & Computers
            ],
            [
                'name' => 'Desktops',
                'slug' => Str::slug('Desktops'),
                'is_active' => true,
                'description' => 'All types of desktop computers and builds.',
                'product_sub_category_id' => 2, // Laptops & Computers
            ],

            // Men's Clothing child categories
            [
                'name' => 'Shirts',
                'slug' => Str::slug('Shirts'),
                'is_active' => true,
                'description' => 'Formal and casual shirts for men.',
                'product_sub_category_id' => 3, // Men's Clothing
            ],
            [
                'name' => 'Trousers',
                'slug' => Str::slug('Trousers'),
                'is_active' => true,
                'description' => 'Jeans, chinos, and formal trousers.',
                'product_sub_category_id' => 3, // Men's Clothing
            ],

            // Skin Care child categories
            [
                'name' => 'Face Creams',
                'slug' => Str::slug('Face Creams'),
                'is_active' => true,
                'description' => 'Moisturizers, anti-aging, and face care creams.',
                'product_sub_category_id' => 7, // Skin Care
            ],
            [
                'name' => 'Face Wash',
                'slug' => Str::slug('Face Wash'),
                'is_active' => true,
                'description' => 'Facial cleansers and washes.',
                'product_sub_category_id' => 7, // Skin Care
            ],

            // Outdoor Gear child categories
            [
                'name' => 'Camping Tents',
                'slug' => Str::slug('Camping Tents'),
                'is_active' => true,
                'description' => 'Tents and sleeping bags for outdoor adventures.',
                'product_sub_category_id' => 9, // Outdoor Gear
            ],
            [
                'name' => 'Hiking Shoes',
                'slug' => Str::slug('Hiking Shoes'),
                'is_active' => true,
                'description' => 'Shoes designed for trekking and outdoor trails.',
                'product_sub_category_id' => 9, // Outdoor Gear
            ],
        ];

        DB::table('product_child_categories')->insert($childCategories);
    }
}
