<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email'    => 'epan@admin.com',
            'password' => Hash::make('epanadmin123'),
            'username' => 'Pannn',
            'photo'    => null,
            'contact'  => '-',
            'aboutme'  => 'Administrator akun',
            'id_role'  => 1,
        ]);

        User::create([
            'email'    => 'biyan@admin.com',
            'password' => Hash::make('biyanadmin123'),
            'username' => 'Biyan',
            'photo'    => null,
            'contact'  => '08987654321',
            'aboutme'  => 'User biasa',
            'id_role'  => 1,
        ]);

        User::create([
            'email'    => 'alfian@admin.com',
            'password' => Hash::make('alfianadmin123'),
            'username' => 'Alfian Aditya',
            'photo'    => null,
            'contact'  => '08123456789',
            'aboutme'  => 'User biasa',
            'id_role'  => 1,
        ]);

        User::create([
            'email'    => 'lutpi@admin.com',
            'password' => Hash::make('lutpiadmin123'),
            'username' => 'Lutpi',
            'photo'    => null,
            'contact'  => '-',
            'aboutme'  => 'User biasa',
            'id_role'  => 1,
        ]);
    }
}