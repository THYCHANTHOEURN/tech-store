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
            [
                'name' => 'Gaming PC Components',
                'slug' => 'gaming-pc-components',
                'image' => 'categories/gaming-pc-components.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Graphics Cards', 'slug' => 'graphics-cards'],
                    ['name' => 'Processors', 'slug' => 'processors'],
                    ['name' => 'RAM', 'slug' => 'ram'],
                    ['name' => 'Gaming Cases', 'slug' => 'gaming-cases'],
                ]
            ],
            [
                'name' => 'Mobile Gaming',
                'slug' => 'mobile-gaming',
                'image' => 'categories/mobile-gaming.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'Gaming Phones', 'slug' => 'gaming-phones'],
                    ['name' => 'Mobile Controllers', 'slug' => 'mobile-controllers'],
                    ['name' => 'Phone Cooling', 'slug' => 'phone-cooling'],
                ]
            ],
            [
                'name' => 'Gaming Software',
                'slug' => 'gaming-software',
                'image' => 'categories/gaming-software.jpg',
                'status' => true,
                'children' => [
                    ['name' => 'PC Games', 'slug' => 'pc-games'],
                    ['name' => 'Console Games', 'slug' => 'console-games'],
                    ['name' => 'Game Credits', 'slug' => 'game-credits'],
                    ['name' => 'Gaming Services', 'slug' => 'gaming-services'],
                ]
            ]
        ];

        foreach ($categories as $category) {
            $children = $category['children'] ?? [];
            unset($category['children']);
            $parent = Category::create($category);

            foreach ($children as $child) {
                $child['image']         = 'categories/' . $child['slug'] . '.jpg';
                $child['status']        = true;
                $child['parent_id']     = $parent->id;
                Category::create($child);
            }
        }
    }
}
