<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StackType;

class StackTypeSeeder extends Seeder
{
    public function run(): void
    {
        $stackTypes = [
            ['name' => 'Programming Language'],
            ['name' => 'Frontend'],
            ['name' => 'Backend'],
            ['name' => 'Game Engine'],
            ['name' => 'Database'],
            ['name' => 'Design Tool'],
            ['name' => 'Version Control'],
            ['name' => 'AI / Machine Learning'],
            ['name' => 'XR / Immersive'],
        ];

        foreach ($stackTypes as $stackType) {
            StackType::firstOrCreate($stackType);
        }
    }
}