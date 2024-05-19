<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
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
            'lastname' => 'biasa',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '089655443322',
            'gender' => 'Laki-laki',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ])->assignRole('staff');
        User::firstOrCreate([
            'firstname' => 'user',
            'lastname' => 'utama',
            'username' => 'userutama',
            'email' => 'user2@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '089655443322',
            'gender' => 'Laki-laki',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ])  ->assignRole('inventaris')
            ->warehouses()->attach(1);
        User::firstOrCreate([
            'firstname' => 'Super',
            'lastname' => 'admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '089655443321',
            'gender' => 'Perempuan',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ])  ->assignRole('superadmin')
            ->warehouses()->sync(Warehouse::pluck('id')->toArray());
    }
}
