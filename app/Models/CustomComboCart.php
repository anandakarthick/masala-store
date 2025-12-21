<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomComboCart extends Model
{
    protected $table = 'custom_combo_carts';

    protected $fillable = [
        'cart_id',
        'combo_setting_id',
        'combo_name',
        'quantity',
        'calculated_price',
        'discount_amount',
        'final_price',
    ];

    protected $casts = [
        'calculated_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function comboSetting(): BelongsTo
    {
        return $this->belongsTo(CustomComboSetting::class, 'combo_setting_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CustomComboCartItem::class);
    }

    /**
     * Add a product to this combo
     */
    public function addProduct(Product $product, int $quantity = 1, ?int $variantId = null): CustomComboCartItem
    {
        $setting = $this->comboSetting;

        // Check if same product exists (if not allowed)
        if (!$setting->allow_same_product) {
            $existing = $this->items()
                ->where('product_id', $product->id)
                ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
                ->when(!$variantId, fn($q) => $q->whereNull('variant_id'))
                ->first();

            if ($existing) {
                // Update quantity instead of adding new
                $existing->update(['quantity' => $existing->quantity + $quantity]);
                $this->recalculatePrices();
                return $existing->fresh();
            }
        }

        // Get unit price
        $unitPrice = $variantId 
            ? ProductVariant::find($variantId)->effective_price 
            : $product->effective_price;

        $item = $this->items()->create([
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);

        $this->recalculatePrices();

        return $item;
    }

    /**
     * Remove a product from this combo
     */
    public function removeProduct(int $itemId): bool
    {
        $deleted = $this->items()->where('id', $itemId)->delete() > 0;
        
        if ($deleted) {
            $this->recalculatePrices();
        }

        return $deleted;
    }

    /**
     * Update product quantity in combo
     */
    public function updateProductQuantity(int $itemId, int $quantity): ?CustomComboCartItem
    {
        $item = $this->items()->find($itemId);

        if (!$item) {
            return null;
        }

        if ($quantity <= 0) {
            $item->delete();
            $this->recalculatePrices();
            return null;
        }

        $item->update(['quantity' => $quantity]);
        $this->recalculatePrices();

        return $item->fresh();
    }

    /**
     * Recalculate combo prices
     */
    public function recalculatePrices(): void
    {
        $items = $this->items()->with('product', 'variant')->get();
        $setting = $this->comboSetting;

        $totalPrice = $items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        $totalItems = $items->sum('quantity');
        $discount = $setting->calculateDiscount($totalPrice, $totalItems);
        $finalPrice = $setting->calculateFinalPrice($totalPrice, $totalItems);

        $this->update([
            'calculated_price' => $totalPrice,
            'discount_amount' => $discount,
            'final_price' => $finalPrice,
        ]);
    }

    /**
     * Get total items count
     */
    public function getTotalItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if combo meets minimum requirement
     */
    public function meetsMinimumRequirement(): bool
    {
        return $this->total_items_count >= $this->comboSetting->min_products;
    }

    /**
     * Check if combo is at maximum capacity
     */
    public function isAtMaxCapacity(): bool
    {
        return $this->total_items_count >= $this->comboSetting->max_products;
    }

    /**
     * Get remaining slots
     */
    public function getRemainingSlots(): int
    {
        return max(0, $this->comboSetting->max_products - $this->total_items_count);
    }

    /**
     * Get slots needed to meet minimum
     */
    public function getSlotsNeededAttribute(): int
    {
        return max(0, $this->comboSetting->min_products - $this->total_items_count);
    }

    /**
     * Get savings display
     */
    public function getSavingsAttribute(): float
    {
        return $this->discount_amount * $this->quantity;
    }

    /**
     * Get total for cart
     */
    public function getCartTotalAttribute(): float
    {
        return $this->final_price * $this->quantity;
    }
}
