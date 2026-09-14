<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::create([
            'name' => 'Roblok',
            'date_in'=> '2021-01-01',
            'date_out'=> '2025-01-01',
            'id_category'=> '1',
        ]);
    }
}