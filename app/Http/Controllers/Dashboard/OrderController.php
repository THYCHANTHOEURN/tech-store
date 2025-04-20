<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Order::query()->with('user');

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('uuid', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
        }

        // Handle order status filter
        if ($request->filled('order_status') && $request->order_status != 'all') {
            $query->where('status', $request->order_status);
        }

        // Handle payment status filter
        if ($request->filled('payment_status') && $request->payment_status != 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $orders = $query->paginate(10)->appends($request->query());

        // Convert enum values to arrays for dropdowns
        $orderStatuses = collect(OrderStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        $paymentStatuses = collect(PaymentStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        return Inertia::render('Dashboard/Orders/Index', [
            'orders'            => $orders,
            'filters'           => $request->only(['search', 'order_status', 'payment_status']),
            'orderStatuses'     => $orderStatuses,
            'paymentStatuses'   => $paymentStatuses,
        ]);
    }

    /**
     * Show the form for creating a new order.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $users      = User::select('id', 'name', 'email')->get();
        $products   = Product::where('status', true)
                        ->where('stock', '>', 0)
                        ->with('primaryImage')
                        ->get();

        $orderStatuses = collect(OrderStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        $paymentStatuses = collect(PaymentStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        return Inertia::render('Dashboard/Orders/Create', [
            'users'             => $users,
            'products'          => $products,
            'orderStatuses'     => $orderStatuses,
            'paymentStatuses'   => $paymentStatuses,
        ]);
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'               => 'required|exists:users,id',
            'shipping_address'      => 'required|string',
            'phone'                 => 'required|string|max:20',
            'payment_method'        => 'required|string',
            'status'                => 'required|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
            'payment_status'        => 'required|in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.price'         => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Create order
            $order = Order::create([
                'user_id'           => $validated['user_id'],
                'total_amount'      => 0, // Will calculate after adding items
                'status'            => $validated['status'],
                'shipping_address'  => $validated['shipping_address'],
                'phone'             => $validated['phone'],
                'payment_method'    => $validated['payment_method'],
                'payment_status'    => $validated['payment_status'],
            ]);

            // Add order items
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Validate stock availability
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                // Create order item
                $order->orderItems()->create([
                    'product_id'    => $item['product_id'],
                    'quantity'      => $item['quantity'],
                    'price'         => $item['price'],
                ]);

                // Update product stock
                $product->decrement('stock', $item['quantity']);

                // Calculate total
                $totalAmount += $item['price'] * $item['quantity'];
            }

            // Update order total
            $order->update([
                'total_amount' => $totalAmount,
            ]);

            // Notify other admins about the new order
            $admins = \App\Models\User::role([
                \App\Enums\RolesEnum::SUPER_ADMIN->value,
                \App\Enums\RolesEnum::ADMIN->value,
                \App\Enums\RolesEnum::MANAGER->value
            ])
            ->where('id', '!=', auth()->id()) // Exclude the current admin
            ->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewOrderNotification($order));
            }

            DB::commit();

            return redirect()->route('dashboard.orders.show', $order->uuid)
                ->with('success', 'Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Inertia\Response
     */
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product.primaryImage']);

        $orderStatuses = collect(OrderStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        $paymentStatuses = collect(PaymentStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        return Inertia::render('Dashboard/Orders/Show', [
            'order'             => $order,
            'orderStatuses'     => $orderStatuses,
            'paymentStatuses'   => $paymentStatuses,
        ]);
    }

    /**
     * Show the form for editing the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Inertia\Response
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'orderItems.product.primaryImage']);

        $users      = User::select('id', 'name', 'email')->get();
        $products   = Product::where('status', true)
                        ->with('primaryImage')
                        ->get();

        $orderStatuses = collect(OrderStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        $paymentStatuses = collect(PaymentStatus::cases())->map(fn ($status) => [
            'label' => ucfirst($status->value),
            'value' => $status->value
        ])->toArray();

        return Inertia::render('Dashboard/Orders/Edit', [
            'order'             => $order,
            'users'             => $users,
            'products'          => $products,
            'orderStatuses'     => $orderStatuses,
            'paymentStatuses'   => $paymentStatuses,
        ]);
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'shipping_address'      => 'required|string',
            'phone'                 => 'required|string|max:20',
            'payment_method'        => 'required|string',
            'status'                => 'required|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
            'payment_status'        => 'required|in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
        ]);

        DB::beginTransaction();

        try {
            // Update order
            $order->update($validated);

            DB::commit();

            return redirect()->route('dashboard.orders.show', $order->uuid)
                ->with('success', 'Order updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order)
    {
        DB::beginTransaction();

        try {
            // For each order item, restore product stock
            foreach ($order->orderItems as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Delete order items (will cascade due to relationship setup)
            $order->delete();

            DB::commit();

            return redirect()->route('dashboard.orders.index')
                ->with('success', 'Order deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }

    /**
     * Generate and display an invoice for the order.
     *
     * @param  \App\Models\Order  $order
     * @return \Inertia\Response
     */
    public function invoice(Order $order)
    {
        $order->load(['user', 'orderItems.product.primaryImage']);

        // Get company information from settings
        $companyName        = \App\Models\Setting::get('company_name', 'Tech Store');
        $companyAddress     = \App\Models\Setting::get('company_address') . ', ' .
                              \App\Models\Setting::get('company_state') . ', ' .
                              \App\Models\Setting::get('company_country');
        $companyPhone       = \App\Models\Setting::get('company_phone', '');
        $companyEmail       = \App\Models\Setting::get('company_email', '');

        return Inertia::render('Dashboard/Orders/Invoice', [
            'order'         => $order,
            'companyInfo'   => [
                'name'          => $companyName,
                'address'       => $companyAddress,
                'phone'         => $companyPhone,
                'email'         => $companyEmail,
            ]
        ]);
    }
}
