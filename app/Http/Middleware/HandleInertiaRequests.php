<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
                'wishlistItems' => $request->user() ? 
                    $request->user()->wishlistItems()->get(['id', 'product_id'])->toArray() : [],
            ],
            'cartCount'     => $request->user() ?
                $request->user()->cartItems()->sum('quantity') : 0,
            'wishlistCount' => $request->user() ?
                $request->user()->wishlistItems()->count() : 0,
            'flash' => [
                'success'   => session('success'),
                'error'     => session('error'),
            ],
        ]);
    }
}
