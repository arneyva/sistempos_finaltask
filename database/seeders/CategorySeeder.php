<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate([
            'code' => 'FOOD-01',
            'name' => 'Makanan Ringan',
        ]);
        Category::firstOrCreate([
            'code' => 'FOOD-02',
            'name' => 'Produk Segar',
        ]);
        Category::firstOrCreate([
            'code' => 'Selfcare-01',
            'name' => 'Skincare',
        ]);
        Category::firstOrCreate([
            'code' => 'Selfcare-02',
            'name' => 'Alat Mandi',
        ]);
        Category::firstOrCreate([
            'code' => 'FOOD-03',
            'name' => 'Minuman',
        ]);

        Category::firstOrCreate([
            'code' => 'FOOD-04',
            'name' => 'Makanan Instan',
        ]);

        Category::firstOrCreate([
            'code' => 'FOOD-05',
            'name' => 'Buah-buahan',
        ]);

        Category::firstOrCreate([
            'code' => 'FOOD-06',
            'name' => 'Daging dan Ayam',
        ]);

        Category::firstOrCreate([
            'code' => 'FOOD-07',
            'name' => 'Ikan dan Produk Laut',
        ]);

        Category::firstOrCreate([
            'code' => 'FOOD-08',
            'name' => 'Sayuran',
        ]);

        Category::firstOrCreate([
            'code' => 'FOOD-09',
            'name' => 'Produk Susu',
        ]);

        Category::firstOrCreate([
            'code' => 'FOOD-10',
            'name' => 'Produk Organik',
        ]);
    }
}
