<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     *
     * @param  string  $slug
     * 
     * @return \Inertia\Response
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', true)
            ->with(['category.parent', 'brand', 'images'])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', true)
            ->with(['category', 'brand', 'primaryImage'])
            ->take(4)
            ->get();

        $breadcrumbs = [
            ['title' => 'Home', 'href' => '/'],
            ['title' => 'Categories', 'href' => '/categories'],
            ['title' => $product->category->name, 'href' => route('categories.show', $product->category->slug)],
            ['title' => $product->name, 'href' => null],
        ];

        return Inertia::render('Web/Products/Show', [
            'product'           => $product,
            'relatedProducts'   => $relatedProducts,
            'breadcrumbs'       => $breadcrumbs,
        ]);
    }
}
