<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\ShopPayment;
use App\Models\Account;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shopsData = [
            [
                'name' => 'Tech World',
                'email' => 'techworld@example.com',
                'phone' => '01710000001',
                'slug' => Str::slug('Tech World'),
                'shop_keeper_name' => 'Asif Rahman',
                'shop_keeper_phone' => '01710000001',
                'shop_keeper_nid' => '1987654321',
                'shop_keeper_email' => 'asif@example.com',
                'shop_keeper_photo' => 'shopkeepers/asif.jpg',
                'shop_keeper_tin' => 'TIN123456',
                'dbid' => 'DBID-001',
                'bank_name' => 'Brac Bank',
                'bank_account_number' => '1234567890',
                'bank_branch' => 'Gulshan Branch',
                'is_active' => true,
                'is_suspended' => false,
                'shop_logo' => 'shops/techworld.png',
                'description' => 'Leading electronics and gadget store.',
                'business_address' => 'House #12, Road #7, Gulshan, Dhaka',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Fashion Hub',
                'email' => 'fashionhub@example.com',
                'phone' => '01710000002',
                'slug' => Str::slug('Fashion Hub'),
                'shop_keeper_name' => 'Farhana Akter',
                'shop_keeper_phone' => '01710000002',
                'shop_keeper_nid' => '1987654322',
                'shop_keeper_email' => 'farhana@example.com',
                'shop_keeper_photo' => 'shopkeepers/farhana.jpg',
                'shop_keeper_tin' => 'TIN654321',
                'dbid' => 'DBID-002',
                'bank_name' => 'Dutch-Bangla Bank',
                'bank_account_number' => '9876543210',
                'bank_branch' => 'Banani Branch',
                'is_active' => true,
                'is_suspended' => false,
                'shop_logo' => 'shops/fashionhub.png',
                'description' => 'Trendy fashion apparel for men and women.',
                'business_address' => 'Road #5, Dhanmondi, Dhaka',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($shopsData as $shopData) {
            // 1. Create Shop
            $shop = Shop::create($shopData);

            // 2. Create ShopPayment for this shop
            $paymentDate = Carbon::now()->subDays(rand(0, 10));
            $durationDays = 30;
            $startDate = $paymentDate;
            $endDate = $startDate->copy()->addDays($durationDays);

            $shopPayment = ShopPayment::create([
                'shop_id' => $shop->id,
                'payment_method' => 'card',
                'payment_date' => $paymentDate->toDateString(),
                'start_date' => $startDate->toDateString(),
                'duration_days' => $durationDays,
                'end_date' => $endDate->toDateString(),
                // 'transaction_number' => '3782822'. rand(1,10) .'46310005',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Create Account entry for the payment
            Account::create([
                'owner_id' => $shop->id,
                'owner_type' => Shop::class,

                'reference_id' => $shopPayment->id,
                'reference_type' => ShopPayment::class,

                'category' => 'payment',
                'sub_type' => 'subscription',
                'direction' => 'credit',
                'amount' => 100.00,
                'currency' => 'USD',

                'provider' => 'stripe',
                'transaction_id' => strtoupper(Str::random(12)),
                'payment_method' => 'card',

                'status' => 'completed',
                'happened_at' => $paymentDate,
                'reconciled_at' => now(),

                'meta' => json_encode([
                    'note' => 'Seeded subscription payment'
                ]),
                'reference_code' => 'SUB-' . strtoupper(Str::random(6)),
                'uuid' => Str::uuid(),

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
