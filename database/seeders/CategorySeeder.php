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
            'name' => 'Makanan Berat',
        ]);

        Category::firstOrCreate([
            'code' => 'DRINK-01',
            'name' => 'Minuman Ringan',
        ]);

        Category::firstOrCreate([
            'code' => 'DRINK-02',
            'name' => 'Minuman Berat',
        ]);

        Category::firstOrCreate([
            'code' => 'SNACK-01',
            'name' => 'Snack Asin',
        ]);

        Category::firstOrCreate([
            'code' => 'SNACK-02',
            'name' => 'Snack Manis',
        ]);

        Category::firstOrCreate([
            'code' => 'FRUIT-01',
            'name' => 'Buah Segar',
        ]);
        Category::firstOrCreate([
            'code' => 'VEG-01',
            'name' => 'Sayuran Segar',
        ]);
    }
}
