<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CartItem;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Show checkout page (shipping information)
     *
     * @return \Inertia\Response |\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Get cart items
        $cartItems = auth()->user()->cartItems()
            ->with(['product.primaryImage', 'product.category'])
            ->get();

        // Check if cart has items
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add products before checkout.');
        }

        // Calculate cart total
        $cartTotal = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->sale_price ?? $item->product->price);
        });

        return Inertia::render('Web/Checkout/Index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'user'      => auth()->user()
        ]);
    }

    /**
     * Show payment page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function payment(Request $request)
    {
        // Validate shipping information
        $validated = $request->validate([
            'shipping_address'  => ['required', 'string', 'max:255'],
            'phone'             => ['required', 'string', 'max:20'],
        ]);

        // Store shipping details in session
        session(['checkout.shipping_address'    => $validated['shipping_address']]);
        session(['checkout.phone'               => $validated['phone']]);

        // Get cart items
        $cartItems = auth()->user()->cartItems()
            ->with(['product.primaryImage'])
            ->get();

        // Check if cart has items
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add products before checkout.');
        }

        // Calculate cart total
        $cartTotal = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->sale_price ?? $item->product->price);
        });

        return Inertia::render('Web/Checkout/Payment', [
            'cartItems'             => $cartItems,
            'cartTotal'             => $cartTotal,
            'shipping_address'      => session('checkout.shipping_address'),
        ]);
    }

    /**
     * Process the order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        // Validate payment information
        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:cash,card,bank_transfer'],
        ]);

        // Get shipping details and phone from session
        $shippingAddress    = session('checkout.shipping_address');
        $phone              = session('checkout.phone');

        if (!$shippingAddress) {
            return redirect()->route('checkout.index')
                ->with('error', 'Shipping information missing. Please fill in your shipping details.');
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Get cart items
            $cartItems = auth()->user()->cartItems()
                ->with(['product'])
                ->get();

            // Check if cart has items
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty. Please add products before checkout.');
            }

            // Calculate cart total
            $totalAmount = $cartItems->sum(function ($item) {
                return $item->quantity * ($item->product->sale_price ?? $item->product->price);
            });

            // Create order
            $order = Order::create([
                'user_id'           => auth()->id(),
                'total_amount'      => $totalAmount,
                'status'            => OrderStatus::PENDING->value,
                'shipping_address'  => $shippingAddress,
                'phone'             => $phone,
                'payment_method'    => $validated['payment_method'],
                'payment_status'    => PaymentStatus::PENDING->value,
            ]);

            // Create order items and update product stock
            foreach ($cartItems as $cartItem) {
                $product    = $cartItem->product;
                $price      = $product->sale_price ?? $product->price;

                // Validate stock availability
                if ($product->stock < $cartItem->quantity) {
                    throw new \Exception("Not enough stock for {$product->name}. Available: {$product->stock}");
                }

                // Create order item
                $order->orderItems()->create([
                    'product_id'    => $cartItem->product_id,
                    'quantity'      => $cartItem->quantity,
                    'price'         => $price,
                ]);

                // Update product stock
                $product->decrement('stock', $cartItem->quantity);
            }

            // Clear cart
            auth()->user()->cartItems()->delete();

            // Clear checkout session
            session()->forget('checkout');

            // Notify admins about the new order
            $admins = \App\Models\User::role([
                \App\Enums\RolesEnum::SUPER_ADMIN->value,
                \App\Enums\RolesEnum::ADMIN->value,
                \App\Enums\RolesEnum::MANAGER->value
            ])->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewOrderNotification($order));
            }

            // Commit transaction
            DB::commit();

            // Redirect to confirmation page
            return redirect()->route('checkout.confirmation', $order->uuid);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error processing your order: ' . $e->getMessage());
        }
    }

    /**
     * Show order confirmation
     *
     * @param  string  $uuid
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function confirmation($uuid)
    {
        $order = Order::where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->with(['orderItems.product.primaryImage'])
            ->first();

        if (!$order) {
            return redirect()->route('index')
                ->with('error', 'Order not found.');
        }

        return Inertia::render('Web/Checkout/Confirmation', [
            'order' => $order
        ]);
    }
}
