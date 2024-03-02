<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'firstname' => 'user',
            'lastname' => 'user',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '089655443322',
            'gender' => 'laki-laki',
            'status' => 1,
            'is_all_warehouses' => 1,
        ]);
    }
}
