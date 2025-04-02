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
        $teamCeoImage       = Setting::where('key', 'team_ceo_image')->first();
        $teamCtoImage       = Setting::where('key', 'team_cto_image')->first();
        $teamMarketingImage = Setting::where('key', 'team_marketing_image')->first();
        $teamSupportImage   = Setting::where('key', 'team_support_image')->first();

        return Inertia::render('Web/AboutUs', [
            'aboutUsImage'  => $aboutUsImage ? $aboutUsImage->image_url : '/storage/settings/about-us.jpg',
            'teamImages'    => [
                'ceo'       => $teamCeoImage ? $teamCeoImage->image_url : '/storage/settings/team-ceo.jpg',
                'cto'       => $teamCtoImage ? $teamCtoImage->image_url : '/storage/settings/team-cto.jpg',
                'marketing' => $teamMarketingImage ? $teamMarketingImage->image_url : '/storage/settings/team-marketing.jpg',
                'support'   => $teamSupportImage ? $teamSupportImage->image_url : '/storage/settings/team-support.jpg',
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
