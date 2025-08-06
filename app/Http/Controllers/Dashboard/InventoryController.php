<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display inventory dashboard
     *
     * @param Request $request
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view', Product::class);

        $settings = Setting::getInventorySettings();

        // Get inventory statistics
        $stats = [
            'total_products'    => Product::count(),
            'out_of_stock'      => Product::outOfStock()->count(),
            'critical_stock'    => Product::criticalStock()->count(),
            'low_stock'         => Product::lowStock()->count(),
            'overstock'         => Product::overstock()->count(),
            'total_stock_value' => Product::sum(DB::raw('stock * price')),
        ];

        // Get products based on filter
        $filter = $request->get('filter', 'all');
        $query  = Product::with(['category', 'brand', 'primaryImage']);

        switch ($filter) {
            case 'out_of_stock':
                $query->outOfStock();
                break;
            case 'critical_stock':
                $query->criticalStock();
                break;
            case 'low_stock':
                $query->lowStock();
                break;
            case 'overstock':
                $query->overstock();
                break;
            default:
                // Show all products
                break;
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('stock', 'asc')->paginate(15)->appends($request->query());

        return Inertia::render('Dashboard/Inventory/Index', [
            'stats'     => $stats,
            'products'  => $products,
            'settings'  => $settings,
            'filters'   => $request->only(['filter', 'search']),
        ]);
    }

    /**
     * Update inventory settings
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $this->authorize('update', Product::class);

        $validated = $request->validate([
            'low_stock_threshold'       => 'required|integer|min:1',
            'critical_stock_threshold'  => 'required|integer|min:1',
            'overstock_threshold'       => 'required|integer|min:100',
            'enable_stock_alerts'       => 'required|boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Inventory settings updated successfully.');
    }

    /**
     * Bulk update stock
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkUpdateStock(Request $request)
    {
        $this->authorize('bulkUpdate', Product::class);

        $validated = $request->validate([
            'updates'               => 'required|array',
            'updates.*.product_id'  => 'required|exists:products,id',
            'updates.*.stock'       => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['updates'] as $update) {
                Product::where('id', $update['product_id'])
                    ->update(['stock' => $update['stock']]);
            }

            DB::commit();

            return back()->with('success', 'Stock updated successfully for ' . count($validated['updates']) . ' products.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating stock: ' . $e->getMessage());
        }
    }

    /**
     * Get inventory alerts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function alerts()
    {
        $this->authorize('alerts', Product::class);

        $settings = Setting::getInventorySettings();

        if (!$settings['enable_stock_alerts']) {
            return response()->json(['alerts' => []]);
        }

        $alerts = [
            'out_of_stock' => Product::outOfStock()
                ->with(['category', 'primaryImage'])
                ->get()
                ->map(function ($product) {
                    return [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'stock'    => $product->stock,
                        'category' => $product->category->name,
                        'image'    => $product->primary_image_url,
                        'type'     => 'out_of_stock',
                        'severity' => 'critical',
                        'message'  => 'Product is out of stock',
                    ];
                }),
            'critical_stock' => Product::criticalStock()
                ->with(['category', 'primaryImage'])
                ->get()
                ->map(function ($product) {
                    return [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'stock'    => $product->stock,
                        'category' => $product->category->name,
                        'image'    => $product->primary_image_url,
                        'type'     => 'critical_stock',
                        'severity' => 'high',
                        'message'  => 'Product stock is critically low',
                    ];
                }),
            'low_stock' => Product::lowStock()
                ->with(['category', 'primaryImage'])
                ->get()
                ->map(function ($product) {
                    return [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'stock'    => $product->stock,
                        'category' => $product->category->name,
                        'image'    => $product->primary_image_url,
                        'type'     => 'low_stock',
                        'severity' => 'medium',
                        'message'  => 'Product stock is running low',
                    ];
                }),
            'overstock' => Product::overstock()
                ->with(['category', 'primaryImage'])
                ->get()
                ->map(function ($product) {
                    return [
                        'id'       => $product->id,
                        'name'     => $product->name,
                        'sku'      => $product->sku,
                        'stock'    => $product->stock,
                        'category' => $product->category->name,
                        'image'    => $product->primary_image_url,
                        'type'     => 'overstock',
                        'severity' => 'low',
                        'message'  => 'Product has excess stock',
                    ];
                }),
        ];

        return response()->json(['alerts' => $alerts]);
    }
}
