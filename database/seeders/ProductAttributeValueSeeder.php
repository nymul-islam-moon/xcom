<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductAttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the attributes from DB (must be seeded first)
        $attributes = ProductAttribute::whereIn('name', [
            'Color', 'Size', 'Material', 'Weight', 'Capacity',
        ])->get()->keyBy('name');

        // Define attribute values
        $values = [
            'Color' => ['Red', 'Blue', 'Black', 'White', 'Green'],
            'Size' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'Material' => ['Cotton', 'Leather', 'Polyester', 'Wool', 'Silk'],
            'Weight' => ['100g', '250g', '500g', '1kg', '2kg'],
            'Capacity' => ['250ml', '500ml', '1L', '64GB', '128GB'],
        ];

        $insertData = [];

        foreach ($values as $attributeName => $attributeValues) {
            $attribute = $attributes[$attributeName] ?? null;

            if (! $attribute) {
                continue; // Skip if not found
            }

            foreach ($attributeValues as $value) {
                $insertData[] = [
                    'product_attribute_id' => $attribute->id,
                    'value' => $value,
                    'slug' => Str::slug($value),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('product_attribute_values')->insert($insertData);
    }
}
