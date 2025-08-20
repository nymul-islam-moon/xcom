<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ProductSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        /**
         * Map: Category Name => list of subcategories (string or ['name'=>..., 'description'=>...])
         * Make sure the parent categories already exist in product_categories (seed those first).
         */
        $data = [
            'Electronics' => [
                ['name' => 'Smartphones',        'description' => 'Android, iOS, and feature phones.'],
                ['name' => 'Laptops',            'description' => 'Ultrabooks, gaming, business laptops.'],
                ['name' => 'Tablets & eReaders', 'description' => 'Android tablets, iPad, Kindles.'],
                ['name' => 'Headphones & Earbuds'],
                ['name' => 'Cameras & Photography'],
                ['name' => 'Drones & Action Cams'],
                ['name' => 'Wearables',          'description' => 'Smartwatches and fitness bands.'],
                ['name' => 'Gaming Consoles'],
                ['name' => 'Computer Components', 'description' => 'CPU, GPU, RAM, SSD, PSU.'],
                ['name' => 'Networking',         'description' => 'Routers, mesh Wi-Fi, switches.'],
            ],
            'Fashion' => [
                ['name' => "Men's Clothing"],
                ['name' => "Women's Clothing"],
                ['name' => "Kids' Clothing"],
                ['name' => 'Shoes'],
                ['name' => 'Bags & Wallets'],
                ['name' => 'Jewelry'],
                ['name' => 'Watches'],
                ['name' => 'Sunglasses & Eyewear'],
                ['name' => 'Winter Wear'],
            ],
            'Home & Kitchen' => [
                ['name' => 'Furniture'],
                ['name' => 'Home Decor'],
                ['name' => 'Kitchen & Dining'],
                ['name' => 'Storage & Organization'],
                ['name' => 'Bedding & Bath'],
                ['name' => 'Cleaning Supplies'],
                ['name' => 'Lighting'],
            ],
            'Books & Stationery' => [
                ['name' => 'Fiction'],
                ['name' => 'Non-Fiction'],
                ['name' => 'Children & Teens'],
                ['name' => 'Exam Prep & Academic'],
                ['name' => 'Notebooks & Journals'],
                ['name' => 'Pens & Writing'],
                ['name' => 'Office Supplies'],
                ['name' => 'Art Supplies'],
            ],
            'Beauty & Personal Care' => [
                ['name' => 'Skincare'],
                ['name' => 'Haircare'],
                ['name' => 'Makeup'],
                ['name' => 'Fragrances'],
                ['name' => 'Bath & Body'],
                ['name' => "Men's Grooming"],
                ['name' => 'Tools & Accessories', 'description' => 'Brushes, trimmers, mirrors.'],
            ],

            // If you seeded more parent categories earlier, you can add their subs here too:
            'Sports & Outdoors' => [
                ['name' => 'Fitness Equipment'],
                ['name' => 'Sportswear'],
                ['name' => 'Camping & Hiking'],
                ['name' => 'Cycling'],
                ['name' => 'Team Sports'],
            ],
            'Toys & Games' => [
                ['name' => 'Learning & Education'],
                ['name' => 'Action Figures & Dolls'],
                ['name' => 'Puzzles & Board Games'],
                ['name' => 'Vehicles & RC'],
            ],
            'Automotive' => [
                ['name' => 'Car Electronics'],
                ['name' => 'Oils & Fluids'],
                ['name' => 'Exterior Accessories'],
                ['name' => 'Interior Accessories'],
                ['name' => 'Tools & Equipment'],
            ],
            'Pet Supplies' => [
                ['name' => 'Dog Supplies'],
                ['name' => 'Cat Supplies'],
                ['name' => 'Aquatic Pets'],
                ['name' => 'Bird Supplies'],
                ['name' => 'Small Animals'],
            ],
            'Groceries & Gourmet Food' => [
                ['name' => 'Beverages'],
                ['name' => 'Snacks'],
                ['name' => 'Cooking Essentials', 'description' => 'Oil, rice, flour, spices.'],
                ['name' => 'Breakfast Foods'],
                ['name' => 'Sweets & Chocolates'],
            ],
        ];

        $rows = [];

        DB::transaction(function () use ($data, $now, &$rows) {
            foreach ($data as $categoryName => $subcats) {
                $categorySlug = Str::slug($categoryName);

                // Get parent category id (assumes categories are already seeded)
                $categoryId = DB::table('product_categories')
                    ->where('slug', $categorySlug)
                    ->value('id');

                if (!$categoryId) {
                    // If a parent is missing, skip its subs (or create the parent here if you prefer)
                    continue;
                }

                foreach ($subcats as $item) {
                    if (is_array($item)) {
                        $subName = $item['name'];
                        $subDesc = $item['description'] ?? null;
                    } else {
                        $subName = (string) $item;
                        $subDesc = null;
                    }

                    // Make slug globally-unique by prefixing the parent category slug
                    $subSlug = Str::slug($categorySlug . ' ' . $subName);

                    $rows[] = [
                        'name'                 => $subName,
                        'slug'                 => $subSlug,
                        'description'          => $subDesc,
                        'product_category_id'  => $categoryId,
                        'created_at'           => $now,
                        'updated_at'           => $now,
                    ];
                }
            }

            if (!empty($rows)) {
                DB::table('product_sub_categories')->upsert(
                    $rows,
                    ['slug'], // unique key
                    ['name', 'description', 'product_category_id', 'updated_at'] // fields to update
                );
            }
        });
    }
}
