<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'SuperAdmin@avra.com'],
            [
                'username' => 'Super Admin',
                'photo' => null,
                'contact' => null,
                'aboutme' => null,
                'password' => Hash::make('SuperAdmin123!'),
                'id_role' => 2,
            ]
        );
    }
}