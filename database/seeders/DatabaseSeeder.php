<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            WorkTagSeeder::class,
            WorkSeeder::class,
            PositionTypeSeeder::class,
            WorkTypeSeeder::class,
            StackTypeSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            AdminSeeder::class,
            ProjectSeeder::class,
            EducationSeeder::class,
            ExperienceSeeder::class,
            LinkSeeder::class,
            StackSeeder::class,
            TaskSeeder::class,
            ExperienceStackSeeder::class,
            AchievementSeeder::class,
        ]);
    }
}
