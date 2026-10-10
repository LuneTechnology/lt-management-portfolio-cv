<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['id_role' => 1], ['name' => 'Admin']);
        Role::firstOrCreate(['id_role' => 2], ['name' => 'Super Admin']);
    }
}
