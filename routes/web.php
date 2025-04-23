<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DataController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\WishlistController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\MessageController;
use Illuminate\Support\Facades\Route;

/**
 * Public routes
 */
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/home', [HomeController::class, 'index'])->name('index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');

/**
 * Static pages
 */
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

// Contact form submission
Route::post('/contact', [MessageController::class, 'store'])->name('contact.store');

/**
 * Customer routes
 */
Route::middleware(['auth', 'customer'])->group(function () {
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Order tracking routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{uuid}', [OrderController::class, 'show'])->name('orders.show');
});

/**
 * Checkout routes
 */
Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmation/{uuid}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // Customer message routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{thread:uuid}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{thread:uuid}/reply', [MessageController::class, 'reply'])->name('messages.reply');
});

/**
 * Authentication routes
 */
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Messages routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{thread:uuid}', [MessageController::class, 'show'])->name('show');
        Route::post('/{thread:uuid}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::get('/unread/count', [MessageController::class, 'unreadCount'])->name('unread.count');
    });
});

/**
 * Data routes
 */
Route::group(['prefix' => 'data', 'as' => 'data.'], function () {
    Route::get('categories', [DataController::class, 'categories'])->name('categories');
});

/**
 * Include other routes
 * These routes are loaded in the order they are included.
 */
require __DIR__.'/dashboard.php';
require __DIR__.'/auth.php';
