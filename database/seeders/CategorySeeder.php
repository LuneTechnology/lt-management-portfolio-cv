<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Memastikan Model Category di-import

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development'],
            ['name' => 'Game Development'],
            ['name' => 'Graphic Design'],
            ['name' => 'Mobile Development'],
            ['name' => 'Data & Analytics'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}