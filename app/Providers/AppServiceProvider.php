<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;

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
    }
}
