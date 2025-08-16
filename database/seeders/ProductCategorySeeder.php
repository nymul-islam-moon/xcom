<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = [
            ['name' => 'Electronics', 'description' => 'Devices, gadgets, and tech products.'],
            ['name' => 'Fashion', 'description' => 'Clothing, shoes, and accessories.'],
            ['name' => 'Home & Kitchen', 'description' => 'Furniture, cookware, and decor.'],
            ['name' => 'Books & Stationery', 'description' => 'Books, notebooks, and office tools.'],
            ['name' => 'Beauty & Personal Care', 'description' => 'Skincare, haircare, and grooming.'],
        ];

        foreach ($categories as $cat) {
            DB::table('product_categories')->insert([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
