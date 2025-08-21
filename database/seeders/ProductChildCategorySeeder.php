<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductChildCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /**
         * Map: Category => [ Subcategory => [ child names or ['name'=>..,'description'=>..] ] ]
         * NOTE: Parent Category & Subcategory must exist (seed those first).
         */
        $data = [
            'Electronics' => [
                'Smartphones' => [
                    ['name' => 'Android Phones'],
                    ['name' => 'iPhones'],
                    ['name' => 'Feature Phones'],
                ],
                'Laptops' => [
                    ['name' => 'Ultrabooks'],
                    ['name' => 'Gaming Laptops'],
                    ['name' => 'Business Laptops'],
                ],
                'Tablets & eReaders' => [
                    ['name' => 'Android Tablets'],
                    ['name' => 'iPad'],
                    ['name' => 'eReaders'],
                ],
                'Headphones & Earbuds' => [
                    ['name' => 'In-Ear'],
                    ['name' => 'Over-Ear'],
                    ['name' => 'True Wireless (TWS)'],
                ],
                'Cameras & Photography' => [
                    ['name' => 'DSLR'],
                    ['name' => 'Mirrorless'],
                    ['name' => 'Lenses'],
                    ['name' => 'Tripods'],
                ],
                'Drones & Action Cams' => [
                    ['name' => 'Drones'],
                    ['name' => 'Action Cameras'],
                    ['name' => 'Gimbals'],
                ],
                'Wearables' => [
                    ['name' => 'Smartwatches'],
                    ['name' => 'Fitness Bands'],
                ],
                'Gaming Consoles' => [
                    ['name' => 'PlayStation'],
                    ['name' => 'Xbox'],
                    ['name' => 'Nintendo'],
                ],
                'Computer Components' => [
                    ['name' => 'Graphics Cards'],
                    ['name' => 'Processors'],
                    ['name' => 'Motherboards'],
                    ['name' => 'RAM'],
                    ['name' => 'Storage'],
                    ['name' => 'Power Supplies'],
                ],
                'Networking' => [
                    ['name' => 'Routers'],
                    ['name' => 'Mesh Systems'],
                    ['name' => 'Switches'],
                ],
            ],

            'Fashion' => [
                "Men's Clothing" => [
                    ['name' => 'T-Shirts'],
                    ['name' => 'Shirts'],
                    ['name' => 'Jeans'],
                    ['name' => 'Trousers'],
                ],
                "Women's Clothing" => [
                    ['name' => 'Dresses'],
                    ['name' => 'Tops'],
                    ['name' => 'Sarees'],
                    ['name' => 'Kurtis'],
                ],
                "Kids' Clothing" => [
                    ['name' => 'Boys'],
                    ['name' => 'Girls'],
                    ['name' => 'Infants'],
                ],
                'Shoes' => [
                    ['name' => 'Men'],
                    ['name' => 'Women'],
                    ['name' => 'Kids'],
                ],
                'Bags & Wallets' => [
                    ['name' => 'Backpacks'],
                    ['name' => 'Handbags'],
                    ['name' => 'Wallets'],
                ],
                'Jewelry' => [
                    ['name' => 'Necklaces'],
                    ['name' => 'Earrings'],
                    ['name' => 'Bracelets'],
                    ['name' => 'Rings'],
                ],
                'Watches' => [
                    ['name' => 'Analog'],
                    ['name' => 'Digital'],
                    ['name' => 'Smart'],
                ],
                'Sunglasses & Eyewear' => [
                    ['name' => 'Sunglasses'],
                    ['name' => 'Blue Light Glasses'],
                ],
                'Winter Wear' => [
                    ['name' => 'Jackets'],
                    ['name' => 'Sweaters'],
                    ['name' => 'Hoodies'],
                ],
            ],

            'Home & Kitchen' => [
                'Furniture' => [
                    ['name' => 'Sofas'],
                    ['name' => 'Beds'],
                    ['name' => 'Tables'],
                    ['name' => 'Chairs'],
                ],
                'Home Decor' => [
                    ['name' => 'Wall Art'],
                    ['name' => 'Vases'],
                    ['name' => 'Clocks'],
                ],
                'Kitchen & Dining' => [
                    ['name' => 'Cookware'],
                    ['name' => 'Dinnerware'],
                    ['name' => 'Storage Containers'],
                ],
                'Storage & Organization' => [
                    ['name' => 'Boxes'],
                    ['name' => 'Shelves'],
                    ['name' => 'Hangers'],
                ],
                'Bedding & Bath' => [
                    ['name' => 'Bedsheets'],
                    ['name' => 'Towels'],
                    ['name' => 'Comforters'],
                ],
                'Cleaning Supplies' => [
                    ['name' => 'Mops'],
                    ['name' => 'Brooms'],
                    ['name' => 'Vacuum Cleaners'],
                ],
                'Lighting' => [
                    ['name' => 'Ceiling Lights'],
                    ['name' => 'Table & Floor Lamps'],
                    ['name' => 'LED Strips'],
                ],
            ],

            'Books & Stationery' => [
                'Fiction' => [
                    ['name' => 'Literary'],
                    ['name' => 'Sci-Fi & Fantasy'],
                    ['name' => 'Mystery & Thriller'],
                ],
                'Non-Fiction' => [
                    ['name' => 'Biographies'],
                    ['name' => 'Self-Help'],
                    ['name' => 'Business'],
                ],
                'Children & Teens' => [
                    ['name' => 'Picture Books'],
                    ['name' => 'Young Adult'],
                ],
                'Exam Prep & Academic' => [
                    ['name' => 'Engineering'],
                    ['name' => 'Medical'],
                    ['name' => 'Civil Service'],
                ],
                'Notebooks & Journals' => [
                    ['name' => 'Spiral'],
                    ['name' => 'Hardbound'],
                    ['name' => 'Diaries'],
                ],
                'Pens & Writing' => [
                    ['name' => 'Ball Pens'],
                    ['name' => 'Gel Pens'],
                    ['name' => 'Markers'],
                ],
                'Office Supplies' => [
                    ['name' => 'Files & Folders'],
                    ['name' => 'Paper'],
                    ['name' => 'Staplers'],
                ],
                'Art Supplies' => [
                    ['name' => 'Paints'],
                    ['name' => 'Brushes'],
                    ['name' => 'Canvas'],
                ],
            ],

            'Beauty & Personal Care' => [
                'Skincare' => [
                    ['name' => 'Cleansers'],
                    ['name' => 'Moisturizers'],
                    ['name' => 'Sunscreens'],
                ],
                'Haircare' => [
                    ['name' => 'Shampoo'],
                    ['name' => 'Conditioner'],
                    ['name' => 'Styling'],
                ],
                'Makeup' => [
                    ['name' => 'Face'],
                    ['name' => 'Eyes'],
                    ['name' => 'Lips'],
                ],
                'Fragrances' => [
                    ['name' => 'Perfumes'],
                    ['name' => 'Deodorants'],
                ],
                'Bath & Body' => [
                    ['name' => 'Body Wash'],
                    ['name' => 'Soap'],
                    ['name' => 'Body Lotion'],
                ],
                "Men's Grooming" => [
                    ['name' => 'Beard Care'],
                    ['name' => 'Shaving'],
                ],
                'Tools & Accessories' => [
                    ['name' => 'Hair Dryers'],
                    ['name' => 'Trimmers'],
                    ['name' => 'Brushes & Combs'],
                ],
            ],

            'Sports & Outdoors' => [
                'Fitness Equipment' => [
                    ['name' => 'Dumbbells'],
                    ['name' => 'Yoga Mats'],
                    ['name' => 'Treadmills'],
                ],
                'Sportswear' => [
                    ['name' => 'Jerseys'],
                    ['name' => 'Shorts'],
                    ['name' => 'Tracksuits'],
                ],
                'Camping & Hiking' => [
                    ['name' => 'Tents'],
                    ['name' => 'Backpacks'],
                    ['name' => 'Sleeping Bags'],
                ],
                'Cycling' => [
                    ['name' => 'Bicycles'],
                    ['name' => 'Helmets'],
                    ['name' => 'Accessories'],
                ],
                'Team Sports' => [
                    ['name' => 'Cricket'],
                    ['name' => 'Football'],
                    ['name' => 'Basketball'],
                ],
            ],
        ];

        $rows = [];

        DB::transaction(function () use ($data, $now, &$rows) {
            foreach ($data as $categoryName => $subMap) {
                $categoryId = DB::table('product_categories')
                    ->where('slug', Str::slug($categoryName))
                    ->value('id');

                if (!$categoryId) continue;

                foreach ($subMap as $subName => $children) {
                    // subcategory rows (must exist)
                    $subId = DB::table('product_sub_categories')
                        ->where('product_category_id', $categoryId)
                        ->where('name', $subName)
                        ->value('id');

                    if (!$subId) continue;

                    foreach ($children as $it) {
                        $childName = is_array($it) ? $it['name'] : (string) $it;
                        $childDesc = is_array($it) ? ($it['description'] ?? null) : null;

                        // Globally-unique slug using category + sub + child
                        $slug = Str::slug(Str::slug($categoryName) . ' ' . Str::slug($subName) . ' ' . $childName);

                        $rows[] = [
                            'name'                    => $childName,
                            'slug'                    => $slug,
                            'description'             => $childDesc,
                            'product_sub_category_id' => $subId,
                            'created_at'              => $now,
                            'updated_at'              => $now,
                        ];
                    }
                }
            }

            if ($rows) {
                DB::table('product_child_categories')->upsert(
                    $rows,
                    ['slug'], // unique key
                    ['name','description','product_sub_category_id','updated_at']
                );
            }
        });
    }
}
