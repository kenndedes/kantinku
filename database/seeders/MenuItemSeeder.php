<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Nasi Goreng Spesial',
                'type' => 'makanan',
                'price' => 15000,
                'photo' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
            ],
            [
                'name' => 'Mie Ayam',
                'type' => 'makanan',
                'price' => 12000,
                'photo' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
            ],
            [
                'name' => 'Soto Ayam',
                'type' => 'makanan',
                'price' => 13000,
                'photo' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
            ],
            [
                'name' => 'Es Teh Manis',
                'type' => 'minuman',
                'price' => 5000,
                'photo' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
            ],
            [
                'name' => 'Jus Jeruk',
                'type' => 'minuman',
                'price' => 7000,
                'photo' => 'https://images.unsplash.com/photo-1600271886742-f049cd5bba3f?auto=format&fit=crop&w=800&q=80',
                'is_available' => false,
            ],
            [
                'name' => 'Air Mineral',
                'type' => 'minuman',
                'price' => 4000,
                'photo' => 'https://images.unsplash.com/photo-1564419320461-6870880221ad?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
