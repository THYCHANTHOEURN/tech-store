<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard overview.
     *
     * @param Request $request
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
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

        // Get sales data for chart with optional date range parameters
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');
        $salesData  = $this->getSalesChartData($startDate, $endDate);

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
     * @param string|null $startDate Optional start date in Y-m-d format
     * @param string|null $endDate Optional end date in Y-m-d format
     * @return array
     */
    private function getSalesChartData($startDate = null, $endDate = null)
    {
        // Set default date range (last 7 days) if none provided
        $startDate  = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(6);
        $endDate    = $endDate ? Carbon::parse($endDate) : Carbon::now();

        // Ensure end date is not before start date
        if ($endDate->isBefore($startDate)) {
            $temp       = $startDate;
            $startDate  = $endDate;
            $endDate    = $temp;
        }

        // Get all dates in the range
        $dates          = collect();
        $currentDate    = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Get sales data
        $salesByDate = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
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

        // Format date range for display
        $formattedStartDate = $startDate->format('M d, Y');
        $formattedEndDate   = $endDate->format('M d, Y');

        return [
            'labels'        => $chartLabels,
            'data'          => $chartData,
            'dateRange'     => [
                'start' => $formattedStartDate,
                'end'   => $formattedEndDate
            ]
        ];
    }
}
