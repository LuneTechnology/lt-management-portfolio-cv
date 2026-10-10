<?php

namespace Database\Seeders;

use App\Models\WorkType;
use Illuminate\Database\Seeder;

class WorkTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Freelance', 'Internship', 'Contract', 'Part-time', 'Full-time'] as $name) {
            WorkType::firstOrCreate(['name' => $name]);
        }
    }
}
