<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'epan@admin.com', 'username' => 'Pannn', 'contact' => '-', 'aboutme' => 'Administrator akun'],
            ['email' => 'biyan@admin.com', 'username' => 'Biyan', 'contact' => '08987654321', 'aboutme' => 'User biasa'],
            ['email' => 'alfian@admin.com', 'username' => 'Alfian Aditya', 'contact' => '08123456789', 'aboutme' => 'User biasa'],
            ['email' => 'lutpi@admin.com', 'username' => 'Lutpi', 'contact' => '-', 'aboutme' => 'User biasa'],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'username' => $data['username'],
                    'photo' => null,
                    'contact' => $data['contact'],
                    'aboutme' => $data['aboutme'],
                    'password' => Hash::make(match ($data['email']) {
                        'epan@admin.com' => 'epanadmin123',
                        'biyan@admin.com' => 'biyanadmin123',
                        'alfian@admin.com' => 'alfianadmin123',
                        default => 'lutpiadmin123',
                    }),
                    'id_role' => 1,
                ]
            );
        }
    }
}
