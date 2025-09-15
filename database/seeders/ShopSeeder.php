<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shops')->insert([
            [
                'name' => 'TechWorld',
                'email' => 'techworld@example.com',
                'phone' => '01712345678',
                'shop_keeper_name' => 'Nymul Islam',
                'shop_keeper_phone' => '01787654321',
                'shop_keeper_nid' => '1234567890123',
                'shop_keeper_photo' => null,
                'shop_keeper_email' => 'nymul@example.com',
                'shop_keeper_tin' => 'TIN123456',
                'dbid' => 'DBID987654',
                'bank_name' => 'DBBL',
                'bank_account_number' => '012345678901',
                'bank_branch' => 'Dhaka Main',
                'shop_logo' => null,
                'description' => 'TechWorld sells electronics and gadgets.',
                'business_address' => '123, Gulshan Avenue, Dhaka, Bangladesh',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FashionHub',
                'email' => 'fashionhub@example.com',
                'phone' => '01711223344',
                'shop_keeper_name' => 'Sara Ahmed',
                'shop_keeper_phone' => '01744332211',
                'shop_keeper_nid' => '9876543210987',
                'shop_keeper_photo' => null,
                'shop_keeper_email' => 'sara@example.com',
                'shop_keeper_tin' => 'TIN654321',
                'dbid' => 'DBID123456',
                'bank_name' => 'BRAC Bank',
                'bank_account_number' => '098765432109',
                'bank_branch' => 'Banani Branch',
                'shop_logo' => null,
                'description' => 'FashionHub offers latest clothing and accessories.',
                'business_address' => '45, Banani Road, Dhaka, Bangladesh',
                'email_verified_at' => now(),
                'password' => Hash::make('fashion123'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
