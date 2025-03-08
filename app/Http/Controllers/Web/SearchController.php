<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->query('searchTerm');

        $products = Product::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('sku', 'like', "%{$searchTerm}%");
                });
            })
            ->with(['brand', 'category'])
            ->where('status', true)
            ->paginate(12);

        return Inertia::render('Search/Index', [
            'products'      => $products,
            'searchTerm'    => $searchTerm
        ]);
    }
}
