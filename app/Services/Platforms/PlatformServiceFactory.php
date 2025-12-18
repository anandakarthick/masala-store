<?php

namespace App\Services\Platforms;

use App\Models\SellingPlatform;

class PlatformServiceFactory
{
    /**
     * Get the appropriate service for a platform
     */
    public static function make(SellingPlatform $platform): ?BasePlatformService
    {
        return match($platform->code) {
            'shopify' => new ShopifyService($platform),
            'woocommerce' => new WooCommerceService($platform),
            // Add more platforms as needed
            // 'amazon' => new AmazonService($platform),
            // 'flipkart' => new FlipkartService($platform),
            default => new GenericPlatformService($platform),
        };
    }

    /**
     * Get all available platform services
     */
    public static function getAvailableServices(): array
    {
        return [
            'shopify' => [
                'name' => 'Shopify',
                'class' => ShopifyService::class,
                'has_api' => true,
            ],
            'woocommerce' => [
                'name' => 'WooCommerce',
                'class' => WooCommerceService::class,
                'has_api' => true,
            ],
            'amazon' => [
                'name' => 'Amazon',
                'class' => null,
                'has_api' => false,
                'note' => 'Amazon SP-API integration coming soon',
            ],
            'flipkart' => [
                'name' => 'Flipkart',
                'class' => null,
                'has_api' => false,
                'note' => 'Flipkart API integration coming soon',
            ],
        ];
    }
}
