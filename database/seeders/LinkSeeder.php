<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Project;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('name', 'LT Portfolio CV')->firstOrFail();

        Link::updateOrCreate(
            ['id_project' => $project->id_project, 'name' => 'GitHub'],
            ['link' => 'https://github.com/']
        );
    }
}
