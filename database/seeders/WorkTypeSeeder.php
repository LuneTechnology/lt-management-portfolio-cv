<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkType;

class WorkTypeSeeder extends Seeder
{
    public function run(): void
    {
        $workTypes = [
            ['name' => 'Freelance'],
            ['name' => 'Internship'],
            ['name' => 'Contract'],
            ['name' => 'Part-time'],
            ['name' => 'Full-time'],
        ];

        foreach ($workTypes as $type) {
            WorkType::create($type);
        }
    }
}