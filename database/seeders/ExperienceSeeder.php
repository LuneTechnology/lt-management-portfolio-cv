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
        $user = User::first();
        $work1 = Work::first();
        $work2 = Work::skip(1)->first() ?? $work1;
        $position1 = PositionType::first();
        $position2 = PositionType::skip(1)->first() ?? $position1;
        $workType = WorkType::first();
        $project = Project::first();

        if ($user && $work1 && $position1 && $workType && $project) {
            Experience::create([
                'id_user'          => $user->id_user,
                'id_work'          => $work2->id_work,
                'id_position_type' => $position1->id_position_type,
                'id_work_type'     => $workType->id_work_type,
                'id_project'       => $project->id_project,
            ]);

            Experience::create([
                'id_user'          => $user->id_user,
                'id_work'          => $work2->id_work,
                'id_position_type' => $position2->id_position_type,
                'id_work_type'     => $workType->id_work_type,
                'id_project'       => $project->id_project,
            ]);
        }
    }
}