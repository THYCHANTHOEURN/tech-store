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
        // Get parent categories
        $gamingLaptopsCategory = Category::where('slug', 'gaming-laptops')->first();
        $gamingAccessoriesCategory = Category::where('slug', 'gaming-accessories')->first();
        $gamingConsolesCategory = Category::where('slug', 'gaming-consoles')->first();

        // Get child categories
        $rogCategory = Category::where('slug', 'rog')->first();
        $tufCategory = Category::where('slug', 'tuf-gaming')->first();
        $razerLaptopCategory = Category::where('slug', 'razer')->first();
        $msiLaptopCategory = Category::where('slug', 'msi')->first();

        $mouseCategory = Category::where('slug', 'gaming-mouse')->first();
        $keyboardCategory = Category::where('slug', 'gaming-keyboard')->first();
        $headsetCategory = Category::where('slug', 'gaming-headset')->first();
        $monitorCategory = Category::where('slug', 'gaming-monitor')->first();

        $playstationCategory = Category::where('slug', 'playstation')->first();
        $xboxCategory = Category::where('slug', 'xbox')->first();
        $nintendoCategory = Category::where('slug', 'nintendo')->first();
        $rogAllyCategory = Category::where('slug', 'rog-ally')->first();

        // Get brands
        $rogBrand = Brand::where('slug', 'asus-rog')->first();
        $msiBrand = Brand::where('slug', 'msi')->first();
        $razerBrand = Brand::where('slug', 'razer')->first();

        $products = [
            // Parent Category Products
            [
                'name' => 'Gaming Laptop Bundle',
                'slug' => 'gaming-laptop-bundle',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'GLB-2023',
                'price' => 2499.99,
                'sale_price' => 2299.99,
                'stock' => 15,
                'featured' => true,
                'status' => true,
                'description' => 'Complete gaming laptop bundle with accessories...',
                'images' => [
                    ['image' => 'products/gaming-laptop-bundle-1.jpg', 'is_primary' => true],
                    ['image' => 'products/gaming-laptop-bundle-2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'Pro Gaming Setup',
                'slug' => 'pro-gaming-setup',
                'category_id' => $gamingAccessoriesCategory->id,
                'brand_id' => $razerBrand->id,
                'sku' => 'PGS-2023',
                'price' => 899.99,
                'sale_price' => 799.99,
                'stock' => 20,
                'featured' => true,
                'status' => true,
                'description' => 'Complete professional gaming setup bundle...',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                    ['image' => 'products/pro-gaming-setup-2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'Ultimate Console Package',
                'slug' => 'ultimate-console-package',
                'category_id' => $gamingConsolesCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'UCP-2023',
                'price' => 799.99,
                'sale_price' => 749.99,
                'stock' => 25,
                'featured' => true,
                'status' => true,
                'description' => 'Ultimate gaming console package with accessories...',
                'images' => [
                    ['image' => 'products/ultimate-console-package-1.jpg', 'is_primary' => true],
                    ['image' => 'products/ultimate-console-package-2.jpg', 'is_primary' => false],
                ]
            ],

            // ROG Products
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
            // MSI Products
            [
                'name' => 'MSI Katana GF66',
                'slug' => 'msi-katana-gf66',
                'category_id' => $msiLaptopCategory->id,
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
            // Gaming Keyboard Category
            [
                'name' => 'Razer BlackWidow V3',
                'slug' => 'razer-blackwidow-v3',
                'category_id' => $keyboardCategory->id,
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
            // Gaming Mouse Category
            [
                'name' => 'ROG Chakram Mouse',
                'slug' => 'rog-chakram-mouse',
                'category_id' => $mouseCategory->id,
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
            // ROG Ally Category
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
        ];

        foreach ($products as $productData) {
            $images = $productData['images'];
            unset($productData['images']);

            $product = Product::create($productData);

            foreach ($images as $imageData) {
                ProductImage::create([
                    'product_id'    => $product->id,
                    'image'         => $imageData['image'],
                    'is_primary'    => $imageData['is_primary']
                ]);
            }
        }
    }
}
