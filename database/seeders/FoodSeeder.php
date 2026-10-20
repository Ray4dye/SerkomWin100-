<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('foods')->insert([
            [
                'name' => 'Nasi Goreng',
                'category' => 'Makanan',
                'price' => 15000,
                'description' => 'Nasi goreng spesial dengan bumbu rahasia.',
                'image' => '',
            ],
            [
                'name' => 'Es Teh Manis',
                'category' => 'Minuman',
                'price' => 5000,
                'description' => 'Es teh manis segar untuk menemani makan Anda.',
                'image' => '',
            ],
            [
                'name' => 'Keripik Singkong',
                'category' => 'Cemilan',
                'price' => 8000,
                'description' => 'Keripik singkong renyah dan gurih.',
                'image' => '',
            ],
            [
                'name' => 'Mie Ayam',
                'category' => 'Makanan',
                'price' => 12000,
                'description' => 'Mie ayam lezat dengan kuah kaldu yang gurih.',
                'image' => '',
            ],
            [
                'name' => 'Jus Jeruk',
                'category' => 'Minuman',
                'price' => 7000,
                'description' => 'Jus jeruk segar untuk menyegarkan hari Anda.',
                'image' => '',
            ],
        ]);
    }
}
