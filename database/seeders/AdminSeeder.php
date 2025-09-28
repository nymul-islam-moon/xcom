<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Nymul Islam',
                'email' => 'nymulislam.dev@gmail.com',
                'phone' => '01339315497',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => \Str::random(10),
            ]
        );
    }
}
