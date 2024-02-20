<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'telephone' => '089655443322',
            'postcode' => '56434',
            'email' => 'gudangutama@gmail.com',
            'country' => 'Indonesia',
            'status' => 1,
        ]);
        Warehouse::firstOrCreate([
            'name' => 'Outlet 1',
            'city' => 'Surakarta',
            'telephone' => '089655443323',
            'postcode' => '56434',
            'email' => 'outletsatu@gmail.com',
            'country' => 'Indonesia',
            'status' => 1,
        ]);
    }
}
