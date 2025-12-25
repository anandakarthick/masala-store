<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_pincode',
        'order_type',
        'order_source',
        'subtotal',
        'discount_amount',
        'first_time_discount_applied',
        'wallet_amount_used',
        'gst_amount',
        'shipping_charge',
        'total_amount',
        'payment_method',
        'payment_status',
        'transaction_id',
        'status',
        'delivery_partner',
        'tracking_number',
        'delivery_attachments',
        'delivery_notes',
        'expected_delivery_date',
        'delivered_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'customer_notes',
        'admin_notes',
        'is_seen_by_admin',
        'seen_at',
        'invoice_number',
        'invoice_generated_at',
        'review_requested_at',
        'review_token',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'first_time_discount_applied' => 'decimal:2',
        'wallet_amount_used' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expected_delivery_date' => 'date',
        'delivered_at' => 'date',
        'cancelled_at' => 'datetime',
        'invoice_generated_at' => 'datetime',
        'seen_at' => 'datetime',
        'is_seen_by_admin' => 'boolean',
        'review_requested_at' => 'datetime',
        'delivery_attachments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Custom combos in this order
     */
    public function customCombos(): HasMany
    {
        return $this->hasMany(OrderCustomCombo::class);
    }

    /**
     * Reviews for this order
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Mark order as seen by admin
     */
    public function markAsSeen(): void
    {
        if (!$this->is_seen_by_admin) {
            $this->update([
                'is_seen_by_admin' => true,
                'seen_at' => now(),
            ]);
        }
    }

    /**
     * Scope for unseen orders
     */
    public function scopeUnseen($query)
    {
        return $query->where('is_seen_by_admin', false);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->whereIn('status', ['confirmed', 'processing', 'packed']);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function getFullShippingAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->shipping_address,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_pincode,
        ]));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'indigo',
            'packed' => 'purple',
            'shipped' => 'cyan',
            'delivered' => 'green',
            'cancelled' => 'red',
            'returned' => 'gray',
            default => 'gray',
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'failed' => 'red',
            'refunded' => 'gray',
            default => 'gray',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if customer can cancel this order
     */
    public function canBeCancelledByCustomer(): bool
    {
        return $this->status === 'pending';
    }

    public function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $lastInvoice = self::whereYear('invoice_generated_at', $year)
            ->whereMonth('invoice_generated_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if order can be reviewed
     */
    public function canBeReviewed(): bool
    {
        return $this->status === 'delivered' && $this->user_id !== null;
    }

    /**
     * Check if order has been fully reviewed
     */
    public function isFullyReviewed(): bool
    {
        if (!$this->canBeReviewed()) {
            return false;
        }
        
        $reviewedItemIds = $this->reviews()->pluck('order_item_id')->toArray();
        $orderItemIds = $this->items()->pluck('id')->toArray();
        
        return empty(array_diff($orderItemIds, $reviewedItemIds));
    }

    /**
     * Get items that haven't been reviewed yet
     */
    public function getUnreviewedItems()
    {
        $reviewedItemIds = $this->reviews()->pluck('order_item_id')->toArray();
        return $this->items()->whereNotIn('id', $reviewedItemIds)->get();
    }

    /**
     * Generate review token for email link
     */
    public function generateReviewToken(): string
    {
        $token = Str::random(64);
        $this->update(['review_token' => $token]);
        return $token;
    }

    /**
     * Check if review has been requested
     */
    public function hasReviewBeenRequested(): bool
    {
        return $this->review_requested_at !== null;
    }

    /**
     * Get delivery attachment URLs
     */
    public function getDeliveryAttachmentUrls(): array
    {
        if (empty($this->delivery_attachments)) {
            return [];
        }

        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $this->delivery_attachments);
    }

    /**
     * Check if order has delivery attachments
     */
    public function hasDeliveryAttachments(): bool
    {
        return !empty($this->delivery_attachments);
    }
}
