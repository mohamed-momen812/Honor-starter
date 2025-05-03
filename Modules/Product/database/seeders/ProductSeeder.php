<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $product_1 = Product::create([
            'name' => 'Laptop',
            'description' => 'High-performance laptop',
            'price' => 999.99,
            'stock' => 100,
            'sku' => 'LAP123',
        ]);

        $product_1->categories()->attach(1); // Assuming category ID 1 is for Electronics

        $product_2 = Product::create([
            'name' => 'Smartphone',
            'description' => 'Latest smartphone model',
            'price' => 699.99,
            'stock' => 50,
            'sku' => 'SMP456',
        ]);

        $product_2->categories()->attach(1); // Assuming category ID 1 is for Electronics
    }
}
