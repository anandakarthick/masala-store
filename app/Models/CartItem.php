<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'variant_id', 'quantity'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getUnitPriceAttribute(): float
    {
        if ($this->variant_id && $this->variant) {
            return $this->variant->effective_price;
        }
        return $this->product->effective_price;
    }

    public function getTotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    public function getItemNameAttribute(): string
    {
        $name = $this->product->name;
        if ($this->variant_id && $this->variant) {
            $name .= ' - ' . $this->variant->name;
        }
        return $name;
    }

    public function getStockQuantityAttribute(): int
    {
        if ($this->variant_id && $this->variant) {
            return $this->variant->stock_quantity;
        }
        return $this->product->stock_quantity;
    }
}
