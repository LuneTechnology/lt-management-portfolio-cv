<?php

namespace Database\Seeders;

use App\Models\WorkTag;
use Illuminate\Database\Seeder;

class WorkTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Company',
            'Education',
            'Organization',
            'Community',
            'Other',
        ];

        foreach ($tags as $tag) {
            WorkTag::firstOrCreate([
                'name' => $tag,
            ]);
        }
    }
}