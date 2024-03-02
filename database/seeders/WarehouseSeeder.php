<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::firstOrCreate([
            'name' => 'Gudang Utama',
            'city' => 'Surakarta',
            'mobile' => '089655443322',
            'zip' => '56434',
            'email' => 'gudangutama@gmail.com',
            'country' => 'Indonesia',
        ]);
        Warehouse::firstOrCreate([
            'name' => 'Outlet 1',
            'city' => 'Surakarta',
            'mobile' => '089655443323',
            'zip' => '56434',
            'email' => 'outletsatu@gmail.com',
            'country' => 'Indonesia',
        ]);
    }
}
