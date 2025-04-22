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
use App\Http\Controllers\Dashboard\MessageController;
use Illuminate\Support\Facades\Route;

/**
 * Dashboard routes
 *
 * These routes are only accessible to authenticated users with the admin, super-admin, manager, or staff role.
 */
Route::middleware(['auth', 'verified', 'admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Dashboard home
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Product management, including export, import, and template download
    Route::resource('products', ProductController::class);
    Route::get('products-export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products-import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products-template', [ProductController::class, 'template'])->name('products.template');

    // Brand management, including export
    Route::resource('brands', BrandController::class);
    Route::get('brands-export', [BrandController::class, 'export'])->name('brands.export');

    // Category management, including export
    Route::resource('categories', CategoryController::class);
    Route::get('categories-export', [CategoryController::class, 'export'])->name('categories.export');

    // Banner management, including export
    Route::resource('banners', BannerController::class);
    Route::get('banners-export', [BannerController::class, 'export'])->name('banners.export');

    // Order management, invoice generation, and export
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders-export', [OrderController::class, 'export'])->name('orders.export');

    // Customer management
    Route::resource('customers', CustomerController::class);
    Route::get('customers-export', [CustomerController::class, 'export'])->name('customers.export');

    // Users management with export
    Route::resource('users', UserController::class);
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');

    // Roles management
    Route::resource('roles', RoleController::class);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/list', [NotificationController::class, 'list'])->name('list');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');
    });

    /**
     * Message management routes
     */
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{thread:uuid}', [MessageController::class, 'show'])->name('show');
        Route::post('/{thread:uuid}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::post('/{thread:uuid}/close', [MessageController::class, 'close'])->name('close');
        Route::post('/{thread:uuid}/reopen', [MessageController::class, 'reopen'])->name('reopen');
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
