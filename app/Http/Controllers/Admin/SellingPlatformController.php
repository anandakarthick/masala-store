<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPlatformListing;
use App\Models\SellingPlatform;
use App\Models\PlatformOrder;
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

        return view('admin.selling-platforms.show', compact('platform', 'listings', 'stats'));
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
        $settings = [];
        
        if ($platform->code === 'amazon') {
            $settings = [
                'seller_id' => $request->input('seller_id'),
                'marketplace_id' => $request->input('marketplace_id'),
                'mws_access_key' => $request->input('mws_access_key'),
                'mws_secret_key' => $request->input('mws_secret_key'),
            ];
        } elseif ($platform->code === 'flipkart') {
            $settings = [
                'seller_id' => $request->input('seller_id'),
                'api_key' => $request->input('api_key'),
                'api_secret' => $request->input('api_secret'),
            ];
        } elseif ($platform->code === 'shopify') {
            $settings = [
                'store_url' => $request->input('store_url'),
                'api_key' => $request->input('api_key'),
                'api_secret' => $request->input('api_secret'),
                'access_token' => $request->input('access_token'),
            ];
        } elseif (in_array($platform->code, ['meesho', 'indiamart', 'etsy', 'myntra', 'jiomart'])) {
            $settings = [
                'seller_id' => $request->input('seller_id'),
                'api_key' => $request->input('api_key'),
                'api_secret' => $request->input('api_secret'),
            ];
        }

        $validated['settings'] = $settings;
        $validated['is_active'] = $request->boolean('is_active');

        $platform->update($validated);

        return redirect()->route('admin.selling-platforms.show', $platform)
            ->with('success', 'Platform settings updated successfully.');
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
        // Get products not yet listed on this platform
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
            
            // Check if not already listed
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
        return view('admin.selling-platforms.edit-listing', compact('platform', 'listing'));
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
     * Sync stock with platform
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

        return redirect()->back()->with('success', "{$synced} listing(s) stock synced.");
    }

    /**
     * Platform orders list
     */
    public function orders(SellingPlatform $platform)
    {
        $orders = $platform->platformOrders()
            ->latest('platform_order_date')
            ->paginate(20);

        return view('admin.selling-platforms.orders', compact('platform', 'orders'));
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
