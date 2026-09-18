<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
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

        foreach ($tags as $tag) {
            Tag::firstOrCreate($tag);
        }
    }
}