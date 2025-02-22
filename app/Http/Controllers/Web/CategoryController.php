<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display Listing of Products by Category
     *
     * @return \Inertia\Response
     */
    public function index()
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

        return Inertia::render('Categories/Index', [
            'mainCategories' => $mainCategories,
            'breadcrumbs'    => $breadcrumbs
        ]);
    }
}
