<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'ROG Ally',
                'image' => 'banners/rog-ally-banner.jpg',
                'link' => '/products/rog-ally',
                'status' => true,
                'position' => 1
            ],
            [
                'title' => 'Gaming Laptops',
                'image' => 'banners/gaming-laptops.jpg',
                'link' => '/categories/gaming-laptops',
                'status' => true,
                'position' => 2
            ],
            [
                'title' => 'Gaming Accessories',
                'image' => 'banners/gaming-accessories.jpg',
                'link' => '/categories/gaming-accessories',
                'status' => true,
                'position' => 3
            ]
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
