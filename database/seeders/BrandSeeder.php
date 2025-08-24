<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Samsung',
                'image' => 'brands/samsung.png',
                'description' => 'Global brand known for electronics and appliances.',
            ],
            [
                'name' => 'Apple',
                'image' => 'brands/apple.png',
                'description' => 'Premium electronics and smartphone brand.',
            ],
            [
                'name' => 'Xiaomi',
                'image' => 'brands/xiaomi.png',
                'description' => 'Affordable and innovative electronic products.',
            ],
            [
                'name' => 'Nike',
                'image' => 'brands/nike.png',
                'description' => 'Leading brand in sportswear and sneakers.',
            ],
            [
                'name' => 'Adidas',
                'image' => 'brands/adidas.png',
                'description' => 'Sporting goods and apparel company.',
            ],
            [
                'name' => 'Sony',
                'image' => 'brands/sony.png',
                'description' => 'Well-known brand for audio, video, and gaming.',
            ],
            [
                'name' => 'HP',
                'image' => 'brands/hp.png',
                'description' => 'Reliable computing and printing solutions.',
            ],
            [
                'name' => 'Dell',
                'image' => 'brands/dell.png',
                'description' => 'Business and consumer laptop/desktop brand.',
            ],
            [
                'name' => 'Unilever',
                'image' => 'brands/unilever.png',
                'description' => 'FMCG giant in personal care and household items.',
            ],
            [
                'name' => 'Nestlé',
                'image' => 'brands/nestle.png',
                'description' => 'Food and beverage multinational company.',
            ],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
                'image' => $brand['image'],
                'description' => $brand['description'],
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
