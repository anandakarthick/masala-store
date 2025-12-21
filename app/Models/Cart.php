<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Custom combos in cart
     */
    public function customCombos(): HasMany
    {
        return $this->hasMany(CustomComboCart::class);
    }

    public static function getCart(): self
    {
        if (auth()->check()) {
            return self::firstOrCreate(['user_id' => auth()->id()]);
        }

        $sessionId = session()->getId();
        return self::firstOrCreate(['session_id' => $sessionId]);
    }

    public function addItem(Product $product, int $quantity = 1, ?int $variantId = null): CartItem
    {
        // Find existing item with same product and variant
        $query = $this->items()->where('product_id', $product->id);
        
        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }
        
        $item = $query->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            return $item->fresh();
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'quantity' => $quantity,
        ]);
    }

    public function updateItemQuantity(int $productId, int $quantity, ?int $variantId = null): ?CartItem
    {
        $query = $this->items()->where('product_id', $productId);
        
        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }
        
        $item = $query->first();
        
        if ($item) {
            if ($quantity <= 0) {
                $item->delete();
                return null;
            }
            $item->update(['quantity' => $quantity]);
            return $item->fresh();
        }

        return null;
    }

    public function removeItem(int $productId, ?int $variantId = null): bool
    {
        $query = $this->items()->where('product_id', $productId);
        
        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }
        
        return $query->delete() > 0;
    }

    /**
     * Remove a custom combo from cart
     */
    public function removeCombo(int $comboCartId): bool
    {
        return $this->customCombos()->where('id', $comboCartId)->delete() > 0;
    }

    /**
     * Update combo quantity
     */
    public function updateComboQuantity(int $comboCartId, int $quantity): ?CustomComboCart
    {
        $combo = $this->customCombos()->find($comboCartId);

        if (!$combo) {
            return null;
        }

        if ($quantity <= 0) {
            $combo->delete();
            return null;
        }

        $combo->update(['quantity' => $quantity]);
        return $combo->fresh();
    }

    public function clear(): bool
    {
        $this->items()->delete();
        $this->customCombos()->delete();
        return true;
    }

    /**
     * Get subtotal for regular items
     */
    public function getItemsSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }

    /**
     * Get subtotal for custom combos
     */
    public function getCombosSubtotalAttribute(): float
    {
        return $this->customCombos->sum(function ($combo) {
            return $combo->final_price * $combo->quantity;
        });
    }

    /**
     * Get total combo savings
     */
    public function getComboSavingsAttribute(): float
    {
        return $this->customCombos->sum(function ($combo) {
            return $combo->discount_amount * $combo->quantity;
        });
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items_subtotal + $this->combos_subtotal;
    }

    public function getTotalItemsAttribute(): int
    {
        $regularItems = $this->items->count();
        $comboItems = $this->customCombos->count();
        return $regularItems + $comboItems;
    }

    public function getTotalQuantityAttribute(): int
    {
        $regularQty = $this->items->sum('quantity');
        $comboQty = $this->customCombos->sum('quantity');
        return $regularQty + $comboQty;
    }

    public function getGstAmountAttribute(): float
    {
        $regularGst = $this->items->sum(function ($item) {
            $price = $item->unit_price * $item->quantity;
            return $item->product->calculateGst($price);
        });

        // For combos, calculate GST based on individual items
        $comboGst = 0;
        foreach ($this->customCombos as $combo) {
            foreach ($combo->items as $item) {
                $price = $item->unit_price * $item->quantity;
                $comboGst += $item->product->calculateGst($price);
            }
            // Adjust for quantity of combo
            $comboGst *= $combo->quantity;
        }

        return $regularGst + $comboGst;
    }

    public function mergeGuestCart(): void
    {
        if (!auth()->check()) return;

        $guestCart = self::where('session_id', session()->getId())
            ->whereNull('user_id')
            ->first();

        if (!$guestCart) return;

        // Merge regular items
        foreach ($guestCart->items as $item) {
            $this->addItem($item->product, $item->quantity, $item->variant_id);
        }

        // Merge custom combos
        foreach ($guestCart->customCombos as $combo) {
            $newCombo = $this->customCombos()->create([
                'combo_setting_id' => $combo->combo_setting_id,
                'combo_name' => $combo->combo_name,
                'quantity' => $combo->quantity,
                'calculated_price' => $combo->calculated_price,
                'discount_amount' => $combo->discount_amount,
                'final_price' => $combo->final_price,
            ]);

            foreach ($combo->items as $item) {
                $newCombo->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);
            }
        }

        $guestCart->delete();
    }
}
