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
            'short_name' => 'g',
            'operator' => '*',
            'operator_value' => 1,
            'description' => 'Base dari satuan berat',
            'is_active' => 1,
        ]);
        Unit::firstOrCreate([
            'name' => 'Kilogram',
            'short_name' => 'kg',
            'base_unit_id' => 1,
            'operator' => '/',
            'operator_value' => 0.001,
            'description' => '1/1000 gram',
            'is_active' => 1,
        ]);
        Unit::firstOrCreate([
            'name' => 'liter',
            'short_name' => 'l',
            'operator' => '*',
            'operator_value' => 1,
            'description' => 'Base dari satuan liter',
            'is_active' => 1,
        ]);
        Unit::firstOrCreate([
            'name' => 'Mililiter',
            'short_name' => 'ml',
            'base_unit_id' => 3,
            'operator' => '*',
            'operator_value' => 1000,
            'description' => '1000 liter',
            'is_active' => 1,
        ]);
    }
}
