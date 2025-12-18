<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantAttribute extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'display_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get attribute values
     */
    public function values(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class)->orderBy('sort_order');
    }

    /**
     * Active values
     */
    public function activeValues(): HasMany
    {
        return $this->hasMany(VariantAttributeValue::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Active scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get by code
     */
    public static function getByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    /**
     * Check if this is a color attribute
     */
    public function isColor(): bool
    {
        return $this->type === 'color';
    }
}
