<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Show the about us page.
     *
     * @return \Inertia\Response
     */
    public function about()
    {
        // Get settings for about us page with image URLs
        $aboutUsImage       = Setting::where('key', 'about_us_image')->first();

        // Get team member images
        $teamCeoImage       = Setting::where('key', 'team_ceo_image')->first();
        $teamCtoImage       = Setting::where('key', 'team_cto_image')->first();
        $teamMarketingImage = Setting::where('key', 'team_marketing_image')->first();
        $teamSupportImage   = Setting::where('key', 'team_support_image')->first();

        // Get team member names
        $teamCeoName        = Setting::get('team_ceo_name', 'John Doe');
        $teamCtoName        = Setting::get('team_cto_name', 'Jane Smith');
        $teamMarketingName  = Setting::get('team_marketing_name', 'Michael Johnson');
        $teamSupportName    = Setting::get('team_support_name', 'Sarah Williams');

        // Get team member positions
        $teamCeoPosition        = Setting::get('team_ceo_position', 'CEO & Founder');
        $teamCtoPosition        = Setting::get('team_cto_position', 'CTO');
        $teamMarketingPosition  = Setting::get('team_marketing_position', 'Marketing Director');
        $teamSupportPosition    = Setting::get('team_support_position', 'Customer Support Lead');

        return Inertia::render('Web/AboutUs', [
            'aboutUsImage'  => $aboutUsImage ? $aboutUsImage->image_url : '/storage/settings/about-us.jpg',
            'teamImages'    => [
                'ceo'       => $teamCeoImage ? $teamCeoImage->image_url : '/storage/settings/team-ceo.jpg',
                'cto'       => $teamCtoImage ? $teamCtoImage->image_url : '/storage/settings/team-cto.jpg',
                'marketing' => $teamMarketingImage ? $teamMarketingImage->image_url : '/storage/settings/team-marketing.jpg',
                'support'   => $teamSupportImage ? $teamSupportImage->image_url : '/storage/settings/team-support.jpg',
            ],
            'teamNames'     => [
                'ceo'       => $teamCeoName,
                'cto'       => $teamCtoName,
                'marketing' => $teamMarketingName,
                'support'   => $teamSupportName,
            ],
            'teamPositions' => [
                'ceo'       => $teamCeoPosition,
                'cto'       => $teamCtoPosition,
                'marketing' => $teamMarketingPosition,
                'support'   => $teamSupportPosition,
            ]
        ]);
    }

    /**
     * Show the contact page.
     *
     * @return \Inertia\Response
     */
    public function contact()
    {
        // Get company information settings
        $companyName            = Setting::get('company_name', 'Tech Store');
        $companyAddress         = Setting::get('company_address', '123 Tech Street, Innovation City');
        $companyState           = Setting::get('company_state', 'State 12345');
        $companyCountry         = Setting::get('company_country', 'Country');
        $companyPhone           = Setting::get('company_phone', '+855 12 345 678');
        $companyPhoneSecondary  = Setting::get('company_phone_secondary', '+855 98 765 432');
        $companyEmail           = Setting::get('company_email', 'info@techstore.com');
        $companyEmailSupport    = Setting::get('company_email_support', 'support@techstore.com');
        $companyHours           = Setting::get('company_hours', 'Monday - Friday: 9:00 AM - 6:00 PM; Saturday: 10:00 AM - 4:00 PM; Sunday: Closed');

        // Get social media links
        $facebookLink = Setting::get('company_facebook', 'https://www.facebook.com/');
        $instagramLink = Setting::get('company_instagram', 'https://www.instagram.com/');
        $twitterLink = Setting::get('company_twitter', 'https://twitter.com/');
        $youtubeLink = Setting::get('company_youtube', 'https://www.youtube.com/');

        return Inertia::render('Web/Contact', [
            'companyInfo' => [
                'name' => $companyName,
                'address' => $companyAddress,
                'state' => $companyState,
                'country' => $companyCountry,
                'phone' => $companyPhone,
                'phoneSecondary' => $companyPhoneSecondary,
                'email' => $companyEmail,
                'emailSupport' => $companyEmailSupport,
                'hours' => $companyHours,
                'social' => [
                    'facebook' => $facebookLink,
                    'instagram' => $instagramLink,
                    'twitter' => $twitterLink,
                    'youtube' => $youtubeLink,
                ],
            ],
        ]);
    }

    /**
     * Show the terms page.
     *
     * @return \Inertia\Response
     */
    public function terms()
    {
        // Get company information needed for terms
        $companyName    = Setting::get('company_name', 'Tech Store');
        $companyAddress = Setting::get('company_address', '123 Tech Street, Innovation City');
        $companyState   = Setting::get('company_state', 'State 12345');
        $companyCountry = Setting::get('company_country', 'Country');
        $companyPhone   = Setting::get('company_phone', '+855 12 345 678');
        $companyEmail   = Setting::get('company_email', 'info@techstore.com');
        $termsUpdatedAt = Setting::get('terms_updated_at', '2023-12-15'); // Default if not set

        return Inertia::render('Web/Terms', [
            'companyInfo' => [
                'name'              => $companyName,
                'address'           => $companyAddress,
                'state'             => $companyState,
                'country'           => $companyCountry,
                'fullAddress'       => "$companyAddress, $companyState, $companyCountry",
                'phone'             => $companyPhone,
                'email'             => $companyEmail,
                'termsUpdatedAt'    => $termsUpdatedAt,
            ]
        ]);
    }
}
