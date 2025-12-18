<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'weight',
        'unit',
        'price',
        'discount_price',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'is_default',
        'sort_order',
        // Clothing attributes
        'size',
        'color',
        'color_code',
        'brand',
        'material',
        'style',
        'pattern',
        'fit',
        'sleeve_type',
        'neck_type',
        'occasion',
        'attributes',
        'variant_image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'attributes' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->discount_price || $this->discount_price >= $this->price) {
            return 0;
        }
        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getWeightDisplayAttribute(): string
    {
        if (!$this->weight) return '';
        return $this->weight . ' ' . $this->unit;
    }

    /**
     * Get variant display name with attributes
     */
    public function getDisplayNameAttribute(): string
    {
        $parts = [];
        
        if ($this->size) $parts[] = $this->size;
        if ($this->color) $parts[] = $this->color;
        if ($this->name && !in_array($this->name, $parts)) $parts[] = $this->name;
        
        return implode(' / ', $parts) ?: $this->name;
    }

    /**
     * Get short display (size + color)
     */
    public function getShortDisplayAttribute(): string
    {
        $parts = [];
        if ($this->size) $parts[] = $this->size;
        if ($this->color) $parts[] = $this->color;
        return implode(' - ', $parts) ?: $this->name;
    }

    /**
     * Get variant image URL
     */
    public function getVariantImageUrlAttribute(): ?string
    {
        if ($this->variant_image) {
            return asset('storage/' . $this->variant_image);
        }
        return $this->product->primary_image_url ?? null;
    }

    /**
     * Check if variant has clothing attributes
     */
    public function hasClothingAttributes(): bool
    {
        return $this->size || $this->color || $this->material || $this->brand;
    }

    /**
     * Get all attribute values as array
     */
    public function getAttributeValuesAttribute(): array
    {
        $attrs = [];
        
        if ($this->size) $attrs['Size'] = $this->size;
        if ($this->color) $attrs['Color'] = $this->color;
        if ($this->brand) $attrs['Brand'] = $this->brand;
        if ($this->material) $attrs['Material'] = $this->material;
        if ($this->pattern) $attrs['Pattern'] = $this->pattern;
        if ($this->fit) $attrs['Fit'] = $this->fit;
        if ($this->sleeve_type) $attrs['Sleeve'] = $this->sleeve_type;
        if ($this->neck_type) $attrs['Neck'] = $this->neck_type;
        if ($this->occasion) $attrs['Occasion'] = $this->occasion;
        if ($this->weight) $attrs['Weight'] = $this->weight_display;
        
        // Merge custom attributes
        if ($this->attributes) {
            $attrs = array_merge($attrs, $this->attributes);
        }
        
        return $attrs;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Filter by size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Filter by color
     */
    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }
}
