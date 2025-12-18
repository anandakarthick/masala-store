<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'batch_number',
        'reference',
        'notes',
        'created_by',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'in' => 'green',
            'out' => 'red',
            'adjustment' => 'yellow',
            default => 'gray',
        };
    }

    public static function recordMovement(
        Product $product,
        string $type,
        int $quantity,
        ?string $reference = null,
        ?string $notes = null,
        ?int $userId = null
    ): self {
        $stockBefore = $product->stock_quantity;
        
        if ($type === 'in') {
            $product->increment('stock_quantity', $quantity);
        } elseif ($type === 'out') {
            $product->decrement('stock_quantity', $quantity);
        } else {
            $product->update(['stock_quantity' => $quantity]);
        }

        return self::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $product->fresh()->stock_quantity,
            'reference' => $reference,
            'notes' => $notes,
            'created_by' => $userId ?? auth()->id(),
        ]);
    }
}
