<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Color',
                'slug' => Str::slug('Color'),
                'description' => 'Available product colors (e.g., Red, Blue, Black, etc.)',
            ],
            [
                'name' => 'Size',
                'slug' => Str::slug('Size'),
                'description' => 'Available product sizes (e.g., S, M, L, XL)',
            ],
            [
                'name' => 'Material',
                'slug' => Str::slug('Material'),
                'description' => 'Type of material used in the product (e.g., Cotton, Leather)',
            ],
            [
                'name' => 'Weight',
                'slug' => Str::slug('Weight'),
                'description' => 'Product weight specifications',
            ],
            [
                'name' => 'Capacity',
                'slug' => Str::slug('Capacity'),
                'description' => 'Storage or volume capacity (e.g., 500ml, 128GB)',
            ],
        ];

        DB::table('product_attributes')->insert($attributes);
    }
}
