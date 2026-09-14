<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email'    => 'admin@example.com',
            'password' => Hash::make('password123'),
            'username' => 'admin',
            'photo'    => null,
            'contact'  => '08123456789',
            'aboutme'  => 'Administrator akun',
            'id_role'  => 1, // pastikan id_role = 1 sudah ada di tabel roles
        ]);

        User::create([
            'email'    => 'user@example.com',
            'password' => Hash::make('password123'),
            'username' => 'testuser',
            'photo'    => null,
            'contact'  => '08987654321',
            'aboutme'  => 'User biasa',
            'id_role'  => 2, // sesuaikan dengan id_role yang ada
        ]);
    }
}