<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPlatformListing extends Model
{
    protected $fillable = [
        'product_id',
        'selling_platform_id',
        'platform_product_id',
        'platform_sku',
        'listing_url',
        'platform_price',
        'platform_mrp',
        'status',
        'platform_stock',
        'rejection_reason',
        'listed_at',
        'last_synced_at',
        'platform_data',
    ];

    protected $casts = [
        'platform_price' => 'decimal:2',
        'platform_mrp' => 'decimal:2',
        'listed_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'platform_data' => 'array',
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the platform
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(SellingPlatform::class, 'selling_platform_id');
    }

    /**
     * Active listings scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'active' => 'green',
            'inactive' => 'red',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending Approval',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'rejected' => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    /**
     * Calculate commission
     */
    public function getEstimatedCommissionAttribute(): float
    {
        if (!$this->platform_price) return 0;
        return $this->platform->calculateCommission($this->platform_price);
    }

    /**
     * Get net earnings after commission
     */
    public function getNetEarningsAttribute(): float
    {
        if (!$this->platform_price) return 0;
        return $this->platform_price - $this->estimated_commission;
    }
}
