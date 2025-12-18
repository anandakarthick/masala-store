<?php

namespace App\Services\Platforms;

use App\Models\ProductPlatformListing;

/**
 * Generic service for platforms without direct API integration
 * Used for manual sync/tracking
 */
class GenericPlatformService extends BasePlatformService
{
    public function isConfigured(): bool
    {
        // Generic platforms don't require API configuration
        return true;
    }

    public function testConnection(): array
    {
        return [
            'success' => true,
            'message' => 'This platform uses manual sync. No API connection required.',
            'manual_sync' => true,
        ];
    }

    public function createProduct(ProductPlatformListing $listing): array
    {
        // For platforms without API, we just update the listing status
        $listing->update([
            'status' => 'pending',
            'platform_data' => [
                'sync_method' => 'manual',
                'created_at' => now()->toIso8601String(),
            ],
        ]);

        return [
            'success' => true,
            'message' => "Product marked for {$this->platform->name}. Please add it manually through the seller portal.",
            'manual_sync' => true,
            'seller_portal_url' => $this->platform->seller_portal_url,
        ];
    }

    public function updateProduct(ProductPlatformListing $listing): array
    {
        $listing->update(['last_synced_at' => now()]);

        return [
            'success' => true,
            'message' => 'Listing updated locally. Please update on the platform manually.',
            'manual_sync' => true,
        ];
    }

    public function updateStock(ProductPlatformListing $listing): array
    {
        $listing->update([
            'platform_stock' => $listing->product->total_stock,
            'last_synced_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'Stock synced locally. Please update on the platform manually.',
            'manual_sync' => true,
            'new_stock' => $listing->platform_stock,
        ];
    }

    public function updatePrice(ProductPlatformListing $listing): array
    {
        $listing->update(['last_synced_at' => now()]);

        return [
            'success' => true,
            'message' => 'Price updated locally. Please update on the platform manually.',
            'manual_sync' => true,
        ];
    }

    public function deleteProduct(ProductPlatformListing $listing): array
    {
        $listing->update([
            'status' => 'inactive',
            'last_synced_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'Listing marked as inactive. Please remove from the platform manually.',
            'manual_sync' => true,
        ];
    }

    public function fetchOrders(array $params = []): array
    {
        return [
            'success' => true,
            'message' => 'Please add orders manually from the platform.',
            'manual_sync' => true,
            'orders' => [],
        ];
    }

    public function getProductStatus(ProductPlatformListing $listing): array
    {
        return [
            'success' => true,
            'status' => $listing->status,
            'message' => 'Status is tracked locally. Check the platform for actual status.',
            'manual_sync' => true,
        ];
    }
}
