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
use App\Http\Controllers\Dashboard\NotificationController;
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
    // Product export, import, and template routes
    Route::get('products-export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products-import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products-template', [ProductController::class, 'template'])->name('products.template');

    // Brand management
    Route::resource('brands', BrandController::class);

    // Category management
    Route::resource('categories', CategoryController::class);
    Route::get('categories-export', [CategoryController::class, 'export'])->name('categories.export');

    // Banner management
    Route::resource('banners', BannerController::class);

    // Order management
    Route::resource('orders', OrderController::class);
    // Order Invoice
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    // Customer management
    Route::resource('customers', CustomerController::class);
    // Users management
    Route::resource('users', UserController::class);
    // Roles management
    Route::resource('roles', RoleController::class);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/list', [NotificationController::class, 'list'])->name('list');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
    });
});


/**
 * Super Admin and Admin routes
 *
 * These routes are only accessible to authenticated users with the super-admin or admin role.
 */
Route::middleware(['auth', 'verified', 'role:super_admin|admin'])->prefix('dashboard')->name('dashboard.')->group(function () {

    // Settings Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

    // Initialize settings route (run this once to set up default settings)
    Route::get('/settings/initialize', [SettingController::class, 'initialize'])->name('settings.initialize');
});
