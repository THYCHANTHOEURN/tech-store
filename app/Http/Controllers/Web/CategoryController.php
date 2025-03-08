<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display list of products in a category
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Product::query()->where('status', true);

        // Handle different filters
        if ($request->featured) {
            $query->where('featured', true);
            $title = 'Featured Products';
        } elseif ($request->sort === 'newest') {
            $query->latest();
            $title = 'New Arrivals';
        } elseif ($request->sort === 'best-selling') {
            $query->withCount('orderItems')
                  ->having('order_items_count', '>', 0)
                  ->orderByDesc('order_items_count');
            $title = 'Best Sellers';
        } else {
            // Default view shows categories with their products
            return $this->showCategories();
        }

        $products = $query->with(['category', 'brand', 'productImages'])
                        ->paginate(12);

        $breadcrumbs = [
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Categories', 'href' => '/categories'],
            ['title' => $title, 'href' => null],
        ];

        return Inertia::render('Web/Categories/Index', [
            'products'      => $products,
            'title'         => $title,
            'breadcrumbs'   => $breadcrumbs,
            'filters'       => $request->only(['featured', 'sort']),
        ]);
    }

    /**
     * Method to show all categories
     *
     * @return \Inertia\Response
     */
    private function showCategories()
    {
        $mainCategories = Category::where('status', true)
            ->whereNull('parent_id')
            ->with(['products' => function ($query) {
                $query->where('status', true)
                    ->latest()
                    ->take(8);
            }])
            ->get();

        $breadcrumbs = [
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Categories', 'href' => '/categories'],
        ];

        return Inertia::render('Web/Categories/Index', [
            'mainCategories'    => $mainCategories,
            'breadcrumbs'       => $breadcrumbs,
            'filters'           => [],
        ]);
    }

    /**
     * Display a category and its products
     *
     * @param  string  $slug
     * 
     * @return \Inertia\Response
     */
    public function show($slug)
    {
        $category   = Category::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $query      = $category->products()->where('status', true);
        $products   = $query->with(['category', 'brand', 'productImages'])
                    ->paginate(12);

        $breadcrumbs = [
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Categories', 'href' => '/categories'],
        ];

        // Add parent category to breadcrumb if exists
        if ($category->parent) {
            $breadcrumbs[] = [
                'title' => $category->parent->name,
                'href'  => route('categories.show', $category->parent->slug)
            ];
        }

        // Add current category to breadcrumb
        $breadcrumbs[] = [
            'title' => $category->name,
            'href'  => null
        ];

        // Get child categories if this is a parent category
        $childCategories = $category->children()
                                   ->where('status', true)
                                   ->with(['products' => function ($query) {
                                       $query->where('status', true)
                                            ->latest()
                                            ->take(8);
                                   }])
                                   ->get();

        return Inertia::render('Web/Categories/Show', [
            'category'          => $category,
            'products'          => $products,
            'childCategories'   => $childCategories,
            'breadcrumbs'       => $breadcrumbs,
        ]);
    }
}
