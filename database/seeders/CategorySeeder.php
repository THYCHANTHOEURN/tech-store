<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 parent categories
        Category::factory(20)->create()->each(function ($parent) {
            // Create 4 child categories for each parent (20 * 4 = 80 children)
            Category::factory(4)->create([
                'parent_id' => $parent->id
            ]);
        });
    }
}
