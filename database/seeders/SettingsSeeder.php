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
                'key'           => 'about_us_image',
                'value'         => 'settings/about-us.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'About Us Main Image',
                'description'   => 'The main image displayed on the About Us page'
            ],
            [
                'key'           => 'team_ceo_image',
                'value'         => 'settings/team-ceo.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'CEO Image',
                'description'   => 'Image of the CEO on the About Us page'
            ],
            // Add name setting for CEO
            [
                'key'           => 'team_ceo_name',
                'value'         => 'John Doe',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CEO Name',
                'description'   => 'Name of the CEO displayed on the About Us page'
            ],
            [
                'key'           => 'team_cto_image',
                'value'         => 'settings/team-cto.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'CTO Image',
                'description'   => 'Image of the CTO on the About Us page'
            ],
            // Add name setting for CTO
            [
                'key'           => 'team_cto_name',
                'value'         => 'Jane Smith',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CTO Name',
                'description'   => 'Name of the CTO displayed on the About Us page'
            ],
            [
                'key'           => 'team_marketing_image',
                'value'         => 'settings/team-marketing.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'Marketing Director Image',
                'description'   => 'Image of the Marketing Director on the About Us page'
            ],
            // Add name setting for Marketing Director
            [
                'key'           => 'team_marketing_name',
                'value'         => 'Michael Johnson',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Marketing Director Name',
                'description'   => 'Name of the Marketing Director displayed on the About Us page'
            ],
            [
                'key'           => 'team_support_image',
                'value'         => 'settings/team-support.jpg',
                'group'         => 'about_page',
                'type'          => 'image',
                'label'         => 'Support Lead Image',
                'description'   => 'Image of the Support Lead on the About Us page'
            ],
            // Add name setting for Support Lead
            [
                'key'           => 'team_support_name',
                'value'         => 'Sarah Williams',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Support Lead Name',
                'description'   => 'Name of the Support Lead displayed on the About Us page'
            ],
            // Add positions for each team member
            [
                'key'           => 'team_ceo_position',
                'value'         => 'CEO & Founder',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CEO Position',
                'description'   => 'Job title of the CEO displayed on the About Us page'
            ],
            [
                'key'           => 'team_cto_position',
                'value'         => 'CTO',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'CTO Position',
                'description'   => 'Job title of the CTO displayed on the About Us page'
            ],
            [
                'key'           => 'team_marketing_position',
                'value'         => 'Marketing Director',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Marketing Director Position',
                'description'   => 'Job title of the Marketing Director displayed on the About Us page'
            ],
            [
                'key'           => 'team_support_position',
                'value'         => 'Customer Support Lead',
                'group'         => 'about_page',
                'type'          => 'text',
                'label'         => 'Support Lead Position',
                'description'   => 'Job title of the Support Lead displayed on the About Us page'
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
