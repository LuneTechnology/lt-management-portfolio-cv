<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\ExperienceStack;
use App\Models\Stack;
use Illuminate\Database\Seeder;

class ExperienceStackSeeder extends Seeder
{
    public function run(): void
    {
        $stacks = Stack::orderBy('id_stack')->take(3)->get();

        foreach (Experience::all() as $experience) {
            foreach ($stacks as $stack) {
                ExperienceStack::firstOrCreate([
                    'id_experience' => $experience->id_experience,
                    'id_stack' => $stack->id_stack,
                ]);
            }
        }
    }
}
