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

        Brand::firstOrCreate([
            'name' => 'Apple',
            'description' => 'Merek asal Amerika Serikat, produsen iPhone, iPad, dan MacBook',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Sony',
            'description' => 'Perusahaan asal Jepang yang memproduksi berbagai macam barang elektronik',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Nike',
            'description' => 'Perusahaan pakaian dan peralatan olahraga ternama',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Adidas',
            'description' => 'Merek pakaian, sepatu, dan aksesori olahraga internasional',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Google',
            'description' => 'Perusahaan teknologi Amerika yang bergerak di berbagai bidang',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Microsoft',
            'description' => 'Perusahaan teknologi multinasional yang memproduksi perangkat lunak',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Coca-Cola',
            'description' => 'Perusahaan minuman berkarbonasi terbesar di dunia',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Toyota',
            'description' => 'Perusahaan mobil asal Jepang yang terkenal',
            'image' => 'image.png',
        ]);

        Brand::firstOrCreate([
            'name' => 'Mercedes-Benz',
            'description' => 'Produsen mobil mewah asal Jerman',
            'image' => 'image.png',
        ]);
    }
}
