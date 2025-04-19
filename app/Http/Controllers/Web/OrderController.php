<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the customer's orders.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Web/Orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Display the specified order.
     *
     * @param  string  $uuid
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function show($uuid)
    {
        $order = Order::where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->with(['orderItems.product.primaryImage'])
            ->first();

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found.');
        }

        return Inertia::render('Web/Orders/Show', [
            'order' => $order
        ]);
    }
}
