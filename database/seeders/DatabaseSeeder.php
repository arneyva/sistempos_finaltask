<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(WarehouseSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CurrencySeeder::class);
    }
}
