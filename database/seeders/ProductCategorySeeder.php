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
        $now = now();

        $categories = [
            // existing
            ['name' => 'Electronics',           'description' => 'Devices, gadgets, and tech products.'],
            ['name' => 'Fashion',               'description' => 'Clothing, shoes, and accessories.'],
            ['name' => 'Home & Kitchen',        'description' => 'Furniture, cookware, and decor.'],
            ['name' => 'Books & Stationery',    'description' => 'Books, notebooks, and office tools.'],
            ['name' => 'Beauty & Personal Care', 'description' => 'Skincare, haircare, and grooming.'],

            // more
            ['name' => 'Computers & Accessories',   'description' => 'Laptops, desktops, components, and peripherals.'],
            ['name' => 'Mobile Phones & Accessories', 'description' => 'Smartphones, cases, chargers, and cables.'],
            ['name' => 'Cameras & Photography',     'description' => 'Cameras, lenses, tripods, and lighting.'],
            ['name' => 'TV, Audio & Home Theater',  'description' => 'Televisions, speakers, soundbars, and receivers.'],
            ['name' => 'Appliances',                'description' => 'Large and small home appliances.'],
            ['name' => 'Smart Home',                'description' => 'Home automation devices and sensors.'],
            ['name' => 'Tools & Home Improvement',  'description' => 'Power tools, hardware, and fixtures.'],
            ['name' => 'Garden & Outdoor',          'description' => 'Plants, patio, grills, and outdoor gear.'],
            ['name' => 'Sports & Outdoors',         'description' => 'Fitness equipment, apparel, and gear.'],
            ['name' => 'Toys & Games',              'description' => 'Kids’ toys, puzzles, and board games.'],
            ['name' => 'Baby & Kids',               'description' => 'Baby care, strollers, and kids’ essentials.'],
            ['name' => 'Pet Supplies',              'description' => 'Food, toys, and accessories for pets.'],
            ['name' => 'Groceries & Gourmet Food',  'description' => 'Everyday groceries and specialty foods.'],
            ['name' => 'Health & Wellness',         'description' => 'Vitamins, wellness, and personal health.'],
            ['name' => 'Automotive',                'description' => 'Car care, parts, and accessories.'],
            ['name' => 'Motorbike Accessories',     'description' => 'Helmets, riding gear, and parts.'],
            ['name' => 'Office Supplies',           'description' => 'Paper, printers, and office essentials.'],
            ['name' => 'Art, Craft & Sewing',       'description' => 'DIY supplies, paints, and fabrics.'],
            ['name' => 'Music & Instruments',       'description' => 'Guitars, keyboards, and audio gear.'],
            ['name' => 'Jewelry & Watches',         'description' => 'Rings, necklaces, bracelets, and watches.'],
            ['name' => 'Travel & Luggage',          'description' => 'Suitcases, backpacks, and travel accessories.'],
            ['name' => 'Video Games & Consoles',    'description' => 'Consoles, games, and accessories.'],
            ['name' => 'Software',                  'description' => 'Operating systems and productivity tools.'],
            ['name' => 'Lighting',                  'description' => 'Indoor, outdoor, and decorative lighting.'],
            ['name' => 'Safety & Security',         'description' => 'Locks, CCTV, alarms, and safes.'],
            ['name' => 'Industrial & Scientific',   'description' => 'Lab supplies and industrial tools.'],
            ['name' => 'Party & Festive Decor',     'description' => 'Seasonal items, balloons, and decor.'],
            ['name' => 'Gift Cards',                'description' => 'Digital and physical gift cards.'],
        ];

        $rows = collect($categories)->map(fn($cat) => [
            'name'        => $cat['name'],
            'slug'        => Str::slug($cat['name']),
            'description' => $cat['description'],
            'created_at'  => $now,
            'updated_at'  => $now,
        ])->all();

        // Upsert by unique slug; update name/description/updated_at if exists
        DB::table('product_categories')->upsert(
            $rows,
            ['slug'],
            ['name', 'description', 'updated_at']
        );
    }
}
