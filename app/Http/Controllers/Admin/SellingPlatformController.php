<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPlatformListing;
use App\Models\SellingPlatform;
use App\Models\PlatformOrder;
use App\Services\Platforms\PlatformServiceFactory;
use Illuminate\Http\Request;

class SellingPlatformController extends Controller
{
    /**
     * Dashboard - Overview of all platforms
     */
    public function index()
    {
        $platforms = SellingPlatform::withCount([
            'productListings',
            'productListings as active_listings_count' => function ($query) {
                $query->where('status', 'active');
            },
            'platformOrders'
        ])->orderBy('sort_order')->get();

        $stats = [
            'total_platforms' => $platforms->count(),
            'active_platforms' => $platforms->where('is_active', true)->count(),
            'total_listings' => ProductPlatformListing::count(),
            'active_listings' => ProductPlatformListing::where('status', 'active')->count(),
            'pending_listings' => ProductPlatformListing::where('status', 'pending')->count(),
            'total_platform_orders' => PlatformOrder::count(),
            'total_platform_revenue' => PlatformOrder::sum('platform_order_amount'),
            'total_commission_paid' => PlatformOrder::sum('commission_amount'),
        ];

        return view('admin.selling-platforms.index', compact('platforms', 'stats'));
    }

    /**
     * Show platform details and configuration
     */
    public function show(SellingPlatform $platform)
    {
        $platform->load(['productListings.product', 'platformOrders']);
        
        $listings = $platform->productListings()
            ->with('product')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_listings' => $platform->productListings()->count(),
            'active_listings' => $platform->productListings()->where('status', 'active')->count(),
            'pending_listings' => $platform->productListings()->where('status', 'pending')->count(),
            'total_orders' => $platform->platformOrders()->count(),
            'total_revenue' => $platform->platformOrders()->sum('platform_order_amount'),
            'total_commission' => $platform->platformOrders()->sum('commission_amount'),
        ];

        // Check if platform has API integration
        $service = PlatformServiceFactory::make($platform);
        $hasApi = $service && $service->isConfigured();

        return view('admin.selling-platforms.show', compact('platform', 'listings', 'stats', 'hasApi'));
    }

    /**
     * Edit platform settings
     */
    public function edit(SellingPlatform $platform)
    {
        return view('admin.selling-platforms.edit', compact('platform'));
    }

    /**
     * Update platform settings
     */
    public function update(Request $request, SellingPlatform $platform)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'seller_portal_url' => 'nullable|url',
            'description' => 'nullable|string',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Build settings array based on platform
        $settings = $platform->settings ?? [];
        
        if ($platform->code === 'amazon') {
            $settings = array_merge($settings, [
                'seller_id' => $request->input('seller_id'),
                'marketplace_id' => $request->input('marketplace_id'),
                'refresh_token' => $request->input('refresh_token'),
                'client_id' => $request->input('client_id'),
                'client_secret' => $request->input('client_secret'),
            ]);
        } elseif ($platform->code === 'flipkart') {
            $settings = array_merge($settings, [
                'seller_id' => $request->input('seller_id'),
                'api_key' => $request->input('api_key'),
                'api_secret' => $request->input('api_secret'),
            ]);
        } elseif ($platform->code === 'shopify') {
            $settings = array_merge($settings, [
                'store_url' => $request->input('store_url'),
                'api_key' => $request->input('api_key'),
                'api_secret' => $request->input('api_secret'),
                'access_token' => $request->input('access_token'),
            ]);
        } elseif ($platform->code === 'woocommerce') {
            $settings = array_merge($settings, [
                'store_url' => $request->input('store_url'),
                'consumer_key' => $request->input('consumer_key'),
                'consumer_secret' => $request->input('consumer_secret'),
            ]);
        } else {
            $settings = array_merge($settings, [
                'seller_id' => $request->input('seller_id'),
                'api_key' => $request->input('api_key'),
                'api_secret' => $request->input('api_secret'),
            ]);
        }

        $validated['settings'] = $settings;
        $validated['is_active'] = $request->boolean('is_active');

        $platform->update($validated);

        return redirect()->route('admin.selling-platforms.show', $platform)
            ->with('success', 'Platform settings updated successfully.');
    }

    /**
     * Test API connection
     */
    public function testConnection(SellingPlatform $platform)
    {
        $service = PlatformServiceFactory::make($platform);
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'No API service available for this platform.',
            ]);
        }

        $result = $service->testConnection();

        return response()->json($result);
    }

    /**
     * Toggle platform active status
     */
    public function toggleStatus(SellingPlatform $platform)
    {
        $platform->update(['is_active' => !$platform->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $platform->is_active,
            'message' => $platform->is_active ? 'Platform enabled' : 'Platform disabled',
        ]);
    }

    /**
     * Show products to add to platform
     */
    public function addProducts(SellingPlatform $platform)
    {
        $listedProductIds = $platform->productListings()->pluck('product_id');
        
        $products = Product::active()
            ->whereNotIn('id', $listedProductIds)
            ->with('primaryImage', 'category')
            ->paginate(20);

        return view('admin.selling-platforms.add-products', compact('platform', 'products'));
    }

    /**
     * Add products to platform
     */
    public function storeProducts(Request $request, SellingPlatform $platform)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
        ]);

        $added = 0;
        foreach ($validated['products'] as $productId) {
            $product = Product::find($productId);
            
            $exists = ProductPlatformListing::where('product_id', $productId)
                ->where('selling_platform_id', $platform->id)
                ->exists();
                
            if (!$exists) {
                ProductPlatformListing::create([
                    'product_id' => $productId,
                    'selling_platform_id' => $platform->id,
                    'platform_price' => $product->effective_price,
                    'platform_mrp' => $product->price,
                    'platform_stock' => $product->total_stock,
                    'platform_sku' => $product->sku,
                    'status' => 'draft',
                ]);
                $added++;
            }
        }

        return redirect()->route('admin.selling-platforms.show', $platform)
            ->with('success', "{$added} product(s) added to {$platform->name}.");
    }

    /**
     * Edit product listing
     */
    public function editListing(SellingPlatform $platform, ProductPlatformListing $listing)
    {
        $listing->load('product');
        
        $service = PlatformServiceFactory::make($platform);
        $hasApi = $service && $service->isConfigured();
        
        return view('admin.selling-platforms.edit-listing', compact('platform', 'listing', 'hasApi'));
    }

    /**
     * Update product listing
     */
    public function updateListing(Request $request, SellingPlatform $platform, ProductPlatformListing $listing)
    {
        $validated = $request->validate([
            'platform_product_id' => 'nullable|string|max:255',
            'platform_sku' => 'nullable|string|max:255',
            'listing_url' => 'nullable|url',
            'platform_price' => 'required|numeric|min:0',
            'platform_mrp' => 'nullable|numeric|min:0',
            'platform_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,pending,active,inactive',
        ]);

        if ($validated['status'] === 'active' && !$listing->listed_at) {
            $validated['listed_at'] = now();
        }

        $listing->update($validated);

        return redirect()->route('admin.selling-platforms.show', $platform)
            ->with('success', 'Listing updated successfully.');
    }

    /**
     * Push product to platform via API
     */
    public function pushToApi(SellingPlatform $platform, ProductPlatformListing $listing)
    {
        $service = PlatformServiceFactory::make($platform);
        
        if (!$service || !$service->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'API not configured for this platform.',
            ]);
        }

        // If product already exists on platform, update it
        if ($listing->platform_product_id) {
            $result = $service->updateProduct($listing);
        } else {
            $result = $service->createProduct($listing);
        }

        return response()->json($result);
    }

    /**
     * Sync stock to platform via API
     */
    public function syncStockToApi(SellingPlatform $platform, ProductPlatformListing $listing)
    {
        $service = PlatformServiceFactory::make($platform);
        
        if (!$service || !$service->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'API not configured for this platform.',
            ]);
        }

        // First update local stock from product
        $listing->update(['platform_stock' => $listing->product->total_stock]);

        // Then sync to platform
        $result = $service->updateStock($listing);

        return response()->json($result);
    }

    /**
     * Sync price to platform via API
     */
    public function syncPriceToApi(SellingPlatform $platform, ProductPlatformListing $listing)
    {
        $service = PlatformServiceFactory::make($platform);
        
        if (!$service || !$service->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'API not configured for this platform.',
            ]);
        }

        $result = $service->updatePrice($listing);

        return response()->json($result);
    }

    /**
     * Delete product from platform via API
     */
    public function deleteFromApi(SellingPlatform $platform, ProductPlatformListing $listing)
    {
        $service = PlatformServiceFactory::make($platform);
        
        if (!$service || !$service->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'API not configured for this platform.',
            ]);
        }

        $result = $service->deleteProduct($listing);

        return response()->json($result);
    }

    /**
     * Bulk push products to platform
     */
    public function bulkPushToApi(Request $request, SellingPlatform $platform)
    {
        $validated = $request->validate([
            'listing_ids' => 'required|array',
            'listing_ids.*' => 'exists:product_platform_listings,id',
        ]);

        $service = PlatformServiceFactory::make($platform);
        
        if (!$service || !$service->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'API not configured for this platform.',
            ]);
        }

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($validated['listing_ids'] as $listingId) {
            $listing = ProductPlatformListing::find($listingId);
            
            if (!$listing) continue;

            $result = $listing->platform_product_id 
                ? $service->updateProduct($listing)
                : $service->createProduct($listing);

            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'product' => $listing->product->name,
                    'error' => $result['message'] ?? 'Unknown error',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$results['success']} synced, {$results['failed']} failed.",
            'details' => $results,
        ]);
    }

    /**
     * Delete product listing
     */
    public function deleteListing(SellingPlatform $platform, ProductPlatformListing $listing)
    {
        $listing->delete();

        return redirect()->route('admin.selling-platforms.show', $platform)
            ->with('success', 'Listing removed from platform.');
    }

    /**
     * Bulk update listing status
     */
    public function bulkUpdateStatus(Request $request, SellingPlatform $platform)
    {
        $validated = $request->validate([
            'listing_ids' => 'required|array',
            'listing_ids.*' => 'exists:product_platform_listings,id',
            'status' => 'required|in:draft,pending,active,inactive',
        ]);

        $updateData = ['status' => $validated['status']];
        
        if ($validated['status'] === 'active') {
            $updateData['listed_at'] = now();
        }

        ProductPlatformListing::whereIn('id', $validated['listing_ids'])
            ->where('selling_platform_id', $platform->id)
            ->update($updateData);

        return response()->json([
            'success' => true,
            'message' => count($validated['listing_ids']) . ' listings updated.',
        ]);
    }

    /**
     * Sync stock with platform (local)
     */
    public function syncStock(SellingPlatform $platform)
    {
        $listings = $platform->productListings()->with('product')->get();
        $synced = 0;

        foreach ($listings as $listing) {
            $newStock = $listing->product->total_stock;
            if ($listing->platform_stock !== $newStock) {
                $listing->update([
                    'platform_stock' => $newStock,
                    'last_synced_at' => now(),
                ]);
                $synced++;
            }
        }

        return redirect()->back()->with('success', "{$synced} listing(s) stock synced locally.");
    }

    /**
     * Sync all stock to platform via API
     */
    public function syncAllStockToApi(SellingPlatform $platform)
    {
        $service = PlatformServiceFactory::make($platform);
        
        if (!$service || !$service->isConfigured()) {
            return redirect()->back()->with('error', 'API not configured for this platform.');
        }

        $listings = $platform->productListings()
            ->where('status', 'active')
            ->whereNotNull('platform_product_id')
            ->with('product')
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($listings as $listing) {
            $listing->update(['platform_stock' => $listing->product->total_stock]);
            
            $result = $service->updateStock($listing);
            
            if ($result['success']) {
                $synced++;
            } else {
                $failed++;
            }
        }

        return redirect()->back()
            ->with('success', "{$synced} synced to {$platform->name}" . ($failed > 0 ? ", {$failed} failed" : ""));
    }

    /**
     * Fetch orders from platform API
     */
    public function fetchOrdersFromApi(SellingPlatform $platform)
    {
        $service = PlatformServiceFactory::make($platform);
        
        if (!$service || !$service->isConfigured()) {
            return redirect()->back()->with('error', 'API not configured for this platform.');
        }

        $result = $service->fetchOrders();

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message'] ?? 'Failed to fetch orders.');
        }

        $imported = 0;
        foreach ($result['orders'] ?? [] as $orderData) {
            // Check if order already exists
            $orderId = $orderData['id'] ?? $orderData['order_id'] ?? null;
            if (!$orderId) continue;

            $exists = PlatformOrder::where('selling_platform_id', $platform->id)
                ->where('platform_order_id', $orderId)
                ->exists();

            if (!$exists) {
                $amount = $orderData['total_price'] ?? $orderData['total'] ?? 0;
                $commission = $platform->calculateCommission($amount);

                PlatformOrder::create([
                    'selling_platform_id' => $platform->id,
                    'platform_order_id' => $orderId,
                    'platform_order_amount' => $amount,
                    'commission_amount' => $commission,
                    'settlement_amount' => $amount - $commission,
                    'customer_name' => $orderData['customer']['first_name'] ?? $orderData['billing']['first_name'] ?? null,
                    'platform_order_status' => $orderData['status'] ?? $orderData['financial_status'] ?? 'pending',
                    'platform_order_date' => $orderData['created_at'] ?? now(),
                    'order_data' => $orderData,
                ]);
                $imported++;
            }
        }

        return redirect()->back()->with('success', "{$imported} new orders imported from {$platform->name}.");
    }

    /**
     * Platform orders list
     */
    public function orders(SellingPlatform $platform)
    {
        $orders = $platform->platformOrders()
            ->latest('platform_order_date')
            ->paginate(20);

        $service = PlatformServiceFactory::make($platform);
        $hasApi = $service && $service->isConfigured();

        return view('admin.selling-platforms.orders', compact('platform', 'orders', 'hasApi'));
    }

    /**
     * Add manual platform order
     */
    public function storeOrder(Request $request, SellingPlatform $platform)
    {
        $validated = $request->validate([
            'platform_order_id' => 'required|string|max:255',
            'platform_order_amount' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string',
            'platform_order_status' => 'nullable|string|max:50',
            'platform_order_date' => 'nullable|date',
        ]);

        $commission = $platform->calculateCommission($validated['platform_order_amount']);
        
        PlatformOrder::create([
            'selling_platform_id' => $platform->id,
            'platform_order_id' => $validated['platform_order_id'],
            'platform_order_amount' => $validated['platform_order_amount'],
            'commission_amount' => $commission,
            'settlement_amount' => $validated['platform_order_amount'] - $commission,
            'customer_name' => $validated['customer_name'],
            'shipping_address' => $validated['shipping_address'],
            'platform_order_status' => $validated['platform_order_status'] ?? 'pending',
            'platform_order_date' => $validated['platform_order_date'] ?? now(),
        ]);

        return redirect()->route('admin.selling-platforms.orders', $platform)
            ->with('success', 'Platform order added successfully.');
    }
}
