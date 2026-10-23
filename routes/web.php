<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Testing\SimulatePaymentController;
use App\Http\Controllers\Webhooks\XenditWebhookController;
use Illuminate\Support\Facades\Route;

// Katalog & Halaman Utama
Route::get('/', [CatalogController::class, 'index'])->name('home');

// Checkout Domain
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.place');

// Order Lifecycle Domain
Route::get('/order/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/order/{orderNumber}/status', [OrderController::class, 'status'])->name('orders.status');

// Payment Webhook & Testing Simulation
Route::post('/order/{orderNumber}/simulate-pay', SimulatePaymentController::class)->name('orders.simulate-pay');
Route::post('/api/xendit/webhook', XenditWebhookController::class)->name('xendit.webhook');

// Admin Domain Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Admin Panel Routes
    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Products Catalog Management
        Route::resource('products', ProductController::class)->except(['show']);

        // Orders Management
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Site Settings Management (Dynamic Content)
        Route::get('/settings', [SiteSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
    });
});
