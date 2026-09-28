<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Experience;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = Experience::all();

        foreach ($experiences as $experience) {
            Task::create([
                'id_experience' => $experience->id_experience,
                'desc' => 'Develop application',
            ]);

            Task::create([
                'id_experience' => $experience->id_experience,
                'desc' => 'Create documentation',
            ]);

            Task::create([
                'id_experience' => $experience->id_experience,
                'desc' => 'Testing application',
            ]);
        }
    }
}