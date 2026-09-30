<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Work;
use App\Models\User;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $pens = Work::where(
            'name',
            'Politeknik Elektronika Negeri Surabaya'
        )->firstOrFail();

        $smkn1 = Work::where(
            'name',
            'SMKN 1 Boyolangu'
        )->firstOrFail();

        $smkn12 = Work::where(
            'name',
            'SMKN 12 Surabaya'
        )->firstOrFail();

        $user = User::firstOrFail();

        Education::create([
            'id_work' => $smkn1->id_work,
            'major' => 'IPA',
            'level' => 'SMA',
            'date_in' => '2015-07-01',
            'date_out' => '2018-05-30',
            'gpa' => null,
            'id_user' => $user->id_user,
        ]);

        Education::create([
            'id_work' => $pens->id_work,
            'major' => 'Game Technology',
            'level' => 'D3',
            'date_in' => '2022-08-01',
            'date_out' => null,
            'gpa' => 3.78,
            'id_user' => $user->id_user,
        ]);

        Education::create([
            'id_work' => $smkn12->id_work,
            'major' => 'Software Engineering',
            'level' => 'SMK',
            'date_in' => '2019-08-01',
            'date_out' => '2022-02-01',
            'gpa' => null,
            'id_user' => $user->id_user,
        ]);
    }
}