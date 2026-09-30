<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Education;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        Education::create([
            'name' => 'SMA Negeri 1 Surabaya',
            'major' => 'IPA',
            'place' => 'Surabaya',
            'level' => 'SMA',
            'date_in' => '2015-07-01',
            'date_out' => '2018-05-30',
            'gpa' => null,
            'id_user' => 4,
        ]);

        Education::create([
            'name' => 'Politeknik Elektronika Negeri Surabaya',
            'major' => 'Game Technology',
            'place' => 'Surabaya',
            'level' => 'D3',
            'date_in' => '2022-08-01',
            'date_out' => null,
            'gpa' => 3.78,
            'id_user' => 4,
        ]);
    }
}