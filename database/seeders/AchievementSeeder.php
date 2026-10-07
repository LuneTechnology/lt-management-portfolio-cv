<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;
use App\Models\Category;
use App\Models\User;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $competition   = Category::firstOrCreate(['name' => 'Competition']);
        $certification = Category::firstOrCreate(['name' => 'Certification']);

        $achievements = [
            [
                'name'        => 'Top 3 Incubated Startup',
                'description' => 'PLN Startup Day 2025',
                'id_category' => $competition->id_category,
                'type'        => 'Award',
                'place'       => 'PLN',
                'date'        => '2025-08-01',
                'status'      => 'Verified',
            ],
            [
                'name'        => '1st Place - Final Project Competition',
                'description' => 'AI Virtual Assistant in FPS Survival Game',
                'id_category' => $competition->id_category,
                'type'        => 'Award',
                'place'       => 'PENS',
                'date'        => '2026-06-01',
                'status'      => 'Verified',
            ],
            [
                'name'        => 'Best Website Design',
                'description' => 'Polytechnic Creative Festival 2023',
                'id_category' => $competition->id_category,
                'type'        => 'Award',
                'place'       => 'PENS',
                'date'        => '2023-10-01',
                'status'      => 'Verified',
            ],
            [
                'name'        => 'IGDX Bootcamp Participant',
                'description' => 'Game Development Bootcamp',
                'id_category' => $certification->id_category,
                'type'        => 'Training',
                'place'       => 'IGDX',
                'date'        => '2023-01-01',
                'status'      => 'Completed',
            ],
            [
                'name'        => 'KKSI Game Development Participant',
                'description' => 'Kreativitas dan Kewirausahaan Siswa Indonesia',
                'id_category' => $certification->id_category,
                'type'        => 'Training',
                'place'       => 'KKSI',
                'date'        => '2021-01-01',
                'status'      => 'Completed',
            ]
        ];
         foreach ($achievements as $achievement) {
            Achievement::create($achievement + ['id_user' => $user->id_user]);
        }
    }
}