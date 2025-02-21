<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name'          => 'ASUS ROG',
                'slug'          => 'asus-rog',
                'logo'          => 'brands/asus-rog.jpg',
                'status'        => true,
                'description'   => 'Republic of Gamers (ROG) is ASUS\'s gaming brand. ROG offers innovative gaming laptops, motherboards, graphics cards, displays, and more.'
            ],
            [
                'name'          => 'ASUS TUF Gaming',
                'slug'          => 'asus-tuf-gaming',
                'logo'          => 'brands/asus-tuf.png',
                'status'        => true,
                'description'   => 'TUF Gaming is ASUS\'s durable gaming brand, offering reliable performance at affordable prices.'
            ],
            [
                'name'          => 'Razer',
                'slug'          => 'razer',
                'logo'          => 'brands/razer.png',
                'status'        => true,
                'description'   => 'Razer™ is the world\'s leading lifestyle brand for gamers, known for high-performance gaming hardware, software and systems.'
            ],
            [
                'name'          => 'MSI',
                'slug'          => 'msi',
                'logo'          => 'brands/msi.png',
                'status'        => true,
                'description'   => 'MSI is a world leader in gaming hardware, providing innovative gaming devices for players worldwide.'
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
