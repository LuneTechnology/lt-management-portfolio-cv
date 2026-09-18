<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            WorkSeeder::class,
            WorkTypeSeeder::class,
            PositionTypeSeeder::class,
            ProjectSeeder::class,
            LinkSeeder::class,
            UserSeeder::class,
            EducationSeeder::class,
            StackSeeder::class,
            TaskSeeder::class,
            ExperienceSeeder::class,
            ExperienceStackSeeder::class,
        ]);
    }
}