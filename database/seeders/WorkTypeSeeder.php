<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkType;

class WorkTypeSeeder extends Seeder
{
    public function run(): void
    {
        $workTypes = [
            ['name' => 'Remote'],
            ['name' => 'Onsite'],
            ['name' => 'Hybrid'],
            ['name' => 'Contract'],
        ];

        foreach ($workTypes as $type) {
            WorkType::create($type);
        }
    }
}