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
            'address' => 'Karangasem,laweyan,ska',
            'google_maps' => 'https://maps.app.goo.gl/sCJFvsUQmenk75ag9',
            'latitude' => -7.5623686,
            'longitude' => 110.8099521,
        ]);
        Warehouse::firstOrCreate([
            'name' => 'Outlet 1',
            'city' => 'Surakarta',
            'mobile' => '089655443323',
            'zip' => '56434',
            'email' => 'outletsatu@gmail.com',
            'country' => 'Indonesia',
            'address' => 'Karangasem,laweyan,ska',
            'google_maps' => 'https://maps.app.goo.gl/sCJFvsUQmenk75ag9',
            'latitude' => -7.5623686,
            'longitude' => 110.8099521,
        ]);
        Warehouse::firstOrCreate([
            'name' => 'Outlet 2',
            'city' => 'Surakarta',
            'mobile' => '089655443324',
            'zip' => '56434',
            'email' => 'outletdua@gmail.com',
            'country' => 'Indonesia',
            'address' => 'Karangasem,laweyan,ska',
            'google_maps' => 'https://maps.app.goo.gl/sCJFvsUQmenk75ag9',
            'latitude' => -7.5623686,
            'longitude' => 110.8099521,
        ]);
    }
}
