<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert some stuff
            DB::table('clients')->insert(
            array(
                'id'     => 1,
                'name'   => 'walk-in-customer',
                'code' => '1',
                'email' => 'walk-in-customer@example.com',
                'phone' => '123456780',
            )
        );
    }
}
