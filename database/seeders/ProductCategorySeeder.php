<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'is_active' => true,
                'description' => 'Devices and gadgets that use electrical power.'
            ],
            [
                'name' => 'Fashion',
                'is_active' => true,
                'description' => 'Clothing, accessories, and trends.'
            ],
            [
                'name' => 'Home & Furniture',
                'is_active' => true,
                'description' => 'Furniture and home decor products.'
            ],
            [
                'name' => 'Beauty & Health',
                'is_active' => true,
                'description' => 'Beauty products, health supplements, and personal care items.'
            ],
            [
                'name' => 'Sports & Outdoors',
                'is_active' => true,
                'description' => 'Sports equipment, outdoor gear, and fitness products.'
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category); 
            // HasSlug trait will automatically generate a slug in `slugs` table
        }
    }
}
