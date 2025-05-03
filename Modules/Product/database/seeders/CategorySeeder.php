<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices',
        ]);
        Category::create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Fashion and apparel',
        ]);
    }
}
