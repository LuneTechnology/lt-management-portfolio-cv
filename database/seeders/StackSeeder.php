<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stack;

class StackSeeder extends Seeder
{
    public function run(): void
    {
        $stacks = [
            [
                'nama' => 'Laravel',
                'id_stack_type' => 3,
            ],
            [
                'nama' => 'Vue.js',
                'id_stack_type' => 2,
            ],
            [
                'nama' => 'React',
                'id_stack_type' => 2,
            ],
            [
                'nama' => 'PHP',
                'id_stack_type' => 1,
            ],
            [
                'nama' => 'JavaScript',
                'id_stack_type' => 1,
            ],
            [
                'nama' => 'C#',
                'id_stack_type' => 1,
            ],
            [
                'nama' => 'Unity',
                'id_stack_type' => 4,
            ],
            [
                'nama' => 'Unreal Engine',
                'id_stack_type' => 4,
            ],
            [
                'nama' => 'MySQL',
                'id_stack_type' => 5,
            ],
        ];

        foreach ($stacks as $stack) {
            Stack::firstOrCreate(
                ['nama' => $stack['nama']],
                ['id_stack_type' => $stack['id_stack_type']]
            );
        }
    }
}