<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
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
        'is_guest',
        'device_id',
        'google_id',
        'avatar',
        'provider',
        'fcm_token',
        'device_type',
        'fcm_token_updated_at',
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
            'is_guest' => 'boolean',
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

        // Generate referral code on user creation (only for non-guest users)
        static::creating(function ($user) {
            if (empty($user->referral_code) && !$user->is_guest) {
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

    /**
     * Find or create a guest user by device_id
     */
    public static function findOrCreateGuestByDeviceId(string $deviceId): self
    {
        // First, try to find existing user with this device_id
        $user = self::where('device_id', $deviceId)->first();

        if ($user) {
            return $user;
        }

        // If not found, create a new guest user
        // Use a unique suffix to avoid email collisions
        $uniqueSuffix = $deviceId . '_' . time() . '_' . Str::random(4);
        
        try {
            $user = self::create([
                'device_id' => $deviceId,
                'name' => 'Guest User',
                'email' => 'guest_' . $uniqueSuffix . '@guest.local',
                'password' => Hash::make(Str::random(32)),
                'is_guest' => true,
                'is_active' => true,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // If there's a duplicate key error, try to find the user again
            // (race condition handling)
            if ($e->errorInfo[1] == 1062) {
                $user = self::where('device_id', $deviceId)->first();
                if (!$user) {
                    // If still no user found, the duplicate is on email, create with new email
                    $user = self::create([
                        'device_id' => $deviceId,
                        'name' => 'Guest User',
                        'email' => 'guest_' . Str::uuid() . '@guest.local',
                        'password' => Hash::make(Str::random(32)),
                        'is_guest' => true,
                        'is_active' => true,
                    ]);
                }
            } else {
                throw $e;
            }
        }

        return $user;
    }

    /**
     * Convert guest user to regular user (when they register/login)
     */
    public function convertFromGuest(array $userData): self
    {
        if (!$this->is_guest) {
            return $this;
        }

        $this->update([
            'name' => $userData['name'] ?? $this->name,
            'email' => $userData['email'] ?? $this->email,
            'phone' => $userData['phone'] ?? null,
            'password' => isset($userData['password']) ? Hash::make($userData['password']) : $this->password,
            'is_guest' => false,
            'google_id' => $userData['google_id'] ?? null,
            'avatar' => $userData['avatar'] ?? null,
            'provider' => $userData['provider'] ?? null,
        ]);

        // Generate referral code for converted user
        if (empty($this->referral_code)) {
            $this->referral_code = self::generateUniqueReferralCode();
            $this->save();
        }

        return $this->fresh();
    }

    /**
     * Scope for guest users
     */
    public function scopeGuests($query)
    {
        return $query->where('is_guest', true);
    }

    /**
     * Scope for registered users
     */
    public function scopeRegistered($query)
    {
        return $query->where('is_guest', false);
    }

    /**
     * Check if user is a guest
     */
    public function isGuest(): bool
    {
        return $this->is_guest ?? false;
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
     * User devices for push notifications
     */
    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
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
