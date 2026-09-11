<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'description' => 'Laptop untuk kebutuhan kerja',
            'price' => 10000000,
            'stock' => 10,
        ]);

        Product::create([
            'name' => 'Mouse',
            'description' => 'Mouse wireless',
            'price' => 150000,
            'stock' => 20,
        ]);

        Product::create([
            'name' => 'Keyboard',
            'description' => 'Keyboard mechanical',
            'price' => 500000,
            'stock' => 15,
        ]);
    }
}