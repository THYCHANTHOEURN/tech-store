<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        // Example of how is_primary works
        $images = [
            'rog-ally' => [
                ['image' => 'products/rog-ally-1.jpg', 'is_primary' => true],  // This will be shown as main image
                ['image' => 'products/rog-ally-2.jpg', 'is_primary' => false], // Secondary image
                ['image' => 'products/rog-ally-3.jpg', 'is_primary' => false], // Secondary image
            ],
            'rog-strix-g15' => [
                ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ['image' => 'products/rog-strix-g15-2.jpg', 'is_primary' => false],
            ],
            'msi-katana-gf66' => [
                ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
            ],
            'razer-blackwidow-v3' => [
                ['image' => 'products/razer-keyboard.jpg', 'is_primary' => true],
            ],
            'rog-chakram-mouse' => [
                ['image' => 'products/rog-mouse.jpg', 'is_primary' => true],
            ],
            // Add the missing products
            'gaming-laptop-bundle' => [
                ['image' => 'products/gaming-laptop-bundle-1.jpg', 'is_primary' => true],
                ['image' => 'products/gaming-laptop-bundle-2.jpg', 'is_primary' => false],
            ],
            'pro-gaming-setup' => [
                ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                ['image' => 'products/pro-gaming-setup-2.jpg', 'is_primary' => false],
            ],
            'ultimate-console-package' => [
                ['image' => 'products/ultimate-console-package-1.jpg', 'is_primary' => true],
                ['image' => 'products/ultimate-console-package-2.jpg', 'is_primary' => false],
            ],
        ];

        foreach ($images as $productSlug => $productImages) {
            $product = Product::where('slug', $productSlug)->first();
            if ($product) {
                foreach ($productImages as $imageData) {
                    ProductImage::create([
                        'product_id'    => $product->id,
                        'image'         => $imageData['image'],
                        'is_primary'    => $imageData['is_primary']
                    ]);
                }
            }
        }
    }
}
