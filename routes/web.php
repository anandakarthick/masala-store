<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\CustomComboController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomComboController as AdminCustomComboController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\SellingPlatformController as AdminSellingPlatformController;
use App\Http\Controllers\Admin\VariantAttributeController as AdminVariantAttributeController;
use App\Http\Controllers\Admin\EstimateController as AdminEstimateController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReferralController as AdminReferralController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use Illuminate\Support\Facades\Route;

// Test route for debugging - no blade
Route::get('/test-no-blade', function () {
    return response('Laravel is working! No blade template used.', 200);
});

// Test route for debugging blade
Route::get('/test-blade', function () {
    return view('test');
});

/*
|--------------------------------------------------------------------------
| SEO Redirects - Removed/Moved Pages
|--------------------------------------------------------------------------
| Add 301 redirects for removed categories, products, or pages here.
| This helps maintain SEO value and provides good user experience.
*/

// Removed categories - redirect to products page or similar category
Route::get('/category/scented-candles', function () {
    return redirect('/products', 301);
});

Route::get('/category/return-gift-candles', function () {
    return redirect('/products', 301);
});

// Add more redirects here as needed:
// Route::get('/category/old-category-slug', function () {
//     return redirect('/category/new-category-slug', 301);
// });

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// SEO Routes - sitemap.xml served from public folder
// robots.txt is served directly from public folder by web server

// CSRF Token refresh route
Route::get('/csrf-token', function() {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/page/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/offers', [ProductController::class, 'offers'])->name('products.offers');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category:slug}', [ProductController::class, 'category'])->name('category.show');

// Build Your Own Combo (Custom Combo)
Route::prefix('combo')->name('combo.')->group(function () {
    Route::get('/', [CustomComboController::class, 'index'])->name('index');
    Route::get('/build/{combo:slug}', [CustomComboController::class, 'builder'])->name('builder');
    Route::post('/start/{combo}', [CustomComboController::class, 'startCombo'])->name('start');
    Route::post('/add-product', [CustomComboController::class, 'addProduct'])->name('add-product');
    Route::post('/remove-product', [CustomComboController::class, 'removeProduct'])->name('remove-product');
    Route::post('/update-quantity', [CustomComboController::class, 'updateQuantity'])->name('update-quantity');
    Route::post('/add-to-cart', [CustomComboController::class, 'addToCart'])->name('add-to-cart');
    Route::post('/status', [CustomComboController::class, 'getStatus'])->name('status');
    Route::post('/delete', [CustomComboController::class, 'deleteCombo'])->name('delete');
});

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/remove-combo', [CartController::class, 'removeCombo'])->name('remove-combo');
    Route::post('/update-combo', [CartController::class, 'updateCombo'])->name('update-combo');
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

// Razorpay Routes
Route::prefix('razorpay')->name('razorpay.')->group(function () {
    Route::post('/create-order', [RazorpayController::class, 'createOrder'])->name('create-order');
    Route::post('/verify-payment', [RazorpayController::class, 'verifyPayment'])->name('verify-payment');
});
Route::post('/razorpay/webhook', [RazorpayController::class, 'webhook'])->name('razorpay.webhook')->withoutMiddleware(['web', 'csrf']);

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
    
    // Google OAuth Routes
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
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
    Route::get('/orders/{order}/review', [ReviewController::class, 'create'])->name('orders.review');
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('orders.review.store');
    Route::get('/profile', [CustomerAccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [CustomerAccountController::class, 'changePassword'])->name('password');
    Route::put('/change-password', [CustomerAccountController::class, 'updatePassword'])->name('password.update');
    Route::get('/wishlist', [CustomerAccountController::class, 'wishlist'])->name('wishlist');
    
    // Wallet & Referrals
    Route::get('/wallet', [CustomerAccountController::class, 'wallet'])->name('wallet');
    Route::get('/referrals', [CustomerAccountController::class, 'referrals'])->name('referrals');
});

// Review via token (for guest orders or direct email link)
Route::get('/review/{token}', [ReviewController::class, 'createByToken'])->name('review.token');
Route::post('/review/{token}', [ReviewController::class, 'storeByToken'])->name('review.token.store');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Notifications (for new order alerts)
    Route::get('/notifications/check-orders', [AdminNotificationController::class, 'checkNewOrders'])->name('notifications.check-orders');
    Route::get('/notifications/pending-count', [AdminNotificationController::class, 'pendingCount'])->name('notifications.pending-count');
    Route::get('/notifications/unseen-count', [AdminNotificationController::class, 'unseenCount'])->name('notifications.unseen-count');
    Route::post('/notifications/mark-seen/{order}', [AdminNotificationController::class, 'markAsSeen'])->name('notifications.mark-seen');
    Route::post('/notifications/mark-all-seen', [AdminNotificationController::class, 'markAllAsSeen'])->name('notifications.mark-all-seen');

    // Categories
    Route::resource('categories', AdminCategoryController::class);
    Route::post('categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Product Images (must be before products resource to avoid conflicts)
    Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::post('products/images/{image}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.set-primary-image');

    // Products
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/{product}/update-stock', [AdminProductController::class, 'updateStock'])->name('products.update-stock');
    Route::post('products/{product}/reorder-images', [AdminProductController::class, 'reorderImages'])->name('products.reorder-images');
    
    // Product Variants
    Route::get('products/{product}/variants', [AdminProductVariantController::class, 'index'])->name('products.variants.index');
    Route::post('products/{product}/variants', [AdminProductVariantController::class, 'store'])->name('products.variants.store');
    Route::post('products/{product}/variants/bulk', [AdminProductVariantController::class, 'bulkCreate'])->name('products.variants.bulk');
    Route::put('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])->name('products.variants.update');
    Route::delete('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])->name('products.variants.destroy');

    // Variant Attributes (Size, Color, Material, etc.)
    Route::prefix('variant-attributes')->name('variant-attributes.')->group(function () {
        Route::get('/', [AdminVariantAttributeController::class, 'index'])->name('index');
        Route::post('/', [AdminVariantAttributeController::class, 'store'])->name('store');
        Route::put('/{attribute}', [AdminVariantAttributeController::class, 'update'])->name('update');
        Route::delete('/{attribute}', [AdminVariantAttributeController::class, 'destroy'])->name('destroy');
        Route::post('/{attribute}/values', [AdminVariantAttributeController::class, 'storeValue'])->name('values.store');
        Route::put('/{attribute}/values/{value}', [AdminVariantAttributeController::class, 'updateValue'])->name('values.update');
        Route::delete('/{attribute}/values/{value}', [AdminVariantAttributeController::class, 'destroyValue'])->name('values.destroy');
    });
    Route::get('variant-attributes/get-values', [AdminVariantAttributeController::class, 'getValues'])->name('variant-attributes.get-values');

    // Custom Combos (Build Your Own Combo Settings)
    Route::resource('combos', AdminCustomComboController::class);
    Route::post('combos/{combo}/toggle-status', [AdminCustomComboController::class, 'toggleStatus'])->name('combos.toggle-status');

    // Orders
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/update-payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::post('orders/{order}/update-delivery', [AdminOrderController::class, 'updateDelivery'])->name('orders.update-delivery');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'generateInvoice'])->name('orders.invoice');
    Route::post('orders/{order}/add-note', [AdminOrderController::class, 'addNote'])->name('orders.add-note');

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index');
        Route::get('/{review}', [AdminReviewController::class, 'show'])->name('show');
        Route::post('/{review}/approve', [AdminReviewController::class, 'approve'])->name('approve');
        Route::post('/{review}/reject', [AdminReviewController::class, 'reject'])->name('reject');
        Route::post('/{review}/toggle-featured', [AdminReviewController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{review}', [AdminReviewController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-approve', [AdminReviewController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-delete', [AdminReviewController::class, 'bulkDelete'])->name('bulk-delete');
    });

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
        
        // Social Media & WhatsApp
        Route::get('/social-media', [App\Http\Controllers\Admin\SocialMediaController::class, 'index'])->name('social-media');
        Route::post('/social-media/links', [App\Http\Controllers\Admin\SocialMediaController::class, 'storeLink'])->name('social-media.store');
        Route::put('/social-media/links/{link}', [App\Http\Controllers\Admin\SocialMediaController::class, 'updateLink'])->name('social-media.update');
        Route::delete('/social-media/links/{link}', [App\Http\Controllers\Admin\SocialMediaController::class, 'destroyLink'])->name('social-media.destroy');
        Route::put('/social-media/whatsapp', [App\Http\Controllers\Admin\SocialMediaController::class, 'updateWhatsApp'])->name('social-media.whatsapp');
        Route::put('/social-media/marquee', [App\Http\Controllers\Admin\SocialMediaController::class, 'updateMarquee'])->name('social-media.marquee');
    });

    // Pages (Privacy Policy, Terms, etc.)
    Route::resource('pages', App\Http\Controllers\Admin\PageController::class);

    // Banner Generator
    Route::prefix('banner-generator')->name('banner-generator.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('index');
        Route::get('/product-details', [App\Http\Controllers\Admin\BannerController::class, 'getProductDetails'])->name('product-details');
        Route::get('/category-details', [App\Http\Controllers\Admin\BannerController::class, 'getCategoryDetails'])->name('category-details');
        Route::post('/save-to-store', [App\Http\Controllers\Admin\BannerController::class, 'saveToStore'])->name('save-to-store');
        Route::get('/store-banners', [App\Http\Controllers\Admin\BannerController::class, 'getStoreBanners'])->name('store-banners');
    });

    // Estimates
    Route::prefix('estimates')->name('estimates.')->group(function () {
        Route::get('/', [AdminEstimateController::class, 'index'])->name('index');
        Route::get('/create', [AdminEstimateController::class, 'create'])->name('create');
        Route::post('/', [AdminEstimateController::class, 'store'])->name('store');
        Route::get('/{estimate}', [AdminEstimateController::class, 'show'])->name('show');
        Route::get('/{estimate}/edit', [AdminEstimateController::class, 'edit'])->name('edit');
        Route::put('/{estimate}', [AdminEstimateController::class, 'update'])->name('update');
        Route::delete('/{estimate}', [AdminEstimateController::class, 'destroy'])->name('destroy');
        Route::get('/{estimate}/download', [AdminEstimateController::class, 'downloadPdf'])->name('download');
        Route::post('/{estimate}/send-email', [AdminEstimateController::class, 'sendEmail'])->name('send-email');
        Route::get('/{estimate}/whatsapp', [AdminEstimateController::class, 'getWhatsAppUrl'])->name('whatsapp');
        Route::post('/{estimate}/update-status', [AdminEstimateController::class, 'updateStatus'])->name('update-status');
        Route::get('/{estimate}/duplicate', [AdminEstimateController::class, 'duplicate'])->name('duplicate');
    });

    // Payment Methods
    Route::get('payment-methods', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::get('payment-methods/{paymentMethod}/edit', [AdminPaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    Route::put('payment-methods/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::post('payment-methods/{paymentMethod}/toggle', [AdminPaymentMethodController::class, 'toggleStatus'])->name('payment-methods.toggle');

    // Selling Platforms (Multi-channel selling)
    Route::prefix('selling-platforms')->name('selling-platforms.')->group(function () {
        Route::get('/', [AdminSellingPlatformController::class, 'index'])->name('index');
        Route::get('/{platform}', [AdminSellingPlatformController::class, 'show'])->name('show');
        Route::get('/{platform}/edit', [AdminSellingPlatformController::class, 'edit'])->name('edit');
        Route::put('/{platform}', [AdminSellingPlatformController::class, 'update'])->name('update');
        Route::post('/{platform}/toggle', [AdminSellingPlatformController::class, 'toggleStatus'])->name('toggle');
        Route::post('/{platform}/test-connection', [AdminSellingPlatformController::class, 'testConnection'])->name('test-connection');
        
        // Product Listings
        Route::get('/{platform}/add-products', [AdminSellingPlatformController::class, 'addProducts'])->name('add-products');
        Route::post('/{platform}/add-products', [AdminSellingPlatformController::class, 'storeProducts'])->name('store-products');
        Route::get('/{platform}/listings/{listing}/edit', [AdminSellingPlatformController::class, 'editListing'])->name('edit-listing');
        Route::put('/{platform}/listings/{listing}', [AdminSellingPlatformController::class, 'updateListing'])->name('update-listing');
        Route::delete('/{platform}/listings/{listing}', [AdminSellingPlatformController::class, 'deleteListing'])->name('delete-listing');
        Route::post('/{platform}/bulk-status', [AdminSellingPlatformController::class, 'bulkUpdateStatus'])->name('bulk-status');
        Route::post('/{platform}/sync-stock', [AdminSellingPlatformController::class, 'syncStock'])->name('sync-stock');
        
        // API Sync Routes
        Route::post('/{platform}/listings/{listing}/push', [AdminSellingPlatformController::class, 'pushToApi'])->name('push-to-api');
        Route::post('/{platform}/listings/{listing}/sync-stock', [AdminSellingPlatformController::class, 'syncStockToApi'])->name('sync-stock-api');
        Route::post('/{platform}/listings/{listing}/sync-price', [AdminSellingPlatformController::class, 'syncPriceToApi'])->name('sync-price-api');
        Route::post('/{platform}/listings/{listing}/delete-from-api', [AdminSellingPlatformController::class, 'deleteFromApi'])->name('delete-from-api');
        Route::post('/{platform}/bulk-push', [AdminSellingPlatformController::class, 'bulkPushToApi'])->name('bulk-push');
        Route::post('/{platform}/sync-all-stock', [AdminSellingPlatformController::class, 'syncAllStockToApi'])->name('sync-all-stock-api');
        
        // Platform Orders
        Route::get('/{platform}/orders', [AdminSellingPlatformController::class, 'orders'])->name('orders');
        Route::post('/{platform}/orders', [AdminSellingPlatformController::class, 'storeOrder'])->name('store-order');
        Route::post('/{platform}/fetch-orders', [AdminSellingPlatformController::class, 'fetchOrdersFromApi'])->name('fetch-orders-api');
    });

    // Referrals & Wallet Management
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [AdminReferralController::class, 'index'])->name('index');
        Route::get('/top-referrers', [AdminReferralController::class, 'topReferrers'])->name('top-referrers');
        Route::get('/user/{user}/wallet', [AdminReferralController::class, 'userWallet'])->name('user-wallet');
        Route::post('/user/{user}/wallet/adjust', [AdminReferralController::class, 'adjustWallet'])->name('adjust-wallet');
        Route::post('/{referral}/process-reward', [AdminReferralController::class, 'processReward'])->name('process-reward');
        Route::post('/process-all-pending', [AdminReferralController::class, 'processAllPending'])->name('process-all-pending');
        Route::post('/{referral}/mark-completed', [AdminReferralController::class, 'markCompleted'])->name('mark-completed');
        Route::post('/{referral}/mark-expired', [AdminReferralController::class, 'markExpired'])->name('mark-expired');
    });
});
