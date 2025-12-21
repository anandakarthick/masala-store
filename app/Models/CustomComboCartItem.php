<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomComboCartItem extends Model
{
    protected $fillable = [
        'custom_combo_cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function comboCart(): BelongsTo
    {
        return $this->belongsTo(CustomComboCart::class, 'custom_combo_cart_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get item name
     */
    public function getItemNameAttribute(): string
    {
        $name = $this->product->name;
        if ($this->variant_id && $this->variant) {
            $name .= ' - ' . $this->variant->name;
        }
        return $name;
    }

    /**
     * Get item total
     */
    public function getTotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get stock quantity
     */
    public function getStockQuantityAttribute(): int
    {
        if ($this->variant_id && $this->variant) {
            return $this->variant->stock_quantity;
        }
        return $this->product->stock_quantity;
    }

    /**
     * Get product image
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->variant_id && $this->variant && $this->variant->variant_image) {
            return asset('storage/' . $this->variant->variant_image);
        }
        return $this->product->primary_image_url;
    }
}
