<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $descriptions = [
            'Develop application features',
            'Integrate APIs and services',
            'Test and document implementation',
        ];

        foreach (Experience::all() as $experience) {
            foreach ($descriptions as $description) {
                Task::firstOrCreate([
                    'id_experience' => $experience->id_experience,
                    'desc' => $description,
                ]);
            }
        }
    }
}
