<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard overview.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Count statistics
        $stats = [
            'products'  => Product::count(),
            'orders'    => Order::count(),
            'customers' => User::role('customer')->count(),
            'revenue'   => Order::where('status', 'completed')->sum('total_amount')
        ];

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id'            => $order->id,
                    'uuid'          => $order->uuid,
                    'customer'      => $order->user->name,
                    'status'        => $order->status,
                    'total_amount'  => $order->total_amount,
                    'created_at'    => $order->created_at->format('Y-m-d')
                ];
            });

        // Popular products
        $popularProducts = Product::withCount('orderItems')
            ->having('order_items_count', '>', 0)
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id'                => $product->id,
                    'uuid'              => $product->uuid,
                    'name'              => $product->name,
                    'primary_image_url' => $product->primary_image_url,
                    'price'             => $product->price,
                    'sold'              => $product->order_items_count,
                    'stock'             => $product->stock
                ];
            });

        return Inertia::render('Dashboard/Index', [
            'stats'             => $stats,
            'recentOrders'      => $recentOrders,
            'popularProducts'   => $popularProducts
        ]);
    }
}
