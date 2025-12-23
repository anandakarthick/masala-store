<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'source',
        'description',
        'reference_order_id',
        'reference_user_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * User who owns this transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reference order (if applicable)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'reference_order_id');
    }

    /**
     * Reference user (referred user for referral rewards)
     */
    public function referenceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reference_user_id');
    }

    /**
     * Scope for credits
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope for debits
     */
    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    /**
     * Scope for referral transactions
     */
    public function scopeReferral($query)
    {
        return $query->where('source', 'referral');
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return $this->type === 'credit' ? 'green' : 'red';
    }

    /**
     * Get formatted amount with sign
     */
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->type === 'credit' ? '+' : '-';
        return $sign . '₹' . number_format($this->amount, 2);
    }

    /**
     * Get source label
     */
    public function getSourceLabelAttribute(): string
    {
        return match($this->source) {
            'referral' => 'Referral Reward',
            'order' => 'Order Payment',
            'admin' => 'Admin Adjustment',
            'refund' => 'Refund',
            default => ucfirst($this->source),
        };
    }
}
