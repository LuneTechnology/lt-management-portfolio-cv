<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,

            WorkTagSeeder::class,
            WorkSeeder::class,

            AdminSeeder::class,
            CategorySeeder::class,
            WorkTypeSeeder::class,
            PositionTypeSeeder::class,

            ProjectSeeder::class,
            LinkSeeder::class,

            EducationSeeder::class,
            ExperienceSeeder::class,

            TagSeeder::class,
            StackSeeder::class,

            TaskSeeder::class,
            ExperienceStackSeeder::class,
        ]);
    }
}