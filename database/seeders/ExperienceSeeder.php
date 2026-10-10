<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\PositionType;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkType;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::orderBy('id_project')->get();
        $users = User::orderBy('id_user')->take(2)->get();
        $position = PositionType::firstOrFail();
        $workType = WorkType::firstOrFail();

        if ($projects->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($users as $index => $user) {
            $project = $projects[$index] ?? $projects->first();

            Experience::updateOrCreate(
                [
                    'id_user' => $user->id_user,
                    'id_project' => $project->id_project,
                ],
                [
                    'id_work' => $project->id_work,
                    'id_position_type' => $position->id_position_type,
                    'id_work_type' => $workType->id_work_type,
                ]
            );
        }
    }
}
