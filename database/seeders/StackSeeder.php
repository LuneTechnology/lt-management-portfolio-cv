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
                'id_tag' => 3,
            ],
            [
                'nama' => 'Vue.js',
                'id_tag' => 2,
            ],
            [
                'nama' => 'React',
                'id_tag' => 2,
            ],
            [
                'nama' => 'PHP',
                'id_tag' => 1,
            ],
            [
                'nama' => 'JavaScript',
                'id_tag' => 1,
            ],
            [
                'nama' => 'C#',
                'id_tag' => 1,
            ],
            [
                'nama' => 'Unity',
                'id_tag' => 4,
            ],
            [
                'nama' => 'Unreal Engine',
                'id_tag' => 4,
            ],
            [
                'nama' => 'MySQL',
                'id_tag' => 5,
            ],
        ];

        foreach ($stacks as $stack) {
            Stack::firstOrCreate(
                ['nama' => $stack['nama']],
                ['id_tag' => $stack['id_tag']]
            );
        }
    }
}