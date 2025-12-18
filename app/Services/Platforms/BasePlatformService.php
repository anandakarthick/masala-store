<?php

namespace App\Services\Platforms;

use App\Models\ProductPlatformListing;
use App\Models\SellingPlatform;

abstract class BasePlatformService
{
    protected SellingPlatform $platform;
    protected array $config;

    public function __construct(SellingPlatform $platform)
    {
        $this->platform = $platform;
        $this->config = $platform->settings ?? [];
    }

    /**
     * Check if API is configured
     */
    abstract public function isConfigured(): bool;

    /**
     * Test API connection
     */
    abstract public function testConnection(): array;

    /**
     * Push product to platform
     */
    abstract public function createProduct(ProductPlatformListing $listing): array;

    /**
     * Update product on platform
     */
    abstract public function updateProduct(ProductPlatformListing $listing): array;

    /**
     * Update stock/inventory on platform
     */
    abstract public function updateStock(ProductPlatformListing $listing): array;

    /**
     * Update price on platform
     */
    abstract public function updatePrice(ProductPlatformListing $listing): array;

    /**
     * Delete/deactivate product on platform
     */
    abstract public function deleteProduct(ProductPlatformListing $listing): array;

    /**
     * Fetch orders from platform
     */
    abstract public function fetchOrders(array $params = []): array;

    /**
     * Get product status from platform
     */
    abstract public function getProductStatus(ProductPlatformListing $listing): array;

    /**
     * Helper to make API request
     */
    protected function makeRequest(string $method, string $url, array $data = [], array $headers = []): array
    {
        $ch = curl_init();
        
        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        
        $headers = array_merge($defaultHeaders, $headers);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode,
            ];
        }
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'data' => json_decode($response, true),
            'raw' => $response,
        ];
    }

    /**
     * Log API activity
     */
    protected function logActivity(string $action, array $data, array $response): void
    {
        \Log::info("Platform API: {$this->platform->name} - {$action}", [
            'request' => $data,
            'response' => $response,
        ]);
    }
}
