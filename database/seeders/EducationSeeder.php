<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Education::create([
            'name'       => 'SMA Negeri 1 Surabaya',
            'major'      => 'IPA',
            'place'      => 'Surabaya',
            'start_date' => '2015-07-01',
            'end_date'   => '2018-05-30',
            'gpa'        => null,
            'id_user'    => 1,
        ]);

        Education::create([
            'name'       => 'Politeknik Elektronika Negeri Surabaya',
            'major'      => 'Teknik Informatika',
            'place'      => 'Surabaya',
            'start_date' => '2018-08-01',
            'end_date'   => '2022-07-15',
            'gpa'        => 3.75,
            'id_user'    => 1,
        ]);

        Education::create([
            'name'       => 'Institut Teknologi Sepuluh Nopember',
            'major'      => 'Sistem Informasi',
            'place'      => 'Surabaya',
            'start_date' => '2022-09-01',
            'end_date'   => null,
            'gpa'        => null,
            'id_user'    => 2,
        ]);
    }
}