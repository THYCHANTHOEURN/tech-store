<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StaticPageController extends Controller
{
    /**
     * Show the about us page.
     *
     * @return \Inertia\Response
     */
    public function aboutUs()
    {
        // Get settings for about us page
        $aboutUsImage = Setting::get('about_us_image');
        $teamCeoImage = Setting::get('team_ceo_image');
        $teamCtoImage = Setting::get('team_cto_image');
        $teamMarketingImage = Setting::get('team_marketing_image');
        $teamSupportImage = Setting::get('team_support_image');

        // Prepare image URLs
        $aboutUsImageUrl = $aboutUsImage ? Storage::url($aboutUsImage) : '/images/about-us.jpg';

        $teamImages = [
            'ceo' => $teamCeoImage ? Storage::url($teamCeoImage) : '/images/team/ceo.jpg',
            'cto' => $teamCtoImage ? Storage::url($teamCtoImage) : '/images/team/cto.jpg',
            'marketing' => $teamMarketingImage ? Storage::url($teamMarketingImage) : '/images/team/marketing.jpg',
            'support' => $teamSupportImage ? Storage::url($teamSupportImage) : '/images/team/support.jpg',
        ];

        return Inertia::render('Web/AboutUs', [
            'aboutUsImage' => $aboutUsImageUrl,
            'teamImages' => $teamImages,
        ]);
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

    /**
     * Show the privacy policy page.
     *
     * @return \Inertia\Response
     */
    public function privacy()
    {
        return Inertia::render('Web/Privacy');
    }

    /**
     * Show the contact us page.
     *
     * @return \Inertia\Response
     */
    public function contact()
    {
        return Inertia::render('Web/Contact');
    }
}
