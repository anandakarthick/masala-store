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

    public function addItem(Product $product, int $quantity = 1): CartItem
    {
        $item = $this->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            return $item->fresh();
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }

    public function updateItemQuantity(int $productId, int $quantity): ?CartItem
    {
        $item = $this->items()->where('product_id', $productId)->first();
        
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

    public function removeItem(int $productId): bool
    {
        return $this->items()->where('product_id', $productId)->delete() > 0;
    }

    public function clear(): bool
    {
        return $this->items()->delete() > 0;
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->product->effective_price * $item->quantity;
        });
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getGstAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            $price = $item->product->effective_price * $item->quantity;
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
            $this->addItem($item->product, $item->quantity);
        }

        $guestCart->delete();
    }
}
