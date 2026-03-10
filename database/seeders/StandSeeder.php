<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\SellerProfile;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StandSeeder extends Seeder
{
    public function run(): void
    {
        $stands = [
            // ─── Stand 1: Warung Bu Dewi ─────────────────────────────
            [
                'stand' => [
                    'code'      => 'BUDEWI',
                    'name'      => 'Warung Bu Dewi',
                    'location'  => 'Blok A - Kantin Utama',
                    'is_active' => true,
                ],
                'seller' => [
                    'name'               => 'Dewi Rahayu',
                    'email'              => 'dewi@gmail.com',
                    'password'           => Hash::make('password'),
                    'role'               => 'seller',
                    'balance'            => 500000,
                    'email_verified_at'  => now(),
                ],
                'seller_profile' => [
                    'status'     => 'approved',
                    'stand_name' => 'Warung Bu Dewi',
                    'phone'      => '081234567801',
                    'approved_at'=> now(),
                ],
                'menus' => [
                    ['name' => 'Nasi Padang Komplit',   'type' => 'makanan',  'price' => 18000, 'stock' => 30, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Nasi Rendang Daging',   'type' => 'makanan',  'price' => 20000, 'stock' => 25, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1574484284002-952d92456975?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Nasi Ayam Pop',          'type' => 'makanan',  'price' => 16000, 'stock' => 20, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Gulai Tunjang',          'type' => 'makanan',  'price' => 22000, 'stock' => 10, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Sayur Daun Singkong',    'type' => 'makanan',  'price' => 5000,  'stock' => 50, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1540914124281-342587941389?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Es Teh Manis Padang',    'type' => 'minuman',  'price' => 5000,  'stock' => 50, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Air Jeruk Nipis',         'type' => 'minuman',  'price' => 6000,  'stock' => 40, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1600271886742-f049cd5bba3f?auto=format&fit=crop&w=800&q=80'],
                ],
            ],

            // ─── Stand 2: Mie Nyemek Chika ───────────────────────────
            [
                'stand' => [
                    'code'      => 'MIENYEMEKC',
                    'name'      => 'Mie Nyemek Chika',
                    'location'  => 'Blok B - Kantin Utama',
                    'is_active' => true,
                ],
                'seller' => [
                    'name'               => 'Chika Amelia',
                    'email'              => 'chika@gmail.com',
                    'password'           => Hash::make('password'),
                    'role'               => 'seller',
                    'balance'            => 750000,
                    'email_verified_at'  => now(),
                ],
                'seller_profile' => [
                    'status'     => 'approved',
                    'stand_name' => 'Mie Nyemek Chika',
                    'phone'      => '081234567802',
                    'approved_at'=> now(),
                ],
                'menus' => [
                    ['name' => 'Mie Nyemek Spesial',    'type' => 'makanan',  'price' => 14000, 'stock' => 40, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Mie Nyemek Pedas Level 3','type'=> 'makanan',  'price' => 15000, 'stock' => 30, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Bakso Urat Jumbo',       'type' => 'makanan',  'price' => 15000, 'stock' => 35, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Bakso Mercon',            'type' => 'makanan',  'price' => 16000, 'stock' => 20, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Bakso Goreng',            'type' => 'makanan',  'price' => 10000, 'stock' => 50, 'is_available' => false, 'photo' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Es Jeruk Segar',          'type' => 'minuman',  'price' => 6000,  'stock' => 50, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1600271886742-f049cd5bba3f?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Teh Hangat',              'type' => 'minuman',  'price' => 4000,  'stock' => 60, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=800&q=80'],
                ],
            ],

            // ─── Stand 3: Snack Corner Fitri ─────────────────────────
            [
                'stand' => [
                    'code'      => 'SNACKFITRI',
                    'name'      => 'Snack Corner Fitri',
                    'location'  => 'Blok C - Dekat Pintu Masuk',
                    'is_active' => true,
                ],
                'seller' => [
                    'name'               => 'Fitri Handayani',
                    'email'              => 'fitri@gmail.com',
                    'password'           => Hash::make('password'),
                    'role'               => 'seller',
                    'balance'            => 300000,
                    'email_verified_at'  => now(),
                ],
                'seller_profile' => [
                    'status'     => 'approved',
                    'stand_name' => 'Snack Corner Fitri',
                    'phone'      => '081234567803',
                    'approved_at'=> now(),
                ],
                'menus' => [
                    ['name' => 'Gorengan Mix (5 pcs)',  'type' => 'makanan',  'price' => 8000,  'stock' => 100,'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Pisang Goreng Crispy',  'type' => 'makanan',  'price' => 7000,  'stock' => 80, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1528975604071-b4dc52a2d18c?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Tempe Mendoan (3 pcs)', 'type' => 'makanan',  'price' => 6000,  'stock' => 60, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1574484284002-952d92456975?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Cireng Isi Keju',        'type' => 'makanan',  'price' => 10000, 'stock' => 50, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Batagor Bumbu Kacang',  'type' => 'makanan',  'price' => 10000, 'stock' => 40, 'is_available' => false, 'photo' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Kopi Susu Gula Aren',   'type' => 'minuman',  'price' => 10000, 'stock' => 40, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1561047029-3000c68339ca?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Es Susu Coklat',         'type' => 'minuman',  'price' => 8000,  'stock' => 45, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Teh Susu Thai',          'type' => 'minuman',  'price' => 9000,  'stock' => 35, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=800&q=80'],
                ],
            ],

            // ─── Stand 4: Segar Bugar ─────────────────────────────────
            [
                'stand' => [
                    'code'      => 'SEGARBUGAR',
                    'name'      => 'Segar Bugar',
                    'location'  => 'Blok D - Kantin Luar',
                    'is_active' => true,
                ],
                'seller' => [
                    'name'               => 'Rizky Pratama',
                    'email'              => 'rizky@gmail.com',
                    'password'           => Hash::make('password'),
                    'role'               => 'seller',
                    'balance'            => 250000,
                    'email_verified_at'  => now(),
                ],
                'seller_profile' => [
                    'status'     => 'approved',
                    'stand_name' => 'Segar Bugar',
                    'phone'      => '081234567804',
                    'approved_at'=> now(),
                ],
                'menus' => [
                    ['name' => 'Jus Alpukat Susu',       'type' => 'minuman',  'price' => 12000, 'stock' => 30, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1623065422902-30a2d299bbe4?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Jus Mangga Harum Manis', 'type' => 'minuman',  'price' => 11000, 'stock' => 35, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1546173159-315724a31696?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Jus Strawberry',          'type' => 'minuman',  'price' => 12000, 'stock' => 25, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1553361371-9b22f78e8b1d?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Smoothie Pisang Coklat', 'type' => 'minuman',  'price' => 14000, 'stock' => 20, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Es Cincau Hijau',         'type' => 'minuman',  'price' => 7000,  'stock' => 50, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Teh Tarik Spesial',       'type' => 'minuman',  'price' => 9000,  'stock' => 40, 'is_available' => false, 'photo' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Salad Buah',              'type' => 'makanan',  'price' => 13000, 'stock' => 20, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80'],
                    ['name' => 'Yogurt Granola Bowl',     'type' => 'makanan',  'price' => 16000, 'stock' => 15, 'is_available' => true,  'photo' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=800&q=80'],
                ],
            ],
        ];

        foreach ($stands as $data) {
            // 1. Upsert Stand
            $stand = Stand::updateOrCreate(
                ['code' => $data['stand']['code']],
                $data['stand']
            );

            // 2. Upsert Seller user
            $seller = User::updateOrCreate(
                ['email' => $data['seller']['email']],
                $data['seller']
            );

            // 3. Upsert SellerProfile linked to Stand
            SellerProfile::updateOrCreate(
                ['user_id' => $seller->id],
                array_merge($data['seller_profile'], ['stand_id' => $stand->id])
            );

            // 4. Upsert MenuItems per stand
            foreach ($data['menus'] as $menu) {
                MenuItem::updateOrCreate(
                    ['stand_id' => $stand->id, 'name' => $menu['name']],
                    array_merge($menu, ['stand_id' => $stand->id])
                );
            }
        }
    }
}
