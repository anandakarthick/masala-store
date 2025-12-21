<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'product_id',
        'variant_id',
        'product_name',
        'product_sku',
        'variant_name',
        'description',
        'quantity',
        'unit_price',
        'discount_percent',
        'gst_percent',
        'gst_amount',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateTotals();
        });

        static::saved(function ($item) {
            $item->estimate->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->estimate->calculateTotals();
        });
    }

    // Relationships
    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Calculate totals
    public function calculateTotals(): void
    {
        $basePrice = $this->unit_price * $this->quantity;
        
        // Apply discount if any
        if ($this->discount_percent > 0) {
            $basePrice = $basePrice - ($basePrice * $this->discount_percent / 100);
        }

        // Calculate GST
        $this->gst_amount = ($basePrice * $this->gst_percent) / 100;
        $this->total_price = $basePrice + $this->gst_amount;
    }
}
