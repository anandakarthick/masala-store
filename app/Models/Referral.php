<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'reward_amount',
        'orders_rewarded',
        'first_order_id',
        'completed_at',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'orders_rewarded' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * The referrer (Customer A who shared the code)
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * The referred user (Customer B who used the code)
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * First order placed by referred user
     */
    public function firstOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'first_order_id');
    }

    /**
     * Scope for pending referrals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed referrals
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'completed' => 'green',
            'expired' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Check if referral can be rewarded
     */
    public function canBeRewarded(): bool
    {
        return $this->status === 'pending' || $this->status === 'completed';
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        if ($this->status === 'pending') {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Increment orders rewarded count
     */
    public function incrementOrdersRewarded(float $amount): void
    {
        $this->increment('orders_rewarded');
        $this->increment('reward_amount', $amount);
    }
}
