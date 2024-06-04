<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class DefaultCustomers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::firstOrCreate([
            'id' => 1,
            'name' => 'Walk-in Customer',
            'email' => 'walkin@gmail.com',
            'phone' => '0812345678',
            'score' => 0,
            'is_poin_activated' => 0,
        ]);
        Client::factory(10)->create();
    }
}
