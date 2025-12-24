<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'display_name',
        'description',
        'icon',
        'instructions',
        'is_active',
        'is_online',
        'min_order_amount',
        'max_order_amount',
        'extra_charge',
        'extra_charge_type',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'min_order_amount' => 'decimal:2',
        'max_order_amount' => 'decimal:2',
        'extra_charge' => 'decimal:2',
        'settings' => 'array',
    ];

    /**
     * Get active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get payment methods available for a given order amount
     */
    public function scopeAvailableForAmount($query, $amount)
    {
        return $query->active()
            ->where('min_order_amount', '<=', $amount)
            ->where(function ($q) use ($amount) {
                $q->whereNull('max_order_amount')
                  ->orWhere('max_order_amount', '>=', $amount);
            });
    }

    /**
     * Calculate extra charge for given amount
     */
    public function calculateExtraCharge($amount): float
    {
        if ($this->extra_charge <= 0) {
            return 0;
        }

        if ($this->extra_charge_type === 'percentage') {
            return round(($amount * $this->extra_charge) / 100, 2);
        }

        return (float) $this->extra_charge;
    }

    /**
     * Get setting value
     */
    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Check if this is Razorpay
     */
    public function isRazorpay(): bool
    {
        return $this->code === 'razorpay';
    }

    /**
     * Check if this is COD
     */
    public function isCod(): bool
    {
        return $this->code === 'cod';
    }

    /**
     * Check if payment method is available for given amount
     */
    public function isAvailableForAmount($amount): bool
    {
        if ($this->min_order_amount > 0 && $amount < $this->min_order_amount) {
            return false;
        }

        if ($this->max_order_amount > 0 && $amount > $this->max_order_amount) {
            return false;
        }

        return true;
    }
}
