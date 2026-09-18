<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Work;

class WorkSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            [
                'name'  => 'Nilawarsa Studio',
                'place' => 'Surabaya',
            ],
            [
                'name'  => 'BPS Tuban',
                'place' => 'Tuban',
            ],
            [
                'name'  => 'Politeknik Elektronika Negeri Surabaya',
                'place' => 'Surabaya',
            ],
        ];

        foreach ($works as $work) {
            Work::create($work);
        }
    }
}