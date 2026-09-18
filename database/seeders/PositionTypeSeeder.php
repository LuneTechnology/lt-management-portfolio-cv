<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositionType;

class PositionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $positionTypes = [
            ['name' => 'Game Programmer'],
            ['name' => 'Data Operator'],
            ['name' => 'Full Stack Developer'],
            ['name' => 'Game Designer'],
        ];

        foreach ($positionTypes as $position) {
            PositionType::create($position);
        }
    }
}