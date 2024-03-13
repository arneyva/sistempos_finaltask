<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert some stuff
        DB::table('client_tiers')->insert(
            array(
                'id'     => 1,
                'tier'   => 'Homeless',
                'total_sales' => 1,
                'total_amount' => 12000,
                'last_sale' => 12,
            )
        );

        DB::table('client_tiers')->insert(
            array(
                'id'     => 2,
                'tier'   => 'Broke',
                'total_sales' => 1,
                'total_amount' => 12000,
                'last_sale' => 12,
            )
        );

        DB::table('client_tiers')->insert(
            array(
                'id'     => 3,
                'tier'   => 'Part-timer',
                'total_sales' => 1,
                'total_amount' => 12000,
                'last_sale' => 12,
            )
        );

        DB::table('client_tiers')->insert(
            array(
                'id'     => 4,
                'tier'   => 'Happily married',
                'total_sales' => 1,
                'total_amount' => 12000,
                'last_sale' => 12,
            )
        );

        DB::table('client_tiers')->insert(
            array(
                'id'     => 5,
                'tier'   => 'A lot of children',
                'total_sales' => 1,
                'total_amount' => 12000,
                'last_sale' => 12,
            )
        );

        DB::table('client_tiers')->insert(
            array(
                'id'     => 6,
                'tier'   => 'Shareholder',
                'total_sales' => 1,
                'total_amount' => 12000,
                'last_sale' => 12,
            )
        );

    }
}
