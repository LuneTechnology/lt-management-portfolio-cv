<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Experience;
use App\Models\Stack;
use App\Models\ExperienceStack;

class ExperienceStackSeeder extends Seeder
{
    public function run(): void
    {
        $experience = Experience::first();

        if (!$experience) {
            return;
        }

        $stacks = Stack::take(3)->get();

        foreach ($stacks as $stack) {
            ExperienceStack::firstOrCreate([
                'id_experience' => $experience->id_experience,
                'id_stack' => $stack->id_stack,
            ]);
        }
    }
}