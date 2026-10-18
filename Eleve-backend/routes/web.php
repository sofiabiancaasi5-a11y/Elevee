<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\NotificationFeedController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

// Redirect root to admin dashboard or login
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware([EnsureAdmin::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Categories Management (Full CRUD)
        Route::resource('/categories', CategoryController::class, ['as' => 'admin']);

        // Products Management (Full CRUD)
        Route::resource('/products', ProductController::class, ['as' => 'admin'])->except(['show']);

        // Product Variant Matrix CRUD
        Route::post('/products/{product}/variants', [ProductController::class, 'addVariant'])->name('admin.products.variants.store');
        Route::put('/variants/{variant}', [ProductController::class, 'updateVariant'])->name('admin.products.variants.update');
        Route::delete('/variants/{variant}', [ProductController::class, 'deleteVariant'])->name('admin.products.variants.destroy');

        // Inventory Matrix
        Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');
        Route::post('/inventory/update-stock', [InventoryController::class, 'updateStock'])->name('admin.inventory.update');

        // Orders Management & Packing Slip
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        Route::post('/orders/{id}/address', [OrderController::class, 'updateAddress'])->name('admin.orders.update-address');
        Route::get('/orders/{id}/packing-slip', [OrderController::class, 'packingSlip'])->name('admin.orders.packing-slip');

        // Customers Directory (Full CRUD)
        Route::resource('/customers', CustomerController::class, ['as' => 'admin']);

        // Reports & Analytics
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');

        // Atelier Real-Time Feed & Notification Management
        Route::prefix('notifications')->name('admin.notifications.')->group(function () {
            Route::get('/', [NotificationFeedController::class, 'index'])->name('index');
            Route::get('/feed', [NotificationFeedController::class, 'feed'])->name('feed');
            Route::post('/mark-all-read', [NotificationFeedController::class, 'markAllRead'])->name('mark-all-read');
            Route::post('/{id}/mark-read', [NotificationFeedController::class, 'markRead'])->name('mark-read');
            Route::delete('/{id}', [NotificationFeedController::class, 'destroy'])->name('destroy');
            Route::post('/clear-read', [NotificationFeedController::class, 'clearRead'])->name('clear-read');
        });
    });
});
