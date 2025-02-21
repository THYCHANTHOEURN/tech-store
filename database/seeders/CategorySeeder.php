<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gaming Laptops',
                'slug' => 'gaming-laptops',
                'image' => 'categories/gaming-laptops.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'ROG', 'slug' => 'rog'],
                    ['name' => 'TUF Gaming', 'slug' => 'tuf-gaming'],
                    ['name' => 'Razer', 'slug' => 'razer'],
                    ['name' => 'MSI', 'slug' => 'msi'],
                ]
            ],
            [
                'name' => 'Gaming Accessories',
                'slug' => 'gaming-accessories',
                'image' => 'categories/gaming-accessories.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Gaming Mouse', 'slug' => 'gaming-mouse'],
                    ['name' => 'Gaming Keyboard', 'slug' => 'gaming-keyboard'],
                    ['name' => 'Gaming Headset', 'slug' => 'gaming-headset'],
                    ['name' => 'Gaming Monitor', 'slug' => 'gaming-monitor'],
                ]
            ],
            [
                'name' => 'Gaming Consoles',
                'slug' => 'gaming-consoles',
                'image' => 'categories/gaming-consoles.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'PlayStation', 'slug' => 'playstation'],
                    ['name' => 'Xbox', 'slug' => 'xbox'],
                    ['name' => 'Nintendo', 'slug' => 'nintendo'],
                    ['name' => 'ROG Ally', 'slug' => 'rog-ally'],
                ]
            ],
            // Add more categories as needed
        ];

        foreach ($categories as $category) {
            $children   = $category['children'] ?? [];
            unset($category['children']);
            $parent     = Category::create($category);

            foreach ($children as $child) {
                $child['parent_id'] = $parent->id;
                $child['status']    = true;
                $child['image']     = 'categories/' . $child['slug'] . '.jpg';
                Category::create($child);
            }
        }
    }
}
