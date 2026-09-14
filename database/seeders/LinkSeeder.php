<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Link;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        Link::create([
            'name' => 'Github',
            'link'=> 'https//:github.com',
            'id_project' => '1',
        ]);
    }
}