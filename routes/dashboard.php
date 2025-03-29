<?php

use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SettingController;
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


// Admin Dashboard Routes
Route::middleware(['auth', 'verified', 'role:super_admin|admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // ... existing dashboard routes ...

    // Settings Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

    // Initialize settings route (run this once to set up default settings)
    Route::get('/settings/initialize', [SettingController::class, 'initialize'])->name('settings.initialize');
});
