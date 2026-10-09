<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with only the primary Admin account.
     */
    public function run(): void
    {
        // Default Super Administrator / Cashier Account
        User::firstOrCreate(
            ['email' => 'admin@merkato.com'],
            [
                'name' => 'Store Manager / Cashier',
                'password' => Hash::make('password123'),
                'role' => 'ADMIN',
                'is_active' => true,
            ]
        );
    }
}
