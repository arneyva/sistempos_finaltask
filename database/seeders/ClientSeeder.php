<?php

namespace Database\Seeders;

use App\Models\Clients;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert some stuff
        Clients::firstOrCreate([
            'name' => 'walk-in-customer',
            'code' => '1',
            'email' => 'walk-in-customer@example.com',
            'phone' => '123456780',
        ]);
    }
}
