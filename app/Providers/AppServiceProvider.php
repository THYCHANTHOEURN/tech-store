<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Add user information to views for JavaScript access
        if (Auth::check()) {
            View::share('user', Auth::user());

            // Add user meta tags for JS access
            $user = Auth::user();
            $metaTags = [
                '<meta name="user-id" content="' . $user->id . '">',
                '<meta name="user-name" content="' . $user->name . '">',
                '<meta name="user-email" content="' . $user->email . '">',
            ];

            Inertia::share('userMetaTags', $metaTags);
        }

        // Share data with Inertia
        Inertia::share([
            'unreadMessagesCount' => function () {
                if (Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('super_admin'))) {
                    return Message::whereHas('thread', function($query) {
                            $query->where('status', 'active');
                        })
                        ->where('user_id', '!=', null)
                        ->where('is_read', false)
                        ->count();
                }
                return 0;
            },
        ]);

        // Set the application name from settings if the Setting table exists
        try {
            if (Schema::hasTable('settings')) {
                $siteName = Setting::where('key', 'site_name')->first();
                if ($siteName) {
                    Config::set('app.name', $siteName->value);
                }
            }
        } catch (\Exception $e) {
            // Table might not exist during migration
        }
    }
}
