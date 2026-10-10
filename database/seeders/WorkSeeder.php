<?php

namespace Database\Seeders;

use App\Models\Work;
use App\Models\WorkTag;
use Illuminate\Database\Seeder;

class WorkSeeder extends Seeder
{
    public function run(): void
    {
        $company = WorkTag::where('name', 'Company')->firstOrFail();
        $education = WorkTag::where('name', 'Education')->firstOrFail();
        $organization = WorkTag::where('name', 'Organization')->firstOrFail();
        $other = WorkTag::where('name', 'Other')->firstOrFail();

        $works = [
            ['name' => 'PT. Molca Teknologi Nusantara', 'place' => 'Surabaya', 'id_work_tag' => $company->id_work_tag],
            ['name' => 'Aventala Grimoire Studio', 'place' => 'Surabaya', 'id_work_tag' => $company->id_work_tag],
            ['name' => 'Politeknik Elektronika Negeri Surabaya', 'place' => 'Surabaya', 'id_work_tag' => $education->id_work_tag],
            ['name' => 'SMKN 1 Boyolangu', 'place' => 'Tulungagung', 'id_work_tag' => $education->id_work_tag],
            ['name' => 'SMKN 12 Surabaya', 'place' => 'Surabaya', 'id_work_tag' => $education->id_work_tag],
            ['name' => 'BSO Game Technology', 'place' => 'Surabaya', 'id_work_tag' => $organization->id_work_tag],
            ['name' => 'Independent / Personal Project', 'place' => 'Remote', 'id_work_tag' => $other->id_work_tag],
        ];

        foreach ($works as $work) {
            Work::firstOrCreate(
                ['name' => $work['name']],
                ['place' => $work['place'], 'id_work_tag' => $work['id_work_tag']]
            );
        }
    }
}
