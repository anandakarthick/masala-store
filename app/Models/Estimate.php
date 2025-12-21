<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_city',
        'customer_state',
        'customer_pincode',
        'estimate_date',
        'valid_until',
        'subject',
        'notes',
        'terms',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_value',
        'gst_amount',
        'shipping_charge',
        'total_amount',
        'status',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'rejected_at',
        'converted_order_id',
        'created_by',
        'admin_notes',
    ];

    protected $casts = [
        'estimate_date' => 'date',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($estimate) {
            if (empty($estimate->estimate_number)) {
                $estimate->estimate_number = self::generateEstimateNumber();
            }
        });
    }

    public static function generateEstimateNumber(): string
    {
        $prefix = 'EST-' . date('Ym') . '-';
        $lastEstimate = self::where('estimate_number', 'like', $prefix . '%')
            ->orderBy('estimate_number', 'desc')
            ->first();

        if ($lastEstimate) {
            $lastNumber = (int) substr($lastEstimate->estimate_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    // Helpers
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Draft</span>',
            'sent' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-600">Sent</span>',
            'viewed' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-600">Viewed</span>',
            'accepted' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">Accepted</span>',
            'rejected' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Rejected</span>',
            'expired' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-600">Expired</span>',
            'converted' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Converted</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">' . ucfirst($this->status) . '</span>',
        };
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'sent', 'viewed']);
    }

    public function canBeConverted(): bool
    {
        return in_array($this->status, ['accepted']) && !$this->converted_order_id;
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        $gstAmount = $this->items->sum('gst_amount');

        // Apply discount
        $discountAmount = 0;
        if ($this->discount_value > 0) {
            if ($this->discount_type === 'percentage') {
                $discountAmount = ($subtotal * $this->discount_value) / 100;
            } else {
                $discountAmount = $this->discount_value;
            }
        }

        $totalAmount = $subtotal - $discountAmount + $gstAmount + $this->shipping_charge;

        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'gst_amount' => $gstAmount,
            'total_amount' => max(0, $totalAmount),
        ]);
    }
}
