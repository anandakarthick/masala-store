<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'wholesale_price',
        'discount_price',
        'weight',
        'unit',
        'stock_quantity',
        'low_stock_threshold',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'hsn_code',
        'gst_percentage',
        'meta_title',
        'meta_description',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now());
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute(): float
    {
        if (!$this->discount_price || $this->discount_price >= $this->price) {
            return 0;
        }
        return round((($this->price - $this->discount_price) / $this->price) * 100, 2);
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return asset('storage/' . $primary->image_path);
        }
        $first = $this->images->first();
        return $first ? asset('storage/' . $first->image_path) : null;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function calculateGst(float $amount): float
    {
        return $amount * ($this->gst_percentage / 100);
    }

    public function getWeightDisplayAttribute(): string
    {
        return $this->weight . ' ' . $this->unit;
    }
}
