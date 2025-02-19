<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Home', [
            'featuredProducts' => Product::with(['category', 'brand', 'productImages'])
                ->where('featured', true)
                ->where('status', true)
                ->take(8)
                ->get(),

            'featuredCategories' => Category::where('status', true)
                ->whereNull('parent_id')
                ->withCount('products')
                ->take(6)
                ->get(),

            'newArrivals' => Product::with(['category', 'brand', 'productImages'])
                ->where('status', true)
                ->latest()
                ->take(8)
                ->get(),

            'bestSellers' => Product::with(['category', 'brand', 'productImages'])
                ->whereHas('orderItems', function($query) {
                    $query->havingRaw('COUNT(*) > 0');
                })
                ->take(8)
                ->get(),

            'sliderImages' => Banner::active()
                ->position(Banner::POSITION_SLIDER)
                ->orderBy('position')
                ->get(),

            'sideBanners' => Banner::active()
                ->position(Banner::POSITION_SIDE)
                ->orderBy('position')
                ->get(),

            'promoBanners' => Banner::active()
                ->position(Banner::POSITION_PROMO)
                ->orderBy('position')
                ->get(),
        ]);
    }
}
