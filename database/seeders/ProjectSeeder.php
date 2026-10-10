<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\Work;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $web = Category::where('name', 'Web Development')->firstOrFail();
        $game = Category::where('name', 'Game Development')->firstOrFail();
        $digitalTwin = Category::where('name', 'Digital Twin')->firstOrFail();

        $personalProject = Work::where('name', 'Independent / Personal Project')->firstOrFail();
        $aventala = Work::where('name', 'Aventala Grimoire Studio')->firstOrFail();
        $molca = Work::where('name', 'PT. Molca Teknologi Nusantara')->firstOrFail();

        $projects = [
            [
                'name' => 'LT Portfolio CV',
                'date_in' => '2026-01-01',
                'date_out' => null,
                'id_category' => $web->id_category,
                'id_work' => $personalProject->id_work,
            ],
            [
                'name' => 'RED LIFE',
                'date_in' => '2025-08-01',
                'date_out' => '2026-07-01',
                'id_category' => $game->id_category,
                'id_work' => $aventala->id_work,
            ],
            [
                'name' => 'Digital Twin PT AlamTri Resources Indonesia',
                'date_in' => '2026-01-01',
                'date_out' => '2026-07-01',
                'id_category' => $digitalTwin->id_category,
                'id_work' => $molca->id_work,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['name' => $project['name']], $project);
        }
    }
}
