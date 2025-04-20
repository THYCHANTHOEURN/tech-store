<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share settings with all Inertia views
        Inertia::share('siteSettings', function () {
            return [
                'logoUrl'       => Setting::get('site_logo') ? Storage::url(Setting::get('site_logo')) : null,
                'siteName'      => Setting::get('site_name', config('app.name')),
                'companyInfo'   => [
                    'name'          => Setting::get('company_name', 'Tech Store'),
                    'address'       => Setting::get('company_address', '123 Tech Street, Innovation City'),
                    'state'         => Setting::get('company_state', 'State 12345'),
                    'country'       => Setting::get('company_country', 'Country'),
                    'fullAddress'   => Setting::get('company_address', '123 Tech Street, Innovation City') . ', ' .
                                       Setting::get('company_state', 'State 12345') . ', ' .
                                       Setting::get('company_country', 'Country'),
                    'phone'         => Setting::get('company_phone', '+855 12 345 678'),
                    'phoneSecondary'=> Setting::get('company_phone_secondary', '+855 98 765 432'),
                    'email'         => Setting::get('company_email', 'info@techstore.com'),
                    'emailSupport'  => Setting::get('company_email_support', 'support@techstore.com'),
                    'hours'         => Setting::get('company_hours', 'Monday - Friday: 9:00 AM - 6:00 PM; Saturday: 10:00 AM - 4:00 PM; Sunday: Closed'),
                    'social'        => [
                        'facebook'      => Setting::get('company_facebook', 'https://www.facebook.com/'),
                        'instagram'     => Setting::get('company_instagram', 'https://www.instagram.com/'),
                        'twitter'       => Setting::get('company_twitter', 'https://twitter.com/'),
                        'youtube'       => Setting::get('company_youtube', 'https://www.youtube.com/'),
                        'tiktok'        => Setting::get('company_tiktok', 'https://www.tiktok.com/'),
                        'telegram'      => Setting::get('company_telegram', 'https://t.me/'),
                    ]
                ]
            ];
        });
    }
}
