<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
        $featuredProducts = Product::with(['category', 'brand', 'productImages'])
            ->where('featured', true)
            ->where('status', true)
            ->take(8)
            ->get();

        $featuredCategories = Category::where('status', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->take(6)
            ->get();

        $newArrivals = Product::with(['category', 'brand', 'productImages'])
            ->where('status', true)
            ->latest()
            ->take(8)
            ->get();

        $bestSellers = Product::with(['category', 'brand', 'productImages'])
            ->whereHas('orderItems', function($query) {
                $query->havingRaw('COUNT(*) > 0');
            })
            ->take(8)
            ->get();

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'featuredCategories' => $featuredCategories,
            'newArrivals' => $newArrivals,
            'bestSellers' => $bestSellers,
            'sliderImages' => [
                [
                    'title' => 'New Arrivals',
                    'description' => 'Check out our latest products',
                    'image' => '/images/slider/slide1.jpg',
                    'link' => route('categories.index', ['sort' => 'newest'])
                ],
                [
                    'title' => 'Special Offers',
                    'description' => 'Up to 50% off on selected items',
                    'image' => '/images/slider/slide2.jpg',
                    'link' => route('categories.index', ['on_sale' => true])
                ],
            ]
        ]);
    }
}
