<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::firstOrCreate([
            'code' => 'USD',
            'name' => 'US Dolar',
            'symbol' => '$',
        ]);
        Currency::firstOrCreate([
            'code' => 'IDR',
            'name' => 'Rupiah Indonesia',
            'symbol' => 'Rp. ',
        ]);
    }
}
