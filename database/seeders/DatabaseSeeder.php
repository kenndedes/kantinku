<?php

namespace Database\Seeders;

use Database\Seeders\MenuItemSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StandSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class,
        ]);
    }
}
