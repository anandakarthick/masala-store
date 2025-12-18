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

    public function clear(): bool
    {
        return $this->items()->delete() > 0;
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->count();
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getGstAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            $price = $item->unit_price * $item->quantity;
            return $item->product->calculateGst($price);
        });
    }

    public function mergeGuestCart(): void
    {
        if (!auth()->check()) return;

        $guestCart = self::where('session_id', session()->getId())
            ->whereNull('user_id')
            ->first();

        if (!$guestCart) return;

        foreach ($guestCart->items as $item) {
            $this->addItem($item->product, $item->quantity, $item->variant_id);
        }

        $guestCart->delete();
    }
}
