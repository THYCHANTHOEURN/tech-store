<?php

namespace App\Providers;

use App\Exports\CustomerExport;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Policies\BannerPolicy;
use App\Policies\BrandPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\MessageThreadPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
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

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Banner::class           => BannerPolicy::class,
        Brand::class            => BrandPolicy::class,
        Category::class         => CategoryPolicy::class,
        Product::class          => ProductPolicy::class,
        User::class             => UserPolicy::class,
        Setting::class          => SettingPolicy::class,
        Role::class             => RolePolicy::class,
        Order::class            => OrderPolicy::class,
        MessageThread::class    => MessageThreadPolicy::class,
    ];


}
