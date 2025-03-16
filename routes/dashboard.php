<?php

use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Dashboard routes
 *
 * These routes are only accessible to authenticated users with the admin, super-admin, manager, or staff role.
 */
Route::middleware(['auth', 'verified', 'admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Dashboard home
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    // Product management
    Route::resource('products', ProductController::class);
    // Brand management
    Route::resource('brands', BrandController::class);
    // Category management
    Route::resource('categories', CategoryController::class);
    // Banner management
    Route::resource('banners', BannerController::class);
    // Order management
    Route::resource('orders', OrderController::class);
    // Customer management
    Route::resource('customers', CustomerController::class);
    // Users management
    Route::resource('users', UserController::class);
    // Roles management
    Route::resource('roles', RoleController::class);
});
