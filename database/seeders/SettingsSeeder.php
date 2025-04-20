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
            // Company Information Settings
            [
                'key'           => 'company_name',
                'value'         => 'Tech Store',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Company Name',
                'description'   => 'Official name of the company'
            ],
            [
                'key'           => 'company_address',
                'value'         => '123 Tech Street, Innovation City',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Company Address',
                'description'   => 'Physical address of the company'
            ],
            [
                'key'           => 'company_state',
                'value'         => 'State 12345',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Company State',
                'description'   => 'State or province of the company'
            ],
            [
                'key'           => 'company_country',
                'value'         => 'Country',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Company Country',
                'description'   => 'Country of the company'
            ],
            [
                'key'           => 'company_phone',
                'value'         => '+855 12 345 678',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Company Phone',
                'description'   => 'Primary phone number of the company'
            ],
            [
                'key'           => 'company_phone_secondary',
                'value'         => '+855 98 765 432',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Secondary Phone',
                'description'   => 'Secondary phone number of the company'
            ],
            [
                'key'           => 'company_email',
                'value'         => 'info@techstore.com',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Company Email',
                'description'   => 'Primary email address of the company'
            ],
            [
                'key'           => 'company_email_support',
                'value'         => 'support@techstore.com',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Support Email',
                'description'   => 'Support email address of the company'
            ],
            [
                'key'           => 'company_hours',
                'value'         => 'Monday - Friday: 9:00 AM - 6:00 PM; Saturday: 10:00 AM - 4:00 PM; Sunday: Closed',
                'group'         => 'company_info',
                'type'          => 'textarea',
                'label'         => 'Business Hours',
                'description'   => 'Business hours of the company'
            ],
            [
                'key'           => 'company_facebook',
                'value'         => 'https://www.facebook.com/',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Facebook URL',
                'description'   => 'Company Facebook page URL'
            ],
            [
                'key'           => 'company_instagram',
                'value'         => 'https://www.instagram.com/',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Instagram URL',
                'description'   => 'Company Instagram page URL'
            ],
            [
                'key'           => 'company_twitter',
                'value'         => 'https://twitter.com/',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Twitter URL',
                'description'   => 'Company Twitter page URL'
            ],
            [
                'key'           => 'company_youtube',
                'value'         => 'https://www.youtube.com/',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'YouTube URL',
                'description'   => 'Company YouTube channel URL'
            ],
            [
                'key'           => 'company_tiktok',
                'value'         => 'https://www.tiktok.com/',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'TikTok URL',
                'description'   => 'Company TikTok page URL'
            ],
            [
                'key'           => 'company_telegram',
                'value'         => 'https://t.me/',
                'group'         => 'company_info',
                'type'          => 'text',
                'label'         => 'Telegram URL',
                'description'   => 'Company Telegram channel URL'
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
