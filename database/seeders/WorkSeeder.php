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

        // Company
        Work::create([
            'name' => 'PT. Molca Teknologi Nusantara',
            'place' => 'Surabaya',
            'id_work_tag' => $company->id_work_tag,
        ]);

        Work::create([
            'name' => 'Aventala Grimoire Studio',
            'place' => 'Surabaya',
            'id_work_tag' => $company->id_work_tag,
        ]);

        // Education
        Work::create([
            'name' => 'Politeknik Elektronika Negeri Surabaya',
            'place' => 'Surabaya',
            'id_work_tag' => $education->id_work_tag,
        ]);

        Work::create([
            'name' => 'SMKN 1 Boyolangu',
            'place' => 'Tulungagung',
            'id_work_tag' => $education->id_work_tag,
        ]);

        Work::create([
            'name' => 'SMKN 12 Surabaya',
            'place' => 'Surabaya',
            'id_work_tag' => $education->id_work_tag,
        ]);

        // Organization
        Work::create([
            'name' => 'BSO Game Technology',
            'place' => 'Surabaya',
            'id_work_tag' => $organization->id_work_tag,
        ]);
    }
}