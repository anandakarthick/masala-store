<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'fcm_token',
        'device_type',
        'device_name',
        'device_id',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get active devices for a user
     */
    public static function getActiveTokensForUser(int $userId): array
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();
    }

    /**
     * Register or update device
     */
    public static function registerDevice(int $userId, string $fcmToken, string $deviceType, ?string $deviceId = null, ?string $deviceName = null): self
    {
        // If device_id provided, update existing or create
        if ($deviceId) {
            return self::updateOrCreate(
                ['user_id' => $userId, 'device_id' => $deviceId],
                [
                    'fcm_token' => $fcmToken,
                    'device_type' => $deviceType,
                    'device_name' => $deviceName,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]
            );
        }

        // Check if token already exists for this user
        $existing = self::where('user_id', $userId)
            ->where('fcm_token', $fcmToken)
            ->first();

        if ($existing) {
            $existing->update([
                'device_type' => $deviceType,
                'device_name' => $deviceName,
                'is_active' => true,
                'last_used_at' => now(),
            ]);
            return $existing;
        }

        // Create new device entry
        return self::create([
            'user_id' => $userId,
            'fcm_token' => $fcmToken,
            'device_type' => $deviceType,
            'device_name' => $deviceName,
            'device_id' => $deviceId,
            'is_active' => true,
            'last_used_at' => now(),
        ]);
    }

    /**
     * Deactivate device by token
     */
    public static function deactivateByToken(int $userId, string $fcmToken): bool
    {
        return self::where('user_id', $userId)
            ->where('fcm_token', $fcmToken)
            ->update(['is_active' => false]) > 0;
    }

    /**
     * Remove invalid tokens
     */
    public static function removeInvalidTokens(array $invalidTokens): int
    {
        return self::whereIn('fcm_token', $invalidTokens)->delete();
    }
}
