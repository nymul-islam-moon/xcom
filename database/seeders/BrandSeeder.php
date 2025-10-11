<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => Str::slug('Apple'),
                'is_active' => true,
                'image' => 'brands/apple.png',
                'description' => 'Apple Inc. designs and sells electronics, software, and online services.',
            ],
            [
                'name' => 'Samsung',
                'slug' => Str::slug('Samsung'),
                'is_active' => true,
                'image' => 'brands/samsung.png',
                'description' => 'Samsung Electronics is a global leader in consumer electronics and technology.',
            ],
            [
                'name' => 'Nike',
                'slug' => Str::slug('Nike'),
                'is_active' => true,
                'image' => 'brands/nike.png',
                'description' => 'Nike is a multinational corporation known for its footwear, apparel, and sports equipment.',
            ],
            [
                'name' => 'Sony',
                'slug' => Str::slug('Sony'),
                'is_active' => true,
                'image' => 'brands/sony.png',
                'description' => 'Sony Corporation produces electronics, gaming consoles, and entertainment content.',
            ],
            [
                'name' => 'Adidas',
                'slug' => Str::slug('Adidas'),
                'is_active' => true,
                'image' => 'brands/adidas.png',
                'description' => 'Adidas designs and manufactures shoes, clothing, and accessories.',
            ],
        ];

        // Add timestamps
        $timestamp = now();
        foreach ($brands as &$brand) {
            $brand['created_at'] = $timestamp;
            $brand['updated_at'] = $timestamp;
        }

        DB::table('brands')->insert($brands);
    }
}
