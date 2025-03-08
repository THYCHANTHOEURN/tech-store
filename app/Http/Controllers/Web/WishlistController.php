<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $wishlistItems = auth()->user()->wishlistItems()
            ->with(['product.primaryImage', 'product.category'])
            ->get();

        return Inertia::render('Web/Wishlist/Index', [
            'wishlistItems' => $wishlistItems,
            'wishlistCount' => $wishlistItems->count()
        ]);
    }

    /**
     * Add a product to the wishlist or restore it if trashed
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => ['required', 'exists:products,id'],
            ]);

            $product = Product::findOrFail($validated['product_id']);

            $existingItem = WishlistItem::withTrashed()
                ->where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            // If the product is already in the wishlist, restore it
            // If the product is not in the wishlist, add it
            if ($existingItem && $existingItem->trashed()) {
                $existingItem->restore();
            } elseif (!$existingItem) {
                WishlistItem::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $validated['product_id'],
                ]);
            }

            $wishlistCount = auth()->user()->wishlistItems()->count();

            return back()->with([
                'success'       => 'Product added to wishlist successfully.',
                'wishlistCount' => $wishlistCount
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add product to wishlist. Please try again.');
        }
    }

    /**
     * Remove a product from the wishlist
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $wishlistItem = WishlistItem::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $wishlistItem->delete();

            $wishlistCount = auth()->user()->wishlistItems()->count();

            return redirect()->route('wishlist.index')->with([
                'success'       => 'Product removed from wishlist successfully.',
                'wishlistCount' => $wishlistCount
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove product from wishlist. Please try again.');
        }
    }

    /**
     * Toggle a product in the wishlist (add if not exists, remove if exists, restore if trashed)
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => ['required', 'exists:products,id'],
            ]);

            // Check for existing item including trashed ones
            $existingItem = WishlistItem::withTrashed()
                ->where('user_id', auth()->id())
                ->where('product_id', $validated['product_id'])
                ->first();

            $message = '';

            if ($existingItem) {
                if ($existingItem->trashed()) {
                    // If item exists but is trashed, restore it
                    $existingItem->restore();
                    $message = 'Product added back to wishlist successfully.';
                } else {
                    // If item exists and is not trashed, delete it
                    $existingItem->delete();
                    $message = 'Product removed from wishlist successfully.';
                }
            } else {
                // If item doesn't exist at all, create it
                WishlistItem::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $validated['product_id'],
                ]);
                $message = 'Product added to wishlist successfully.';
            }

            $wishlistCount = auth()->user()->wishlistItems()->count();

            return back()->with([
                'success'       => $message,
                'wishlistCount' => $wishlistCount
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update wishlist. Please try again.');
        }
    }
}
