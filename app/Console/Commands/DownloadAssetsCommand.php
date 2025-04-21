<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadAssetsCommand extends Command
{
    protected $signature = 'assets:download';
    protected $description = 'Download all required assets for the store';

    private $assets = [
        'brands' => [
            'asus-rog'  => 'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=500',
            'asus-tuf'  => 'https://images.unsplash.com/photo-1603946877690-d410437c29aa?w=500',
            'razer'     => 'https://images.unsplash.com/photo-1618478594486-c65b899c4936?w=500',
            'msi'       => 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500',
        ],
        'products' => [
            'rog-ally' => [
                'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=800', // Gaming device
                'https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=800', // Gaming device
                'https://images.unsplash.com/photo-1527814050087-3793815479db?w=800', // Gaming device
            ],
            'rog-strix-g15' => [
                'https://images.unsplash.com/photo-1605134513573-384dcf99a44c?w=800',
                'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800', // Gaming laptop
            ],
            'msi-katana'        => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800',
            'razer-keyboard'    => 'https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=800',
            'rog-mouse'         => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=800',

            // default images for products
            'default' => 'public/images/default.jpg', // Local default image
        ],
        'categories' => [
            // Main categories
            'gaming-laptops'        => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800',
            'gaming-accessories'    => 'https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=800',
            'gaming-consoles'       => 'https://images.unsplash.com/photo-1486401899868-0e435ed85128?w=800',
            'gaming-pc-components'  => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=800',
            'mobile-gaming'         => 'https://images.unsplash.com/photo-1600861194942-f883de0dfe96?w=800',      // Person playing on phone
            'gaming-software'       => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=800',

            // Gaming Laptops subcategories
            'rog'           => 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800',        // ROG specific laptop
            'tuf-gaming'    => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800', // TUF specific laptop
            'razer'         => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=800',      // Razer laptop
            'msi'           => 'https://images.unsplash.com/photo-1593642634443-44adaa06623a?w=800',        // MSI gaming laptop

            // Gaming Accessories subcategories
            'gaming-mouse'      => 'https://images.unsplash.com/photo-1613141411244-0e4ac259d217?w=800',    // Gaming mouse closeup
            'gaming-keyboard'   => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800', // RGB keyboard
            'gaming-headset'    => 'https://images.unsplash.com/photo-1599669454699-248893623440?w=800',  // Gaming headset
            'gaming-monitor'    => 'https://images.unsplash.com/photo-1619953942547-233eab5a70d6?w=800',  // Gaming monitor setup

            // Gaming Consoles subcategories
            'playstation'   => 'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?w=800',    // PS5 console
            'xbox'          => 'https://images.unsplash.com/photo-1605901309584-818e25960a8f?w=800',          // Updated Xbox image
            'nintendo'      => 'https://images.unsplash.com/photo-1578303512597-81e6cc155b3e?w=800',    // Nintendo Switch
            'rog-ally'      => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=800',       // Handheld gaming


            // Gaming PC Components subcategories
            'graphics-cards'    => 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=800',
            'processors'        => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=800',
            'ram'               => 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=800',
            'gaming-cases'      => 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=800',

            // Mobile Gaming subcategories
            'gaming-phones'         => 'https://images.unsplash.com/photo-1616348436168-de43ad0db179?w=800',
            'mobile-controllers'    => 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=800',
            'phone-cooling'         => 'https://images.unsplash.com/photo-1601944179066-29786cb9d32a?w=800',      // Gaming phone


            // Gaming Software subcategories
            'pc-games'          => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=800',
            'console-games'     => 'https://images.unsplash.com/photo-1592478411213-6153e4ebc07d?w=800',
            'game-credits'      => 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=800',
            'gaming-services'   => 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=800',
        ],
        'banners' => [
            'rog-ally-banner'       => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1400&h=400',
            'gaming-laptops'        => 'https://images.unsplash.com/photo-1593642634443-44adaa06623a?w=1400&h=400',
            'gaming-accessories'    => 'https://images.unsplash.com/photo-1616588589676-62b3bd4ff6d2?w=1400&h=400',
            'rog-laptops-side'      => 'https://images.unsplash.com/photo-1593642634402-b0eb5e2eebc9?w=600&h=400',
            'msi-gaming-side'       => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=600&h=400', // Updated MSI side banner
            'razer-gear-side'       => 'https://images.unsplash.com/photo-1618478594486-c65b899c4936?w=600&h=400',
            'summer-sale-promo'     => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=800&h=300',
            'new-arrivals-promo'    => 'https://images.unsplash.com/photo-1593642634315-48f5414c3ad9?w=800&h=300',
            'special-bundles-promo' => 'https://images.unsplash.com/photo-1593642634524-b40b5baae6bb?w=800&h=300'
        ],
        // Add settings-related images
        'settings' => [

            // logo
            'site-logo'         => 'public/images/logo.png', // Local logo image

            'about-us'          => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=1200&h=800', // Modern tech store interior
            'team-ceo'          => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400', // Professional male CEO
            'team-cto'          => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400', // Professional female CTO
            'team-marketing'    => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400', // Marketing director
            'team-support'      => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400', // Customer support lead
        ]
    ];

    public function handle()
    {
        $this->info('Starting asset download...');

        // Create storage link if it doesn't exist
        if (!file_exists(public_path('storage'))) {
            $this->call('storage:link');
        }

        foreach ($this->assets as $type => $items) {
            $this->info("\nDownloading {$type} images...");

            $path = storage_path("app/public/{$type}");
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            foreach ($items as $name => $urls) {
                $urls = is_array($urls) ? $urls : [$urls];
                foreach ($urls as $index => $url) {
                    // Modified naming logic
                    $filename = count($urls) > 1 ?
                        "{$name}-" . ($index + 1) . ".jpg" :
                        "{$name}.jpg";
                    $filepath = "{$path}/{$filename}";

                    $this->info("Downloading: {$filename}");

                    // Check if the URL is actually a local file path
                    if (file_exists($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                        try {
                            // If it's a local file, copy it instead of downloading
                            if (file_exists($url)) {
                                File::copy($url, $filepath);
                                $this->info("✓ Successfully copied local file: {$filename}");
                            } else {
                                $this->error("✗ Local file not found: {$url}");
                            }
                        } catch (\Exception $e) {
                            $this->error("✗ Error copying {$filename}: " . $e->getMessage());
                        }
                    } else {
                        try {
                            $response = Http::withHeaders([
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124'
                            ])->timeout(30)->get($url);

                            if ($response->successful()) {
                                File::put($filepath, $response->body());
                                $this->info("✓ Successfully downloaded: {$filename}");
                            } else {
                                $this->error("✗ Failed to download {$filename}: HTTP {$response->status()}");
                            }
                        } catch (\Exception $e) {
                            $this->error("✗ Error downloading {$filename}: " . $e->getMessage());
                        }
                    }

                    // Add a small delay between requests
                    usleep(500000); // 0.5 second delay
                }
            }
        }

        $this->info("\nAsset download process completed!");
    }
}
