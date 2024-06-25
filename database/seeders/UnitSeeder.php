<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::firstOrCreate([
            'name' => 'gram',
            'ShortName' => 'g',
            'operator' => '*',
            'operator_value' => 1,
        ]);
        Unit::firstOrCreate([
            'name' => 'Kilogram',
            'ShortName' => 'kg',
            'base_unit' => 1,
            'operator' => '*',
            'operator_value' => 1000,
        ]);
    }
}
