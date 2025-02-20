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
                'position' => Banner::POSITION_SLIDER
            ],
            [
                'title' => 'Gaming Laptops',
                'image' => 'banners/gaming-laptops.jpg',
                'link' => '/categories/gaming-laptops',
                'status' => true,
                'position' => Banner::POSITION_SIDE
            ],
            [
                'title' => 'Gaming Accessories',
                'image' => 'banners/gaming-accessories.jpg',
                'link' => '/categories/gaming-accessories',
                'status' => true,
                'position' => Banner::POSITION_PROMO
            ]
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
