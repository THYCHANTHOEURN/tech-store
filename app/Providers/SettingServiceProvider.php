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
        // Example of how to share settings with all views if needed
        Inertia::share('siteSettings', function () {
            return [
                'logoUrl' => Setting::get('site_logo') ? Storage::url(Setting::get('site_logo')) : null,
                'siteName' => Setting::get('site_name', config('app.name')),
                // Add more global settings as needed
            ];
        });
    }
}
