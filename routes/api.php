<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\SupportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile App
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    
    // Auth routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/google', [AuthController::class, 'googleLogin']);
    Route::post('/auth/google-info', [AuthController::class, 'googleLoginWithInfo']);
    Route::post('/auth/login-phone', [AuthController::class, 'loginWithPhone']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    
    // App Config (Public)
    Route::get('/config', [ConfigController::class, 'index']);
    
    // Products & Categories (Public)
    Route::get('/home', [ProductController::class, 'home']);
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/products', [ProductController::class, 'products']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/{idOrSlug}', [ProductController::class, 'show']);
    
    // Cart (works for guests with session)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    
    // Order tracking (public)
    Route::post('/orders/track', [OrderController::class, 'track']);
    
    // FCM Token for Guest Users (Public - no auth required)
    Route::post('/notifications/register-device', [NotificationController::class, 'registerDevice']);
    
    // Help & Support (Public)
    Route::get('/support', [SupportController::class, 'index']);
    Route::get('/pages/{slug}', [SupportController::class, 'getPage']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthController::class, 'changePassword']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/fcm-token', [AuthController::class, 'updateFcmToken']);
        Route::delete('/auth/fcm-token', [AuthController::class, 'removeFcmToken']);
        
        // Addresses
        Route::get('/addresses', [AddressController::class, 'index']);
        Route::get('/addresses/{id}', [AddressController::class, 'show']);
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::put('/addresses/{id}', [AddressController::class, 'update']);
        Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
        Route::post('/addresses/{id}/default', [AddressController::class, 'setDefault']);
        
        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/add', [WishlistController::class, 'add']);
        Route::delete('/wishlist/{productId}', [WishlistController::class, 'remove']);
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
        Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check']);
        
        // Checkout & Orders
        Route::get('/checkout', [OrderController::class, 'checkoutData']);
        Route::post('/checkout/apply-coupon', [OrderController::class, 'applyCoupon']);
        Route::post('/orders', [OrderController::class, 'placeOrder']);
        Route::get('/orders', [OrderController::class, 'myOrders']);
        Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
        
        // Wallet
        Route::get('/wallet', [WalletController::class, 'index']);
        
        // Referrals
        Route::get('/referrals', [ReferralController::class, 'index']);
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });
});
