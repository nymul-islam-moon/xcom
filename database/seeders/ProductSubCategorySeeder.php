<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Map: Category Name => list of subcategories (string or ['name'=>..., 'description'=>...])
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

            // a few from your "more" list
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
                $categoryId = DB::table('product_categories')
                    ->where('slug', Str::slug($categoryName))
                    ->value('id');

                if (!$categoryId) {
                    // parent missing; skip its subs
                    continue;
                }

                foreach ($subcats as $item) {
                    $subName = is_array($item) ? $item['name'] : (string) $item;
                    $subDesc = is_array($item) ? ($item['description'] ?? null) : null;

                    // globally unique slug, prefixed by parent
                    $slug = Str::slug(Str::slug($categoryName) . ' ' . $subName);

                    $rows[] = [
                        'name'                => $subName,
                        'slug'                => $slug,
                        'description'         => $subDesc,
                        'product_category_id' => $categoryId,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }
            }

            if ($rows) {
                DB::table('product_sub_categories')->upsert(
                    $rows,
                    ['slug'], // unique
                    ['name','description','product_category_id','updated_at']
                );
            }
        });
    }
}
