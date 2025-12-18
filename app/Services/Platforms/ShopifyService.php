<?php

namespace App\Services\Platforms;

use App\Models\ProductPlatformListing;

class ShopifyService extends BasePlatformService
{
    protected string $baseUrl;

    public function __construct($platform)
    {
        parent::__construct($platform);
        $storeUrl = $this->config['store_url'] ?? '';
        $this->baseUrl = "https://{$storeUrl}/admin/api/2024-01";
    }

    public function isConfigured(): bool
    {
        return !empty($this->config['store_url']) 
            && !empty($this->config['access_token']);
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API not configured'];
        }

        $response = $this->makeRequest('GET', "{$this->baseUrl}/shop.json", [], $this->getHeaders());
        
        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Connected successfully' : 'Connection failed',
            'data' => $response['data'] ?? null,
        ];
    }

    public function createProduct(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API not configured'];
        }

        $product = $listing->product;
        
        $productData = [
            'product' => [
                'title' => $product->name,
                'body_html' => $product->description,
                'vendor' => \App\Models\Setting::get('business_name', 'SV Masala'),
                'product_type' => $product->category->name ?? 'General',
                'variants' => [
                    [
                        'price' => $listing->platform_price,
                        'compare_at_price' => $listing->platform_mrp,
                        'sku' => $listing->platform_sku ?? $product->sku,
                        'inventory_quantity' => $listing->platform_stock ?? $product->total_stock,
                        'inventory_management' => 'shopify',
                        'weight' => $product->weight,
                        'weight_unit' => 'g',
                    ]
                ],
                'status' => 'active',
            ]
        ];

        // Add images if available
        if ($product->primary_image_url) {
            $productData['product']['images'] = [
                ['src' => $product->primary_image_url]
            ];
        }

        $response = $this->makeRequest('POST', "{$this->baseUrl}/products.json", $productData, $this->getHeaders());
        
        $this->logActivity('createProduct', $productData, $response);

        if ($response['success'] && isset($response['data']['product']['id'])) {
            $listing->update([
                'platform_product_id' => $response['data']['product']['id'],
                'listing_url' => "https://{$this->config['store_url']}/products/{$response['data']['product']['handle']}",
                'status' => 'active',
                'listed_at' => now(),
                'last_synced_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Product created on Shopify',
                'product_id' => $response['data']['product']['id'],
            ];
        }

        return [
            'success' => false,
            'message' => $response['data']['errors'] ?? 'Failed to create product',
            'errors' => $response['data']['errors'] ?? null,
        ];
    }

    public function updateProduct(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        $product = $listing->product;
        
        $productData = [
            'product' => [
                'id' => $listing->platform_product_id,
                'title' => $product->name,
                'body_html' => $product->description,
            ]
        ];

        $response = $this->makeRequest(
            'PUT', 
            "{$this->baseUrl}/products/{$listing->platform_product_id}.json", 
            $productData, 
            $this->getHeaders()
        );

        $this->logActivity('updateProduct', $productData, $response);

        if ($response['success']) {
            $listing->update(['last_synced_at' => now()]);
        }

        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Product updated' : 'Failed to update product',
        ];
    }

    public function updateStock(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        // First get the inventory item ID
        $productResponse = $this->makeRequest(
            'GET',
            "{$this->baseUrl}/products/{$listing->platform_product_id}.json",
            [],
            $this->getHeaders()
        );

        if (!$productResponse['success']) {
            return ['success' => false, 'message' => 'Failed to fetch product'];
        }

        $inventoryItemId = $productResponse['data']['product']['variants'][0]['inventory_item_id'] ?? null;
        
        if (!$inventoryItemId) {
            return ['success' => false, 'message' => 'Inventory item not found'];
        }

        // Get location ID
        $locationsResponse = $this->makeRequest('GET', "{$this->baseUrl}/locations.json", [], $this->getHeaders());
        $locationId = $locationsResponse['data']['locations'][0]['id'] ?? null;

        if (!$locationId) {
            return ['success' => false, 'message' => 'Location not found'];
        }

        // Set inventory level
        $inventoryData = [
            'location_id' => $locationId,
            'inventory_item_id' => $inventoryItemId,
            'available' => $listing->platform_stock ?? $listing->product->total_stock,
        ];

        $response = $this->makeRequest(
            'POST',
            "{$this->baseUrl}/inventory_levels/set.json",
            $inventoryData,
            $this->getHeaders()
        );

        $this->logActivity('updateStock', $inventoryData, $response);

        if ($response['success']) {
            $listing->update(['last_synced_at' => now()]);
        }

        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Stock updated' : 'Failed to update stock',
        ];
    }

    public function updatePrice(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        // Get variant ID
        $productResponse = $this->makeRequest(
            'GET',
            "{$this->baseUrl}/products/{$listing->platform_product_id}.json",
            [],
            $this->getHeaders()
        );

        if (!$productResponse['success']) {
            return ['success' => false, 'message' => 'Failed to fetch product'];
        }

        $variantId = $productResponse['data']['product']['variants'][0]['id'] ?? null;

        if (!$variantId) {
            return ['success' => false, 'message' => 'Variant not found'];
        }

        $variantData = [
            'variant' => [
                'id' => $variantId,
                'price' => $listing->platform_price,
                'compare_at_price' => $listing->platform_mrp,
            ]
        ];

        $response = $this->makeRequest(
            'PUT',
            "{$this->baseUrl}/variants/{$variantId}.json",
            $variantData,
            $this->getHeaders()
        );

        $this->logActivity('updatePrice', $variantData, $response);

        if ($response['success']) {
            $listing->update(['last_synced_at' => now()]);
        }

        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Price updated' : 'Failed to update price',
        ];
    }

    public function deleteProduct(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        $response = $this->makeRequest(
            'DELETE',
            "{$this->baseUrl}/products/{$listing->platform_product_id}.json",
            [],
            $this->getHeaders()
        );

        $this->logActivity('deleteProduct', ['product_id' => $listing->platform_product_id], $response);

        if ($response['success']) {
            $listing->update([
                'platform_product_id' => null,
                'status' => 'inactive',
                'last_synced_at' => now(),
            ]);
        }

        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Product deleted from Shopify' : 'Failed to delete product',
        ];
    }

    public function fetchOrders(array $params = []): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API not configured'];
        }

        $queryParams = http_build_query([
            'status' => $params['status'] ?? 'any',
            'limit' => $params['limit'] ?? 50,
            'created_at_min' => $params['created_at_min'] ?? now()->subDays(30)->toIso8601String(),
        ]);

        $response = $this->makeRequest(
            'GET',
            "{$this->baseUrl}/orders.json?{$queryParams}",
            [],
            $this->getHeaders()
        );

        return [
            'success' => $response['success'],
            'orders' => $response['data']['orders'] ?? [],
            'message' => $response['success'] ? 'Orders fetched' : 'Failed to fetch orders',
        ];
    }

    public function getProductStatus(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        $response = $this->makeRequest(
            'GET',
            "{$this->baseUrl}/products/{$listing->platform_product_id}.json",
            [],
            $this->getHeaders()
        );

        if ($response['success']) {
            return [
                'success' => true,
                'status' => $response['data']['product']['status'] ?? 'unknown',
                'data' => $response['data']['product'],
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to fetch product status',
        ];
    }

    protected function getHeaders(): array
    {
        return [
            "X-Shopify-Access-Token: {$this->config['access_token']}",
        ];
    }
}
