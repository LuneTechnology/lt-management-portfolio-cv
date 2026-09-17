<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Experience;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $experience = Experience::first();

        if (!$experience) {
            return;
        }

        Task::create([
            'id_experience' => $experience->id_experience,
            'name' => 'Develop Application',
        ]);

        Task::create([
            'id_experience' => $experience->id_experience,
            'name' => 'Create Documentation',
        ]);

        Task::create([
            'id_experience' => $experience->id_experience,
            'name' => 'Testing',
        ]);
    }
}