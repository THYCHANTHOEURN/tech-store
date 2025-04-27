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

        // Additional subcategories
        $graphicsCardsCategory = Category::where('slug', 'graphics-cards')->first();
        $processorsCategory = Category::where('slug', 'processors')->first();
        $ramCategory = Category::where('slug', 'ram')->first();
        $gamingCasesCategory = Category::where('slug', 'gaming-cases')->first();

        $gamingPhonesCategory = Category::where('slug', 'gaming-phones')->first();
        $mobileControllersCategory = Category::where('slug', 'mobile-controllers')->first();
        $phoneCoolingCategory = Category::where('slug', 'phone-cooling')->first();

        $pcGamesCategory = Category::where('slug', 'pc-games')->first();
        $consoleGamesCategory = Category::where('slug', 'console-games')->first();
        $gameCreditsCategory = Category::where('slug', 'game-credits')->first();
        $gamingServicesCategory = Category::where('slug', 'gaming-services')->first();

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

        // Add 20 gaming laptops to the gaming-laptops category
        $gamingLaptops = [
            [
                'name' => 'ROG Zephyrus G14',
                'slug' => 'rog-zephyrus-g14',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-ZEPH-G14',
                'price' => 1499.99,
                'sale_price' => 1399.99,
                'stock' => 35,
                'featured' => true,
                'status' => true,
                'description' => 'Ultra-slim gaming laptop with AMD Ryzen 9 processor and RTX 3060 graphics. Perfect for gaming and content creation.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                    ['image' => 'products/rog-strix-g15-2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'MSI Stealth 15M',
                'slug' => 'msi-stealth-15m',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-ST-15M',
                'price' => 1299.99,
                'sale_price' => 1199.99,
                'stock' => 25,
                'featured' => false,
                'status' => true,
                'description' => 'Ultra-thin gaming laptop with Intel Core i7 and RTX 3060 graphics in a sleek design.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Razer Blade 15',
                'slug' => 'razer-blade-15',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $razerBrand->id,
                'sku' => 'RZ-BL-15',
                'price' => 1899.99,
                'sale_price' => 1799.99,
                'stock' => 20,
                'featured' => true,
                'status' => true,
                'description' => 'Premium gaming laptop with NVIDIA RTX graphics and CNC aluminum chassis.',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Strix SCAR 17',
                'slug' => 'rog-strix-scar-17',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-SCAR-17',
                'price' => 2299.99,
                'sale_price' => 2099.99,
                'stock' => 15,
                'featured' => true,
                'status' => true,
                'description' => 'Esports-focused gaming laptop with high refresh rate display and RGB lighting.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'MSI GE76 Raider',
                'slug' => 'msi-ge76-raider',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-GE76',
                'price' => 2499.99,
                'sale_price' => 2299.99,
                'stock' => 12,
                'featured' => true,
                'status' => true,
                'description' => 'Powerful 17-inch gaming laptop with RGB light bar and top-tier gaming performance.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Flow X13',
                'slug' => 'rog-flow-x13',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-FLW-X13',
                'price' => 1399.99,
                'sale_price' => 1299.99,
                'stock' => 18,
                'featured' => false,
                'status' => true,
                'description' => 'Convertible gaming ultrabook with external GPU support for maximum versatility.',
                'images' => [
                    ['image' => 'products/gaming-laptop-bundle-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'MSI Vector GP76',
                'slug' => 'msi-vector-gp76',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-VCTR-76',
                'price' => 1899.99,
                'sale_price' => 1799.99,
                'stock' => 22,
                'featured' => false,
                'status' => true,
                'description' => 'High-performance gaming laptop with 12th Gen Intel processors and RTX graphics.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Zephyrus Duo 16',
                'slug' => 'rog-zephyrus-duo-16',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-DUO-16',
                'price' => 2899.99,
                'sale_price' => 2699.99,
                'stock' => 10,
                'featured' => true,
                'status' => true,
                'description' => 'Dual-screen gaming laptop with innovative ScreenPad Plus secondary display.',
                'images' => [
                    ['image' => 'products/rog-ally-1.jpg', 'is_primary' => true],
                    ['image' => 'products/rog-ally-2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'MSI Crosshair 15',
                'slug' => 'msi-crosshair-15',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-CRSS-15',
                'price' => 1599.99,
                'sale_price' => 1499.99,
                'stock' => 20,
                'featured' => false,
                'status' => true,
                'description' => 'Rainbow Six Extraction special edition gaming laptop with unique design elements.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Strix G17',
                'slug' => 'rog-strix-g17',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-STX-G17',
                'price' => 1599.99,
                'sale_price' => 1499.99,
                'stock' => 25,
                'featured' => false,
                'status' => true,
                'description' => '17-inch gaming laptop with AMD Ryzen processors and RTX graphics.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Razer Blade 17',
                'slug' => 'razer-blade-17',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $razerBrand->id,
                'sku' => 'RZ-BL-17',
                'price' => 2499.99,
                'sale_price' => 2299.99,
                'stock' => 15,
                'featured' => true,
                'status' => true,
                'description' => 'Premium 17-inch gaming laptop with QHD display and THX Spatial Audio.',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'MSI Alpha 17',
                'slug' => 'msi-alpha-17',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-ALPH-17',
                'price' => 1399.99,
                'sale_price' => 1299.99,
                'stock' => 22,
                'featured' => false,
                'status' => true,
                'description' => 'All-AMD gaming laptop with Ryzen processors and Radeon RX graphics.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Strix G15 Advantage Edition',
                'slug' => 'rog-strix-g15-advantage',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-G15-ADV',
                'price' => 1699.99,
                'sale_price' => 1599.99,
                'stock' => 18,
                'featured' => true,
                'status' => true,
                'description' => 'All-AMD gaming powerhouse with Ryzen 9 and Radeon RX 6800M graphics.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                    ['image' => 'products/rog-strix-g15-2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'MSI Delta 15',
                'slug' => 'msi-delta-15',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-DLTA-15',
                'price' => 1599.99,
                'sale_price' => 1499.99,
                'stock' => 20,
                'featured' => false,
                'status' => true,
                'description' => 'Thin and light AMD advantage gaming laptop with all-day battery life.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Flow Z13',
                'slug' => 'rog-flow-z13',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-FLW-Z13',
                'price' => 1799.99,
                'sale_price' => 1699.99,
                'stock' => 15,
                'featured' => true,
                'status' => true,
                'description' => 'Gaming tablet with detachable keyboard and external GPU support.',
                'images' => [
                    ['image' => 'products/rog-ally-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'MSI Pulse GL76',
                'slug' => 'msi-pulse-gl76',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-PLS-76',
                'price' => 1399.99,
                'sale_price' => 1299.99,
                'stock' => 25,
                'featured' => false,
                'status' => true,
                'description' => '17-inch gaming laptop with high refresh rate and customizable RGB keyboard.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Razer Book 13',
                'slug' => 'razer-book-13',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $razerBrand->id,
                'sku' => 'RZ-BK-13',
                'price' => 1499.99,
                'sale_price' => 1399.99,
                'stock' => 20,
                'featured' => false,
                'status' => true,
                'description' => 'Productivity-focused ultrabook with gaming-grade build quality.',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Zephyrus M16',
                'slug' => 'rog-zephyrus-m16',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-ZEPH-M16',
                'price' => 1899.99,
                'sale_price' => 1799.99,
                'stock' => 18,
                'featured' => true,
                'status' => true,
                'description' => '16-inch gaming laptop with 16:10 display and slim-bezel design.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'MSI Creator Z16',
                'slug' => 'msi-creator-z16',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-CRT-Z16',
                'price' => 2399.99,
                'sale_price' => 2199.99,
                'stock' => 12,
                'featured' => true,
                'status' => true,
                'description' => 'Creator-focused laptop with gaming capabilities and premium CNC aluminum chassis.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'ROG Zephyrus S17',
                'slug' => 'rog-zephyrus-s17',
                'category_id' => $gamingLaptopsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-ZEPH-S17',
                'price' => 2599.99,
                'sale_price' => 2399.99,
                'stock' => 10,
                'featured' => true,
                'status' => true,
                'description' => 'Premium 17-inch gaming laptop with optical-mechanical keyboard and advanced cooling.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                    ['image' => 'products/rog-strix-g15-2.jpg', 'is_primary' => false],
                ]
            ]
        ];

        // Add Gaming PC Components category products
        $gamingPcComponentsProducts = [
            // Graphics Cards
            [
                'name' => 'ROG Strix GeForce RTX 4090',
                'slug' => 'rog-strix-rtx-4090',
                'category_id' => $graphicsCardsCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-RTX4090',
                'price' => 1999.99,
                'sale_price' => 1899.99,
                'stock' => 15,
                'featured' => true,
                'status' => true,
                'description' => 'The ROG Strix GeForce RTX 4090 delivers the ultimate 4K gaming experience with unparalleled performance and cooling.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'MSI Gaming X Trio RTX 4080',
                'slug' => 'msi-gaming-x-trio-rtx-4080',
                'category_id' => $graphicsCardsCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-RTX4080-X',
                'price' => 1499.99,
                'sale_price' => 1399.99,
                'stock' => 20,
                'featured' => false,
                'status' => true,
                'description' => 'The MSI Gaming X Trio RTX 4080 delivers exceptional gaming performance with advanced cooling technology.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            // Processors
            [
                'name' => 'AMD Ryzen 9 7950X',
                'slug' => 'amd-ryzen-9-7950x',
                'category_id' => $processorsCategory->id,
                'brand_id' => $rogBrand->id, // Using ROG as a brand placeholder
                'sku' => 'AMD-R9-7950X',
                'price' => 699.99,
                'sale_price' => 649.99,
                'stock' => 30,
                'featured' => true,
                'status' => true,
                'description' => '16-core powerhouse processor with blazing fast clock speeds for gaming and content creation.',
                'images' => [
                    ['image' => 'products/rog-ally-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Intel Core i9-13900K',
                'slug' => 'intel-core-i9-13900k',
                'category_id' => $processorsCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as a brand placeholder
                'sku' => 'INTEL-I9-13900K',
                'price' => 599.99,
                'sale_price' => 579.99,
                'stock' => 25,
                'featured' => true,
                'status' => true,
                'description' => 'Intel\'s flagship gaming processor with 24 cores and 32 threads for maximum gaming performance.',
                'images' => [
                    ['image' => 'products/gaming-laptop-bundle-1.jpg', 'is_primary' => true],
                ]
            ],
            // RAM
            [
                'name' => 'Corsair Vengeance RGB Pro 32GB',
                'slug' => 'corsair-vengeance-rgb-pro-32gb',
                'category_id' => $ramCategory->id,
                'brand_id' => $rogBrand->id, // Using ROG as a brand placeholder
                'sku' => 'CORS-VENG-32GB',
                'price' => 149.99,
                'sale_price' => 129.99,
                'stock' => 50,
                'featured' => false,
                'status' => true,
                'description' => 'High-performance DDR4 RAM with dynamic RGB lighting for gaming PCs.',
                'images' => [
                    ['image' => 'products/rog-mouse.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'G.SKILL Trident Z5 RGB 64GB',
                'slug' => 'gskill-trident-z5-rgb-64gb',
                'category_id' => $ramCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as a brand placeholder
                'sku' => 'GSKILL-Z5-64GB',
                'price' => 299.99,
                'sale_price' => 279.99,
                'stock' => 40,
                'featured' => true,
                'status' => true,
                'description' => 'Ultra-fast DDR5 memory with advanced RGB lighting effects for next-gen gaming rigs.',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                ]
            ],
            // Gaming Cases
            [
                'name' => 'NZXT H510 Elite',
                'slug' => 'nzxt-h510-elite',
                'category_id' => $gamingCasesCategory->id,
                'brand_id' => $razerBrand->id, // Using Razer as a brand placeholder
                'sku' => 'NZXT-H510-ELITE',
                'price' => 149.99,
                'sale_price' => 139.99,
                'stock' => 35,
                'featured' => false,
                'status' => true,
                'description' => 'Compact mid-tower case with tempered glass panel and RGB lighting for clean gaming builds.',
                'images' => [
                    ['image' => 'products/razer-keyboard.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Cooler Master Cosmos C700M',
                'slug' => 'cooler-master-cosmos-c700m',
                'category_id' => $gamingCasesCategory->id,
                'brand_id' => $rogBrand->id, // Using ROG as a brand placeholder
                'sku' => 'CM-COSMOS-C700M',
                'price' => 399.99,
                'sale_price' => 369.99,
                'stock' => 15,
                'featured' => true,
                'status' => true,
                'description' => 'Premium full-tower case with curved tempered glass and versatile interior layout.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Add Mobile Gaming category products
        $mobileGamingProducts = [
            // Gaming Phones
            [
                'name' => 'ROG Phone 7 Ultimate',
                'slug' => 'rog-phone-7-ultimate',
                'category_id' => $gamingPhonesCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-PHONE7-ULT',
                'price' => 1299.99,
                'sale_price' => 1199.99,
                'stock' => 25,
                'featured' => true,
                'status' => true,
                'description' => 'Ultimate gaming smartphone with Snapdragon 8 Gen 2 processor and advanced cooling system.',
                'images' => [
                    ['image' => 'products/rog-ally-1.jpg', 'is_primary' => true],
                    ['image' => 'products/rog-ally-2.jpg', 'is_primary' => false],
                ]
            ],
            [
                'name' => 'Black Shark 5 Pro',
                'slug' => 'black-shark-5-pro',
                'category_id' => $gamingPhonesCategory->id,
                'brand_id' => $razerBrand->id, // Using Razer as placeholder
                'sku' => 'BS-5PRO',
                'price' => 799.99,
                'sale_price' => 749.99,
                'stock' => 30,
                'featured' => false,
                'status' => true,
                'description' => 'Gaming-focused smartphone with magnetic pop-up triggers and 144Hz display.',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                ]
            ],
            // Mobile Controllers
            [
                'name' => 'Razer Kishi V2',
                'slug' => 'razer-kishi-v2',
                'category_id' => $mobileControllersCategory->id,
                'brand_id' => $razerBrand->id,
                'sku' => 'RZ-KISHI-V2',
                'price' => 99.99,
                'sale_price' => 89.99,
                'stock' => 45,
                'featured' => true,
                'status' => true,
                'description' => 'Universal mobile gaming controller with console-quality controls and low latency connection.',
                'images' => [
                    ['image' => 'products/razer-keyboard.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Backbone One',
                'slug' => 'backbone-one',
                'category_id' => $mobileControllersCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as placeholder
                'sku' => 'BACKBONE-ONE',
                'price' => 99.99,
                'sale_price' => 89.99,
                'stock' => 40,
                'featured' => false,
                'status' => true,
                'description' => 'Premium mobile gaming controller with integrated app and social features.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            // Phone Cooling
            [
                'name' => 'ROG Phone Cooler',
                'slug' => 'rog-phone-cooler',
                'category_id' => $phoneCoolingCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-PHONE-COOL',
                'price' => 79.99,
                'sale_price' => 69.99,
                'stock' => 50,
                'featured' => true,
                'status' => true,
                'description' => 'External cooling fan for smartphones with RGB lighting and app control.',
                'images' => [
                    ['image' => 'products/rog-mouse.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Black Shark FunCooler Pro',
                'slug' => 'black-shark-funcooler-pro',
                'category_id' => $phoneCoolingCategory->id,
                'brand_id' => $razerBrand->id, // Using Razer as placeholder
                'sku' => 'BS-FUNCOOLER',
                'price' => 49.99,
                'sale_price' => 39.99,
                'stock' => 60,
                'featured' => false,
                'status' => true,
                'description' => 'Clip-on phone cooling fan with semiconductor cooling technology.',
                'images' => [
                    ['image' => 'products/gaming-laptop-bundle-1.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Add Gaming Software category products
        $gamingSoftwareProducts = [
            // PC Games
            [
                'name' => 'Cyberpunk 2077',
                'slug' => 'cyberpunk-2077-pc',
                'category_id' => $pcGamesCategory->id,
                'brand_id' => $rogBrand->id, // Using ROG as a placeholder
                'sku' => 'CP2077-PC',
                'price' => 59.99,
                'sale_price' => 49.99,
                'stock' => 100,
                'featured' => true,
                'status' => true,
                'description' => 'Open-world action-adventure RPG set in Night City, a megalopolis obsessed with power and body modification.',
                'images' => [
                    ['image' => 'products/rog-ally-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Elden Ring',
                'slug' => 'elden-ring-pc',
                'category_id' => $pcGamesCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as a placeholder
                'sku' => 'ELDEN-RING-PC',
                'price' => 59.99,
                'sale_price' => 49.99,
                'stock' => 80,
                'featured' => true,
                'status' => true,
                'description' => 'Action RPG set in a fantasy world created by Hidetaka Miyazaki and George R. R. Martin.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
            // Console Games
            [
                'name' => 'God of War Ragnarök',
                'slug' => 'god-of-war-ragnarok-ps5',
                'category_id' => $consoleGamesCategory->id,
                'brand_id' => $razerBrand->id, // Using Razer as placeholder
                'sku' => 'GOW-RAG-PS5',
                'price' => 69.99,
                'sale_price' => 59.99,
                'stock' => 75,
                'featured' => true,
                'status' => true,
                'description' => 'Action-adventure game following Kratos and Atreus as they journey through the Nine Realms.',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Zelda: Breath of the Wild',
                'slug' => 'zelda-breath-wild-switch',
                'category_id' => $consoleGamesCategory->id,
                'brand_id' => $rogBrand->id, // Using ROG as placeholder
                'sku' => 'ZELDA-BOTW-NSW',
                'price' => 59.99,
                'sale_price' => 49.99,
                'stock' => 65,
                'featured' => false,
                'status' => true,
                'description' => 'Open-world adventure game in the legendary Zelda franchise for Nintendo Switch.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ]
            ],
            // Game Credits
            [
                'name' => 'PlayStation Store Gift Card $50',
                'slug' => 'playstation-store-gift-card-50',
                'category_id' => $gameCreditsCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as placeholder
                'sku' => 'PSN-GC-50',
                'price' => 50.00,
                'sale_price' => null,
                'stock' => 200,
                'featured' => false,
                'status' => true,
                'description' => '$50 PlayStation Store gift card for games, add-ons, subscriptions and more.',
                'images' => [
                    ['image' => 'products/gaming-laptop-bundle-2.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Xbox Gift Card $100',
                'slug' => 'xbox-gift-card-100',
                'category_id' => $gameCreditsCategory->id,
                'brand_id' => $rogBrand->id, // Using ROG as placeholder
                'sku' => 'XBOX-GC-100',
                'price' => 100.00,
                'sale_price' => null,
                'stock' => 150,
                'featured' => false,
                'status' => true,
                'description' => '$100 Xbox gift card for games, add-ons, movies and more from the Microsoft Store.',
                'images' => [
                    ['image' => 'products/rog-ally-3.jpg', 'is_primary' => true],
                ]
            ],
            // Gaming Services
            [
                'name' => 'Xbox Game Pass Ultimate 3 Month',
                'slug' => 'xbox-game-pass-ultimate-3month',
                'category_id' => $gamingServicesCategory->id,
                'brand_id' => $razerBrand->id, // Using Razer as placeholder
                'sku' => 'XGPU-3M',
                'price' => 44.99,
                'sale_price' => 39.99,
                'stock' => 300,
                'featured' => true,
                'status' => true,
                'description' => '3-month subscription to Xbox Game Pass Ultimate with access to hundreds of high-quality games.',
                'images' => [
                    ['image' => 'products/razer-keyboard.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'PlayStation Plus 12 Month',
                'slug' => 'playstation-plus-12month',
                'category_id' => $gamingServicesCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as placeholder
                'sku' => 'PS-PLUS-12M',
                'price' => 59.99,
                'sale_price' => 49.99,
                'stock' => 250,
                'featured' => true,
                'status' => true,
                'description' => '12-month PlayStation Plus subscription with monthly games, online multiplayer, and exclusive discounts.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Add Gaming Headset products
        $gamingHeadsetProducts = [
            [
                'name' => 'ROG Theta 7.1',
                'slug' => 'rog-theta-7-1',
                'category_id' => $headsetCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-THETA-71',
                'price' => 299.99,
                'sale_price' => 269.99,
                'stock' => 40,
                'featured' => true,
                'status' => true,
                'description' => 'Premium gaming headset with 7.1 surround sound and AI noise-cancelling microphone.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Razer BlackShark V2 Pro',
                'slug' => 'razer-blackshark-v2-pro',
                'category_id' => $headsetCategory->id,
                'brand_id' => $razerBrand->id,
                'sku' => 'RZ-BS-V2PRO',
                'price' => 179.99,
                'sale_price' => 159.99,
                'stock' => 50,
                'featured' => true,
                'status' => true,
                'description' => 'Wireless esports gaming headset with THX Spatial Audio and ultra-soft memory foam ear cushions.',
                'images' => [
                    ['image' => 'products/razer-keyboard.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Add Gaming Monitor products
        $gamingMonitorProducts = [
            [
                'name' => 'ROG Swift PG32UQX',
                'slug' => 'rog-swift-pg32uqx',
                'category_id' => $monitorCategory->id,
                'brand_id' => $rogBrand->id,
                'sku' => 'ROG-PG32UQX',
                'price' => 2999.99,
                'sale_price' => 2799.99,
                'stock' => 10,
                'featured' => true,
                'status' => true,
                'description' => 'Ultimate 32-inch 4K HDR gaming monitor with Mini LED technology and 144Hz refresh rate.',
                'images' => [
                    ['image' => 'products/rog-strix-g15-2.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'MSI Optix MPG341CQR',
                'slug' => 'msi-optix-mpg341cqr',
                'category_id' => $monitorCategory->id,
                'brand_id' => $msiBrand->id,
                'sku' => 'MSI-MPG341CQR',
                'price' => 799.99,
                'sale_price' => 749.99,
                'stock' => 25,
                'featured' => false,
                'status' => true,
                'description' => '34-inch ultrawide curved gaming monitor with 144Hz refresh rate and RGB lighting.',
                'images' => [
                    ['image' => 'products/msi-katana.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Add PlayStation products
        $playstationProducts = [
            [
                'name' => 'PlayStation 5 Console',
                'slug' => 'playstation-5-console',
                'category_id' => $playstationCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as a placeholder
                'sku' => 'PS5-DISC',
                'price' => 499.99,
                'sale_price' => null,
                'stock' => 30,
                'featured' => true,
                'status' => true,
                'description' => 'Next-generation gaming console with ultra-high speed SSD and DualSense controller.',
                'images' => [
                    ['image' => 'products/ultimate-console-package-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'DualSense Wireless Controller',
                'slug' => 'dualsense-wireless-controller',
                'category_id' => $playstationCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as a placeholder
                'sku' => 'PS5-DUALSENSE',
                'price' => 69.99,
                'sale_price' => 59.99,
                'stock' => 50,
                'featured' => false,
                'status' => true,
                'description' => 'PlayStation 5 wireless controller with haptic feedback and adaptive triggers.',
                'images' => [
                    ['image' => 'products/ultimate-console-package-2.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Add Xbox products
        $xboxProducts = [
            [
                'name' => 'Xbox Series X Console',
                'slug' => 'xbox-series-x-console',
                'category_id' => $xboxCategory->id,
                'brand_id' => $razerBrand->id, // Using Razer as a placeholder
                'sku' => 'XBOX-SERIES-X',
                'price' => 499.99,
                'sale_price' => null,
                'stock' => 25,
                'featured' => true,
                'status' => true,
                'description' => 'Microsoft\'s most powerful console ever with 4K gaming and high frame rates.',
                'images' => [
                    ['image' => 'products/ultimate-console-package-1.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Xbox Elite Wireless Controller Series 2',
                'slug' => 'xbox-elite-controller-series-2',
                'category_id' => $xboxCategory->id,
                'brand_id' => $razerBrand->id, // Using Razer as a placeholder
                'sku' => 'XBOX-ELITE-S2',
                'price' => 179.99,
                'sale_price' => 159.99,
                'stock' => 40,
                'featured' => true,
                'status' => true,
                'description' => 'Premium Xbox controller with adjustable-tension thumbsticks and wrap-around rubberized grip.',
                'images' => [
                    ['image' => 'products/pro-gaming-setup-2.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Add Nintendo products
        $nintendoProducts = [
            [
                'name' => 'Nintendo Switch OLED Model',
                'slug' => 'nintendo-switch-oled',
                'category_id' => $nintendoCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as a placeholder
                'sku' => 'SWITCH-OLED',
                'price' => 349.99,
                'sale_price' => 329.99,
                'stock' => 35,
                'featured' => true,
                'status' => true,
                'description' => 'Nintendo Switch console with 7-inch OLED screen and enhanced audio.',
                'images' => [
                    ['image' => 'products/ultimate-console-package-2.jpg', 'is_primary' => true],
                ]
            ],
            [
                'name' => 'Nintendo Switch Pro Controller',
                'slug' => 'nintendo-switch-pro-controller',
                'category_id' => $nintendoCategory->id,
                'brand_id' => $msiBrand->id, // Using MSI as a placeholder
                'sku' => 'NSW-PRO-CTRL',
                'price' => 69.99,
                'sale_price' => 59.99,
                'stock' => 45,
                'featured' => false,
                'status' => true,
                'description' => 'Premium controller for Nintendo Switch with motion controls, HD rumble, and amiibo functionality.',
                'images' => [
                    ['image' => 'products/gaming-laptop-bundle-2.jpg', 'is_primary' => true],
                ]
            ],
        ];

        // Merge all product arrays
        $allCategoryProducts = array_merge(
            $gamingPcComponentsProducts,
            $mobileGamingProducts,
            $gamingSoftwareProducts,
            $gamingHeadsetProducts,
            $gamingMonitorProducts,
            $playstationProducts,
            $xboxProducts,
            $nintendoProducts
        );

        // Merge existing products with all new category products
        $products = array_merge($products, $gamingLaptops, $allCategoryProducts);

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
