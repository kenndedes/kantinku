<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Kantinku',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'photo' => null,
                'balance' => 0,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User Kantinku',
                'password' => Hash::make('password'),
                'role' => 'user',
                'photo' => null,
                'balance' => 100000,
                'email_verified_at' => now(),
            ]
        );
    }
}
