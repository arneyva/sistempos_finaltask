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
    }
}
