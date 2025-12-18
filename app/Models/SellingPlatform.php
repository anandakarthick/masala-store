<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellingPlatform extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'website_url',
        'seller_portal_url',
        'description',
        'platform_type',
        'commission_percentage',
        'is_active',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_percentage' => 'decimal:2',
        'settings' => 'array',
    ];

    /**
     * Get product listings for this platform
     */
    public function productListings(): HasMany
    {
        return $this->hasMany(ProductPlatformListing::class);
    }

    /**
     * Get orders from this platform
     */
    public function platformOrders(): HasMany
    {
        return $this->hasMany(PlatformOrder::class);
    }

    /**
     * Active platforms scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get setting value
     */
    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Calculate commission for an amount
     */
    public function calculateCommission($amount): float
    {
        return round(($amount * $this->commission_percentage) / 100, 2);
    }

    /**
     * Get platform type label
     */
    public function getPlatformTypeLabelAttribute(): string
    {
        return match($this->platform_type) {
            'marketplace' => 'Marketplace',
            'b2b' => 'B2B Platform',
            'social_commerce' => 'Social Commerce',
            'own_store' => 'Own Store',
            default => ucfirst($this->platform_type),
        };
    }

    /**
     * Get active listings count
     */
    public function getActiveListingsCountAttribute(): int
    {
        return $this->productListings()->where('status', 'active')->count();
    }

    /**
     * Get total listings count
     */
    public function getTotalListingsCountAttribute(): int
    {
        return $this->productListings()->count();
    }
}
