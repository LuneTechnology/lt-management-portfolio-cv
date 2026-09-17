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
                'tag' => 'Web Framework',
            ],
            [
                'nama' => 'Vue.js',
                'tag' => 'Web Framework',
            ],
            [
                'nama' => 'React',
                'tag' => 'Web Framework',
            ],
            [
                'nama' => 'PHP',
                'tag' => 'Programming Language',
            ],
            [
                'nama' => 'JavaScript',
                'tag' => 'Programming Language',
            ],
            [
                'nama' => 'C#',
                'tag' => 'Programming Language',
            ],
            [
                'nama' => 'Unity',
                'tag' => 'Game Engine',
            ],
            [
                'nama' => 'Unreal Engine',
                'tag' => 'Game Engine',
            ],
            [
                'nama' => 'MySQL',
                'tag' => 'Database',
            ],
        ];

        foreach ($stacks as $stack) {
            Stack::create($stack);
        }
    }
}