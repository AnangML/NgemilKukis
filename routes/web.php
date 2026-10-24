<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GreetingCardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Auth\CustomerEmailAuthController;
use App\Http\Controllers\Auth\CustomerGoogleAuthController;
use App\Http\Controllers\Auth\CustomerPhoneAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Testing\SimulatePaymentController;
use App\Http\Controllers\Webhooks\XenditWebhookController;
use Illuminate\Support\Facades\Route;

// Katalog & Halaman Utama
Route::get('/', [CatalogController::class, 'index'])->name('home');

// Customer Authentication Domain (Google, Email, Phone OTP)
Route::post('/auth/register', [CustomerEmailAuthController::class, 'register'])->name('customer.register');
Route::post('/auth/login', [CustomerEmailAuthController::class, 'login'])->name('customer.login');
Route::post('/auth/logout', [CustomerEmailAuthController::class, 'logout'])->name('customer.logout');

Route::get('/auth/google/redirect', [CustomerGoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [CustomerGoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/auth/google/simulate', [CustomerGoogleAuthController::class, 'simulate'])->name('auth.google.simulate');

Route::post('/auth/phone/send-otp', [CustomerPhoneAuthController::class, 'sendOtp'])->name('auth.phone.send-otp');
Route::post('/auth/phone/verify-otp', [CustomerPhoneAuthController::class, 'verifyOtp'])->name('auth.phone.verify-otp');

// Customer Account & Address Book Domain
Route::middleware('auth')->prefix('akun')->name('customer.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('account');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('account.update');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{address}', [AccountController::class, 'deleteAddress'])->name('addresses.destroy');
});

// Customer Order Tracking & History Domain
Route::get('/pesanan', [CustomerOrderController::class, 'index'])->name('customer.orders');

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
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');
        Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::resource('products', ProductController::class)->except(['show']);

        // Greeting Cards Management
        Route::patch('greeting-cards/{greetingCard}/toggle', [GreetingCardController::class, 'toggleStatus'])->name('greeting-cards.toggle');
        Route::resource('greeting-cards', GreetingCardController::class)->except(['show'])->parameters(['greeting-cards' => 'greetingCard']);

        // Orders Management
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Site Settings Management (Dynamic Content)
        Route::get('/settings', [SiteSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
    });
});
