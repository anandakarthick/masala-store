<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category:slug}', [ProductController::class, 'category'])->name('category.show');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Checkout
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('apply-coupon');
    Route::post('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('remove-coupon');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/payment/{order}', [CheckoutController::class, 'payment'])->name('payment');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// Order Tracking
Route::prefix('track')->name('tracking.')->group(function () {
    Route::get('/', [OrderTrackingController::class, 'index'])->name('index');
    Route::post('/', [OrderTrackingController::class, 'track'])->name('track');
    Route::get('/{order}', [OrderTrackingController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Customer Account Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [CustomerAccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CustomerAccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [CustomerAccountController::class, 'orderDetail'])->name('orders.show');
    Route::get('/profile', [CustomerAccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [CustomerAccountController::class, 'changePassword'])->name('password');
    Route::put('/change-password', [CustomerAccountController::class, 'updatePassword'])->name('password.update');
    Route::get('/wishlist', [CustomerAccountController::class, 'wishlist'])->name('wishlist');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', AdminCategoryController::class);
    Route::post('categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Products
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/{product}/update-stock', [AdminProductController::class, 'updateStock'])->name('products.update-stock');
    Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::post('products/images/{image}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');

    // Orders
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/update-payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::post('orders/{order}/update-delivery', [AdminOrderController::class, 'updateDelivery'])->name('orders.update-delivery');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'generateInvoice'])->name('orders.invoice');
    Route::post('orders/{order}/add-note', [AdminOrderController::class, 'addNote'])->name('orders.add-note');

    // Customers
    Route::resource('customers', AdminCustomerController::class);
    Route::post('customers/{customer}/toggle-status', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

    // Coupons
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::post('coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::get('/sales', [AdminReportController::class, 'sales'])->name('sales');
        Route::get('/products', [AdminReportController::class, 'products'])->name('products');
        Route::get('/categories', [AdminReportController::class, 'categories'])->name('categories');
        Route::get('/stock', [AdminReportController::class, 'stock'])->name('stock');
        Route::get('/customers', [AdminReportController::class, 'customers'])->name('customers');
        Route::get('/export', [AdminReportController::class, 'export'])->name('export');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
        Route::put('/', [AdminSettingsController::class, 'update'])->name('update');
        
        // Banners
        Route::get('/banners', [AdminSettingsController::class, 'banners'])->name('banners');
        Route::post('/banners', [AdminSettingsController::class, 'storeBanner'])->name('banners.store');
        Route::put('/banners/{banner}', [AdminSettingsController::class, 'updateBanner'])->name('banners.update');
        Route::delete('/banners/{banner}', [AdminSettingsController::class, 'destroyBanner'])->name('banners.destroy');
        
        // Delivery Partners
        Route::get('/delivery-partners', [AdminSettingsController::class, 'deliveryPartners'])->name('delivery-partners');
        Route::post('/delivery-partners', [AdminSettingsController::class, 'storeDeliveryPartner'])->name('delivery-partners.store');
        Route::put('/delivery-partners/{partner}', [AdminSettingsController::class, 'updateDeliveryPartner'])->name('delivery-partners.update');
        Route::delete('/delivery-partners/{partner}', [AdminSettingsController::class, 'destroyDeliveryPartner'])->name('delivery-partners.destroy');
    });
});
