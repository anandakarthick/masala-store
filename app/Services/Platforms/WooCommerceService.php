<?php

namespace App\Services\Platforms;

use App\Models\ProductPlatformListing;

class WooCommerceService extends BasePlatformService
{
    protected string $baseUrl;

    public function __construct($platform)
    {
        parent::__construct($platform);
        $storeUrl = rtrim($this->config['store_url'] ?? '', '/');
        $this->baseUrl = "{$storeUrl}/wp-json/wc/v3";
    }

    public function isConfigured(): bool
    {
        return !empty($this->config['store_url']) 
            && !empty($this->config['consumer_key'])
            && !empty($this->config['consumer_secret']);
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API not configured'];
        }

        $response = $this->makeRequest('GET', "{$this->baseUrl}/system_status", [], $this->getHeaders());
        
        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Connected successfully' : 'Connection failed',
        ];
    }

    public function createProduct(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API not configured'];
        }

        $product = $listing->product;
        
        $productData = [
            'name' => $product->name,
            'type' => 'simple',
            'regular_price' => (string) $listing->platform_mrp,
            'sale_price' => (string) $listing->platform_price,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'sku' => $listing->platform_sku ?? $product->sku,
            'manage_stock' => true,
            'stock_quantity' => $listing->platform_stock ?? $product->total_stock,
            'weight' => (string) $product->weight,
            'status' => 'publish',
        ];

        if ($product->primary_image_url) {
            $productData['images'] = [
                ['src' => $product->primary_image_url]
            ];
        }

        if ($product->category) {
            $productData['categories'] = [
                ['name' => $product->category->name]
            ];
        }

        $response = $this->makeRequest('POST', "{$this->baseUrl}/products", $productData, $this->getHeaders());
        
        $this->logActivity('createProduct', $productData, $response);

        if ($response['success'] && isset($response['data']['id'])) {
            $listing->update([
                'platform_product_id' => $response['data']['id'],
                'listing_url' => $response['data']['permalink'] ?? null,
                'status' => 'active',
                'listed_at' => now(),
                'last_synced_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Product created on WooCommerce',
                'product_id' => $response['data']['id'],
            ];
        }

        return [
            'success' => false,
            'message' => $response['data']['message'] ?? 'Failed to create product',
        ];
    }

    public function updateProduct(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        $product = $listing->product;
        
        $productData = [
            'name' => $product->name,
            'description' => $product->description,
            'short_description' => $product->short_description,
        ];

        $response = $this->makeRequest(
            'PUT', 
            "{$this->baseUrl}/products/{$listing->platform_product_id}", 
            $productData, 
            $this->getHeaders()
        );

        $this->logActivity('updateProduct', $productData, $response);

        if ($response['success']) {
            $listing->update(['last_synced_at' => now()]);
        }

        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Product updated' : 'Failed to update',
        ];
    }

    public function updateStock(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        $stockData = [
            'stock_quantity' => $listing->platform_stock ?? $listing->product->total_stock,
            'manage_stock' => true,
        ];

        $response = $this->makeRequest(
            'PUT',
            "{$this->baseUrl}/products/{$listing->platform_product_id}",
            $stockData,
            $this->getHeaders()
        );

        $this->logActivity('updateStock', $stockData, $response);

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

        $priceData = [
            'regular_price' => (string) $listing->platform_mrp,
            'sale_price' => (string) $listing->platform_price,
        ];

        $response = $this->makeRequest(
            'PUT',
            "{$this->baseUrl}/products/{$listing->platform_product_id}",
            $priceData,
            $this->getHeaders()
        );

        $this->logActivity('updatePrice', $priceData, $response);

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
            "{$this->baseUrl}/products/{$listing->platform_product_id}?force=true",
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
            'message' => $response['success'] ? 'Product deleted' : 'Failed to delete',
        ];
    }

    public function fetchOrders(array $params = []): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API not configured'];
        }

        $queryParams = http_build_query([
            'per_page' => $params['limit'] ?? 50,
            'after' => $params['created_at_min'] ?? now()->subDays(30)->toIso8601String(),
        ]);

        $response = $this->makeRequest(
            'GET',
            "{$this->baseUrl}/orders?{$queryParams}",
            [],
            $this->getHeaders()
        );

        return [
            'success' => $response['success'],
            'orders' => $response['data'] ?? [],
        ];
    }

    public function getProductStatus(ProductPlatformListing $listing): array
    {
        if (!$this->isConfigured() || !$listing->platform_product_id) {
            return ['success' => false, 'message' => 'API not configured or product not synced'];
        }

        $response = $this->makeRequest(
            'GET',
            "{$this->baseUrl}/products/{$listing->platform_product_id}",
            [],
            $this->getHeaders()
        );

        if ($response['success']) {
            return [
                'success' => true,
                'status' => $response['data']['status'] ?? 'unknown',
                'data' => $response['data'],
            ];
        }

        return ['success' => false, 'message' => 'Failed to fetch product status'];
    }

    protected function getHeaders(): array
    {
        $credentials = base64_encode("{$this->config['consumer_key']}:{$this->config['consumer_secret']}");
        return [
            "Authorization: Basic {$credentials}",
        ];
    }
}
