<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'is_active',
        'google_id',
        'avatar',
        'provider',
        'referral_code',
        'referred_by',
        'wallet_balance',
        'referred_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'wallet_balance' => 'decimal:2',
            'referred_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Generate referral code on user creation
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateUniqueReferralCode();
            }
        });
    }

    /**
     * Generate a unique referral code
     */
    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * User who referred this user
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Users referred by this user
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Referral records where this user is the referrer
     */
    public function referralsMade(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Referral record where this user was referred
     */
    public function referralReceived(): HasOne
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * Wallet transactions
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Add to wallet balance
     */
    public function addToWallet(float $amount, string $source, string $description, ?int $referenceOrderId = null, ?int $referenceUserId = null, ?array $metadata = null): WalletTransaction
    {
        $this->increment('wallet_balance', $amount);
        $this->refresh();

        return $this->walletTransactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->wallet_balance,
            'source' => $source,
            'description' => $description,
            'reference_order_id' => $referenceOrderId,
            'reference_user_id' => $referenceUserId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Deduct from wallet balance
     */
    public function deductFromWallet(float $amount, string $source, string $description, ?int $referenceOrderId = null, ?array $metadata = null): ?WalletTransaction
    {
        if ($this->wallet_balance < $amount) {
            return null;
        }

        $this->decrement('wallet_balance', $amount);
        $this->refresh();

        return $this->walletTransactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->wallet_balance,
            'source' => $source,
            'description' => $description,
            'reference_order_id' => $referenceOrderId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get total referral earnings
     */
    public function getTotalReferralEarningsAttribute(): float
    {
        return $this->walletTransactions()
            ->where('source', 'referral')
            ->where('type', 'credit')
            ->sum('amount');
    }

    /**
     * Get successful referrals count
     */
    public function getSuccessfulReferralsCountAttribute(): int
    {
        return $this->referralsMade()->completed()->count();
    }

    /**
     * Get referral link
     */
    public function getReferralLinkAttribute(): string
    {
        // Ensure user has a referral code
        if (empty($this->referral_code)) {
            $this->referral_code = self::generateUniqueReferralCode();
            $this->save();
        }
        
        return url('/register?ref=' . $this->referral_code);
    }

    /**
     * Check if user was referred
     */
    public function wasReferred(): bool
    {
        return $this->referred_by !== null;
    }

    /**
     * Check if user has completed first order
     */
    public function hasCompletedFirstOrder(): bool
    {
        return $this->orders()
            ->whereNotIn('status', ['cancelled'])
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role?->slug === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role?->slug === 'customer';
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->slug === $role;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->pincode
        ]);
        return implode(', ', $parts);
    }
}
