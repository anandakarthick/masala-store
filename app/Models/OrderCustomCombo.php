<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderCustomCombo extends Model
{
    protected $fillable = [
        'order_id',
        'combo_setting_id',
        'combo_name',
        'quantity',
        'original_price',
        'discount_amount',
        'final_price',
        'items_snapshot',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'items_snapshot' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function comboSetting(): BelongsTo
    {
        return $this->belongsTo(CustomComboSetting::class, 'combo_setting_id');
    }

    /**
     * Get total amount
     */
    public function getTotalAttribute(): float
    {
        return $this->final_price * $this->quantity;
    }

    /**
     * Get total savings
     */
    public function getSavingsAttribute(): float
    {
        return $this->discount_amount * $this->quantity;
    }

    /**
     * Get items from snapshot
     */
    public function getItemsAttribute(): array
    {
        return $this->items_snapshot ?? [];
    }
}
