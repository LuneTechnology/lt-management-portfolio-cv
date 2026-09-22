<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Experience;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkType;
use App\Models\PositionType;
use App\Models\Project;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        // Helper pencarian berdasarkan ID spesifik dengan fallback aman
        $getUserId   = fn($id) => User::find($id)?->id_user ?? User::first()?->id_user;
        $getWorkId   = fn($id) => Work::find($id)?->id_work ?? Work::first()?->id_work;
        $getPosition = fn($id) => PositionType::find($id)?->id_position_type ?? PositionType::first()?->id_position_type;
        $getWorkType = fn($id) => WorkType::find($id)?->id_work_type ?? WorkType::first()?->id_work_type;
        $getProject  = fn($id) => Project::find($id)?->id_project ?? Project::first()?->id_project;

        // Susun daftar data experience berdasarkan ID spesifik
        $experiences = [
            [
                'id_user'          => $getUserId(1),  // Menargetkan User ID 1
                'id_work'          => $getWorkId(1),  // Menargetkan Work ID 1
                'id_position_type' => $getPosition(1),
                'id_work_type'     => $getWorkType(2),
                'id_project'       => $getProject(1),
            ],
            [
                'id_user'          => $getUserId(2),  
                'id_work'          => $getWorkId(2),  
                'id_position_type' => $getPosition(2),
                'id_work_type'     => $getWorkType(2),
                'id_project'       => $getProject(1),
            ],
        ];

        foreach ($experiences as $data) {
            if ($data['id_user'] && $data['id_work']) {
                Experience::create($data);
            }
        }
    }
}