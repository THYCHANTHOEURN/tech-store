<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart page
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $cartItems = auth()->user()->cartItems()
            ->with(['product.primaryImage', 'product.category'])
            ->get();

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'cartCount' => $cartItems->sum('quantity')
        ]);
    }

    /**
     * Add a product to the cart
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id'    => ['required', 'exists:products,id'],
                'quantity'      => ['required', 'integer', 'min:1']
            ]);

            $product = Product::findOrFail($validated['product_id']);
            
            if ($product->stock < $validated['quantity']) {
                return back()->with('error', 'Not enough stock available.');
            }

            $existingItem = CartItem::where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $validated['quantity'];
                if ($product->stock < $newQuantity) {
                    return back()->with('error', 'Cannot add more of this item (stock limit reached).');
                }
                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'user_id'       => auth()->id(),
                    'product_id'    => $validated['product_id'],
                    'quantity'      => $validated['quantity']
                ]);
            }

            $cartCount = auth()->user()->cartItems()->sum('quantity');

            return back()->with([
                'success'   => 'Product added to cart successfully.',
                'cartCount' => $cartCount
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add product to cart. Please try again.');
        }
    }
}
