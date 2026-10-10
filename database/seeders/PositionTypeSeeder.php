<?php

namespace Database\Seeders;

use App\Models\PositionType;
use Illuminate\Database\Seeder;

class PositionTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Game Programmer', 'Data Operator', 'Full Stack Developer', 'Game Designer'] as $name) {
            PositionType::firstOrCreate(['name' => $name]);
        }
    }
}
