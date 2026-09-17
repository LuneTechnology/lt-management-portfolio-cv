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
                'tag' => 'laravel',
            ],
            [
                'nama' => 'Vue.js',
                'tag' => 'vue',
            ],
            [
                'nama' => 'React',
                'tag' => 'react',
            ],
            [
                'nama' => 'PHP',
                'tag' => 'php',
            ],
            [
                'nama' => 'JavaScript',
                'tag' => 'javascript',
            ],
            [
                'nama' => 'C#',
                'tag' => 'csharp',
            ],
            [
                'nama' => 'Unity',
                'tag' => 'unity',
            ],
            [
                'nama' => 'Unreal Engine',
                'tag' => 'unreal',
            ],
            [
                'nama' => 'MySQL',
                'tag' => 'mysql',
            ],
        ];

        foreach ($stacks as $stack) {
            Stack::create($stack);
        }
    }
}