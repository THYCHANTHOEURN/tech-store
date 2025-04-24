<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Get sales data for chart - last 7 days
        $salesData = $this->getSalesChartData();

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

        // dd($salesData);
        return Inertia::render('Dashboard/Index', [
            'stats'             => $stats,
            'recentOrders'      => $recentOrders,
            'popularProducts'   => $popularProducts,
            'salesChart'        => $salesData
        ]);
    }

    /**
     * Get sales data for chart display
     *
     * @return array
     */
    private function getSalesChartData()
    {
        // Get dates for the last 7 days
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        // Get sales data
        $salesByDate = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Prepare data for the chart
        $chartLabels = $dates->map(function ($date) {
            return Carbon::parse($date)->format('M d');
        })->toArray();

        $chartData = $dates->map(function ($date) use ($salesByDate) {
            return isset($salesByDate[$date]) ? round($salesByDate[$date], 2) : 0;
        })->toArray();

        return [
            'labels' => $chartLabels,
            'data' => $chartData
        ];
    }
}
