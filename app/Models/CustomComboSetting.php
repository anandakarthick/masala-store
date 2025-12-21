<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustomComboSetting extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'min_products',
        'max_products',
        'discount_type',
        'discount_value',
        'combo_price',
        'allowed_categories',
        'allowed_products',
        'excluded_products',
        'allow_same_product',
        'allow_variants',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'allowed_categories' => 'array',
        'allowed_products' => 'array',
        'excluded_products' => 'array',
        'allow_same_product' => 'boolean',
        'allow_variants' => 'boolean',
        'is_active' => 'boolean',
        'combo_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($combo) {
            if (empty($combo->slug)) {
                $combo->slug = Str::slug($combo->name);
            }
        });
    }

    public function comboCarts(): HasMany
    {
        return $this->hasMany(CustomComboCart::class, 'combo_setting_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get eligible products for this combo
     */
    public function getEligibleProducts()
    {
        $query = Product::active()->with(['category', 'primaryImage', 'activeVariants', 'defaultVariant']);

        // Filter by allowed categories
        if (!empty($this->allowed_categories)) {
            $query->whereIn('category_id', $this->allowed_categories);
        }

        // Filter by allowed products (whitelist)
        if (!empty($this->allowed_products)) {
            $query->whereIn('id', $this->allowed_products);
        }

        // Exclude specific products
        if (!empty($this->excluded_products)) {
            $query->whereNotIn('id', $this->excluded_products);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Check if a product is eligible for this combo
     */
    public function isProductEligible(Product $product): bool
    {
        // Check excluded products
        if (!empty($this->excluded_products) && in_array($product->id, $this->excluded_products)) {
            return false;
        }

        // Check allowed products (whitelist)
        if (!empty($this->allowed_products)) {
            return in_array($product->id, $this->allowed_products);
        }

        // Check allowed categories
        if (!empty($this->allowed_categories)) {
            return in_array($product->category_id, $this->allowed_categories);
        }

        return true;
    }

    /**
     * Calculate discount for a given total price
     */
    public function calculateDiscount(float $totalPrice, int $itemCount): float
    {
        // If fixed combo price is set, discount is the difference
        if ($this->combo_price) {
            return max(0, $totalPrice - $this->combo_price);
        }

        switch ($this->discount_type) {
            case 'percentage':
                return $totalPrice * ($this->discount_value / 100);
            case 'fixed':
                return $this->discount_value;
            case 'per_item':
                return $this->discount_value * $itemCount;
            default:
                return 0;
        }
    }

    /**
     * Get final price for combo
     */
    public function calculateFinalPrice(float $totalPrice, int $itemCount): float
    {
        if ($this->combo_price) {
            return $this->combo_price;
        }

        $discount = $this->calculateDiscount($totalPrice, $itemCount);
        return max(0, $totalPrice - $discount);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Get discount display text
     */
    public function getDiscountDisplayAttribute(): string
    {
        if ($this->combo_price) {
            return 'Fixed Price: ₹' . number_format($this->combo_price, 0);
        }

        switch ($this->discount_type) {
            case 'percentage':
                return $this->discount_value . '% OFF';
            case 'fixed':
                return '₹' . number_format($this->discount_value, 0) . ' OFF';
            case 'per_item':
                return '₹' . number_format($this->discount_value, 0) . ' OFF per item';
            default:
                return '';
        }
    }
}
