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
        $user1 = User::firstOrCreate([
            'firstname' => 'user',
            'lastname' => 'user',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '089655443322',
            'gender' => 'Laki-laki',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ]);
        $user2 = User::firstOrCreate([
            'firstname' => 'admin',
            'lastname' => 'admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '089655443321',
            'gender' => 'Perempuan',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ]);
        $user2->assignRole('superadmin');
    }
}
