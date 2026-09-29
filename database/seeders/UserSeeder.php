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
            'email'    => 'Pannn@example.com',
            'password' => Hash::make('password123'),
            'username' => 'Pannn',
            'photo'    => null,
            'contact'  => '08123456789',
            'aboutme'  => 'Administrator akun',
            'id_role'  => 1,
        ]);

        User::create([
            'email'    => 'singkek@example.com',
            'password' => Hash::make('password123'),
            'username' => 'singkek',
            'photo'    => null,
            'contact'  => '08987654321',
            'aboutme'  => 'User biasa',
            'id_role'  => 1,
        ]);

        User::create([
            'email'    => 'alfianaditya730@gmail.com',
            'password' => Hash::make('alfianaditya730!'),
            'username' => 'Alfian Aditya',
            'photo'    => null,
            'contact'  => '08123456789',
            'aboutme'  => 'User biasa',
            'id_role'  => 1,
        ]);
    }
}