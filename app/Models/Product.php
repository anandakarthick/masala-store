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
        'has_variants',
        'is_combo',
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
        'has_variants' => 'boolean',
        'is_combo' => 'boolean',
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

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('price');
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true)->orderBy('sort_order')->orderBy('price');
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    /**
     * Items included in this combo/pack
     */
    public function comboItems(): HasMany
    {
        return $this->hasMany(ComboItem::class, 'combo_product_id')->orderBy('sort_order');
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

    public function scopeCombo($query)
    {
        return $query->where('is_combo', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('has_variants', false)->where('stock_quantity', '>', 0);
        })->orWhere(function($q) {
            $q->where('has_variants', true)->whereHas('variants', function($vq) {
                $vq->where('is_active', true)->where('stock_quantity', '>', 0);
            });
        });
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

    // Get effective price (from default variant if has variants)
    public function getEffectivePriceAttribute(): float
    {
        if ($this->has_variants) {
            $defaultVariant = $this->defaultVariant ?? $this->activeVariants->first();
            return $defaultVariant ? $defaultVariant->effective_price : ($this->discount_price ?? $this->price);
        }
        return $this->discount_price ?? $this->price;
    }

    // Get price range for products with variants
    public function getPriceRangeAttribute(): array
    {
        if (!$this->has_variants || $this->activeVariants->isEmpty()) {
            return ['min' => $this->effective_price, 'max' => $this->effective_price];
        }
        
        $prices = $this->activeVariants->pluck('effective_price');
        return [
            'min' => $prices->min(),
            'max' => $prices->max(),
        ];
    }

    public function getPriceDisplayAttribute(): string
    {
        if ($this->has_variants && $this->activeVariants->count() > 1) {
            $range = $this->price_range;
            if ($range['min'] != $range['max']) {
                return '₹' . number_format($range['min'], 2) . ' - ₹' . number_format($range['max'], 2);
            }
        }
        return '₹' . number_format($this->effective_price, 2);
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
        if ($this->has_variants) {
            return $this->activeVariants->every(fn($v) => $v->isLowStock() || $v->isOutOfStock());
        }
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        if ($this->has_variants) {
            return $this->activeVariants->every(fn($v) => $v->isOutOfStock());
        }
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
        if ($this->has_variants) {
            $variants = $this->activeVariants;
            if ($variants->count() > 1) {
                return $variants->first()->weight_display . ' - ' . $variants->last()->weight_display;
            } elseif ($variants->count() == 1) {
                return $variants->first()->weight_display;
            }
        }
        return $this->weight . ' ' . $this->unit;
    }

    // Get total stock across all variants
    public function getTotalStockAttribute(): int
    {
        if ($this->has_variants) {
            return $this->activeVariants->sum('stock_quantity');
        }
        return $this->stock_quantity;
    }

    // Check if product is a combo/pack
    public function isCombo(): bool
    {
        return $this->is_combo || $this->comboItems->count() > 0;
    }
}
