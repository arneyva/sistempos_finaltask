<?php

namespace Database\Seeders;

use App\Models\ClientsTiers;
use Illuminate\Database\Seeder;

class ClientTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert some stuff
        ClientsTiers::firstOrCreate([
            'tier' => 'Homeless',
            'total_sales' => 1,
            'discount' => 1000,
            'total_amount' => 12000,
            'last_sale' => 12,
        ]);
        ClientsTiers::firstOrCreate([
            'tier' => 'Broke',
            'total_sales' => 1,
            'discount' => 1000,
            'total_amount' => 12000,
            'last_sale' => 12,
        ]);
        ClientsTiers::firstOrCreate([
            'tier' => 'Part-timer',
            'total_sales' => 1,
            'discount' => 1000,
            'total_amount' => 12000,
            'last_sale' => 12,
        ]);
        ClientsTiers::firstOrCreate([
            'tier' => 'Happily married',
            'total_sales' => 1,
            'discount' => 1000,
            'total_amount' => 12000,
            'last_sale' => 12,
        ]);
        ClientsTiers::firstOrCreate([
            'tier' => 'A lot of children',
            'total_sales' => 1,
            'discount' => 1000,
            'total_amount' => 12000,
            'last_sale' => 12,
        ]);
        ClientsTiers::firstOrCreate([
            'tier' => 'Shareholder',
            'total_sales' => 1,
            'discount' => 1000,
            'total_amount' => 12000,
            'last_sale' => 12,
        ]);
        ClientsTiers::firstOrCreate([
            'tier' => 'A lot of children',
            'total_sales' => 1,
            'discount' => 1000,
            'total_amount' => 12000,
            'last_sale' => 12,
        ]);
    }
}
