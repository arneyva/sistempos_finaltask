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
            'id' => 1,
            'firstname' => 'Staff',
            'lastname' => 'biasa',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'pin' => $this->getPin(),
            'phone' => '089655443322',
            'gender' => 'Laki-laki',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ])->assignRole('staff')
            ->warehouses()->attach(2);
        User::firstOrCreate([
            'id' => 2,
            'firstname' => 'Inventaris',
            'lastname' => 'Biasa',
            'username' => 'userutama',
            'email' => 'inventaris@gmail.com',
            'password' => bcrypt('password'),
            'pin' => $this->getPin(),
            'phone' => '089655443322',
            'gender' => 'Laki-laki',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ])->assignRole('inventaris')
            ->warehouses()->attach(1);
        User::firstOrCreate([
            'id' => 3,
            'firstname' => 'Super',
            'lastname' => 'admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'pin' => $this->getPin(),
            'phone' => '089655443321',
            'gender' => 'Perempuan',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ])->assignRole('superadmin')
            ->warehouses()->sync(Warehouse::pluck('id')->toArray());

        $superadmins = User::factory(10)->create();
        $superadmins->each(function ($superadmin) {
            // Menetapkan peran superadmin
            $superadmin->assignRole('superadmin');

            // Menyinkronkan semua gudang
            $superadmin->warehouses()->sync(Warehouse::pluck('id')->toArray());
        });

        $inventarises = User::factory(10)->create();
        $inventarises->each(function ($inventaris) {
            // Menetapkan peran superadmin
            $inventaris->assignRole('inventaris');

            // Menyinkronkan semua gudang
            $inventaris->warehouses()->attach(1);
        });

        $staffs = User::factory(10)->create();
        $staffs->each(function ($staff) {
            // Menetapkan peran superadmin
            $staff->assignRole('staff');
        });
    }

    public function getPin()
    {
        $isUnique = false;
        $uniqueCode = '';

        while (! $isUnique) {
            // Generate a random number between 0 and 999999, then pad it with zeros to ensure it is 6 digits
            $randomCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Check if the code is unique (assuming 'code' is the column where the unique codes are stored)
            $codeExists = User::where('pin', $randomCode)->exists();

            if (! $codeExists) {
                $isUnique = true;
                $uniqueCode = $randomCode;
            }
        }

        // Here, you have a unique $uniqueCode which is a 6-digit number, including leading zeros
        return $uniqueCode;
    }
}
