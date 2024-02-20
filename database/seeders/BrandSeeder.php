<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::firstOrCreate([
            'name' => 'Samsung',
            'description' => 'Merek dari Korea,Alat elektronik',
            'image' => 'image.png',
        ]);
        Brand::firstOrCreate([
            'name' => 'Xiaomi',
            'description' => 'Merek dari Cina,Alat elektronik',
            'image' => 'image.png',
        ]);
    }
}
