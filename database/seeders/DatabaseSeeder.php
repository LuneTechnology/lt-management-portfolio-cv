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
            ProjectSeeder::class,
            LinkSeeder::class,
            UserSeeder::class,
            EducationSeeder::class,
            StackSeeder::class,
            TaskSeeder::class,
            ExperienceStackSeeder::class,
        ]);
    }
}