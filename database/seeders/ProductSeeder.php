<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $rogAllyCategory = Category::where('slug', 'gaming-consoles')->first();
        $rogCategory = Category::where('slug', 'gaming-laptops')->first();
        $accessoryCategory = Category::where('slug', 'gaming-accessories')->first();

        $rogBrand = Brand::where('slug', 'asus-rog')->first();
        $msiBrand = Brand::where('slug', 'msi')->first();
        $razerBrand = Brand::where('slug', 'razer')->first();

        $products = [
            [
                'name' => 'ROG Ally',
                'slug' => 'rog-ally',
                'category_id' => $rogAllyCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-ALLY-Z1E',
                'price' => 699.99,
                'sale_price' => 649.99,
                'stock' => 50,
                'featured' => true,
                'status' => true,
                'description' => 'The ROG Ally is a powerful handheld gaming device...',
                'images' => [
                    ['image' => 'products/rog-ally-1.jpg', 'is_primary' => true],
                    ['image' => 'products/rog-ally-2.jpg', 'is_primary' => false],
                    ['image' => 'products/rog-ally-3.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'ROG Strix G15',
                'slug' => 'rog-strix-g15',
                'category_id' => $rogCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-G15-RTX4060',
                'price' => 1299.99,
                'sale_price' => 1199.99,
                'stock' => 30,
                'featured' => true,
                'status' => true,
                'description' => 'The ROG Strix G15 is a powerful gaming laptop...',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                    ['image' => 'products/rog-strix-g15-2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'MSI Katana GF66',
                'slug' => 'msi-katana-gf66',
                'category_id' => $rogCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-KT-RTX3060',
                'price' => 1099.99,
                'sale_price' => 999.99,
                'stock' => 25,
                'featured' => true,
                'status' => true,
                'description' => 'MSI Katana GF66 gaming laptop featuring RTX 3060...',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Razer BlackWidow V3',
                'slug' => 'razer-blackwidow-v3',
                'category_id' => $accessoryCategory->id,
                'brand_id' => $razerBrand->id,
                'sku' => 'RZ-BW-V3',
                'price' => 139.99,
                'sale_price' => 119.99,
                'stock' => 100,
                'featured' => true,
                'status' => true,
                'description' => 'Razer BlackWidow V3 mechanical gaming keyboard...',
                'images' => [
                    ['image' => 'products/razer-keyboard.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Chakram Mouse',
                'slug' => 'rog-chakram-mouse',
                'category_id' => $accessoryCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-CHK-X',
                'price' => 149.99,
                'sale_price' => 129.99,
                'stock' => 75,
                'featured' => true,
                'status' => true,
                'description' => 'ROG Chakram wireless gaming mouse with programmable joystick...',
                'images' => [
                    ['image' => 'products/rog-mouse.jpg', 'is_primary' => true],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $images = $productData['images'];
            unset($productData['images']);

            $product = Product::create($productData);

            foreach ($images as $imageData) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageData['image'],
                    'is_primary' => $imageData['is_primary']
                ]);
            }
        }
    }
}
