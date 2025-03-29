<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // About Us Page Images
            [
                'key' => 'about_us_image',
                'value' => 'settings/about-us.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'About Us Main Image',
                'description' => 'The main image displayed on the About Us page'
            ],
            [
                'key' => 'team_ceo_image',
                'value' => 'settings/team-ceo.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'CEO Image',
                'description' => 'Image of the CEO on the About Us page'
            ],
            [
                'key' => 'team_cto_image',
                'value' => 'settings/team-cto.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'CTO Image',
                'description' => 'Image of the CTO on the About Us page'
            ],
            [
                'key' => 'team_marketing_image',
                'value' => 'settings/team-marketing.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'Marketing Director Image',
                'description' => 'Image of the Marketing Director on the About Us page'
            ],
            [
                'key' => 'team_support_image',
                'value' => 'settings/team-support.jpg',
                'group' => 'about_page',
                'type' => 'image',
                'label' => 'Support Lead Image',
                'description' => 'Image of the Support Lead on the About Us page'
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
