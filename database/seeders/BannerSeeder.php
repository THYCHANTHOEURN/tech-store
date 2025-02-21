<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            // Slider Banners
            [
                'title'     => 'ROG Ally Gaming Console',
                'image'     => 'banners/rog-ally-banner.jpg',
                'link'      => '/products/rog-ally',
                'status'    => true,
                'position'  => Banner::POSITION_SLIDER
            ],
            [
                'title'     => 'Gaming Laptops Collection',
                'image'     => 'banners/gaming-laptops.jpg',
                'link'      => '/categories/gaming-laptops',
                'status'    => true,
                'position'  => Banner::POSITION_SLIDER
            ],
            [
                'title'     => 'Premium Gaming Accessories',
                'image'     => 'banners/gaming-accessories.jpg',
                'link'      => '/categories/gaming-accessories',
                'status'    => true,
                'position'  => Banner::POSITION_SLIDER
            ],

            // Side Banners
            [
                'title'     => 'ROG Laptops',
                'image'     => 'banners/rog-laptops-side.jpg',
                'link'      => '/brands/asus-rog',
                'status'    => true,
                'position'  => Banner::POSITION_SIDE
            ],
            // [
            //     'title' => 'MSI Gaming',
            //     'image' => 'banners/msi-gaming-side.jpg',
            //     'link' => '/brands/msi',
            //     'status' => true,
            //     'position' => Banner::POSITION_SIDE
            // ],
            // [
            //     'title' => 'Razer Gear',
            //     'image' => 'banners/razer-gear-side.jpg',
            //     'link' => '/brands/razer',
            //     'status' => true,
            //     'position' => Banner::POSITION_SIDE
            // ],

            // Promo Banners
            [
                'title'     => 'Summer Gaming Sale',
                'image'     => 'banners/summer-sale-promo.jpg',
                'link'      => '/sale',
                'status'    => true,
                'position'  => Banner::POSITION_PROMO
            ],
            [
                'title'     => 'New Arrivals',
                'image'     => 'banners/new-arrivals-promo.jpg',
                'link'      => '/new-arrivals',
                'status'    => true,
                'position'  => Banner::POSITION_PROMO
            ],
            [
                'title'     => 'Special Bundles',
                'image'     => 'banners/special-bundles-promo.jpg',
                'link'      => '/bundles',
                'status'    => true,
                'position'  => Banner::POSITION_PROMO
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
