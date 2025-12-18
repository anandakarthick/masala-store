<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantAttributeValue extends Model
{
    protected $fillable = [
        'variant_attribute_id',
        'value',
        'display_value',
        'color_code',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent attribute
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(VariantAttribute::class, 'variant_attribute_id');
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->display_value ?? $this->value;
    }

    /**
     * Active scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
