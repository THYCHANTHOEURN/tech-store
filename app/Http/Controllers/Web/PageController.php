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
        return Inertia::render('Web/Contact');
    }

    /**
     * Show the terms page.
     *
     * @return \Inertia\Response
     */
    public function terms()
    {
        return Inertia::render('Web/Terms');
    }
}
