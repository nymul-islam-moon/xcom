<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'          => 'Electronics',
                'is_active'     => true,
                'slug'          => 'electronics',
                'description'   => 'Devices and gadgets that use electrical power.',
            ],
            [
                'name'          => 'Fashion',
                'is_active'     => true,
                'slug'          => 'fashion',
                'description'   => 'Clothing, accessories, and trends.',
            ],
            [
                'name'          => 'Home & Furniture',
                'is_active'     => true,
                'slug'          => 'home-furniture',
                'description'   => 'Furniture and home decor products.',
            ],
            [
                'name'          => 'Beauty & Health',
                'is_active'     => true,
                'slug'          => 'beauty-health',
                'description'   => 'Beauty products, health supplements, and personal care items.',
            ],
            [
                'name'          => 'Sports & Outdoors',
                'is_active'     => true,
                'slug'          => 'sports-outdoors',
                'description'   => 'Sports equipment, outdoor gear, and fitness products.',
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
