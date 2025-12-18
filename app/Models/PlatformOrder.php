<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformOrder extends Model
{
    protected $fillable = [
        'selling_platform_id',
        'order_id',
        'platform_order_id',
        'platform_order_status',
        'platform_order_amount',
        'commission_amount',
        'settlement_amount',
        'customer_name',
        'shipping_address',
        'order_data',
        'platform_order_date',
    ];

    protected $casts = [
        'platform_order_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'settlement_amount' => 'decimal:2',
        'order_data' => 'array',
        'platform_order_date' => 'datetime',
    ];

    /**
     * Get the platform
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(SellingPlatform::class, 'selling_platform_id');
    }

    /**
     * Get the linked internal order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
