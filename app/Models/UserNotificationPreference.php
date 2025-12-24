<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'push_notifications',
        'email_notifications',
        'order_updates',
        'promotions',
        'sms_notifications',
    ];

    protected $casts = [
        'push_notifications' => 'boolean',
        'email_notifications' => 'boolean',
        'order_updates' => 'boolean',
        'promotions' => 'boolean',
        'sms_notifications' => 'boolean',
    ];

    /**
     * User who owns these preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create preferences for a user
     */
    public static function getOrCreate(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'push_notifications' => true,
                'email_notifications' => true,
                'order_updates' => true,
                'promotions' => false,
                'sms_notifications' => false,
            ]
        );
    }

    /**
     * Check if user wants push notifications for a specific type
     */
    public function wantsPushNotification(string $type): bool
    {
        if (!$this->push_notifications) {
            return false;
        }

        return match($type) {
            'order_placed', 'order_confirmed', 'order_shipped', 'order_delivered', 'order_cancelled' => $this->order_updates,
            'promo', 'promotion', 'offer', 'discount' => $this->promotions,
            default => true,
        };
    }

    /**
     * Check if user wants email notifications for a specific type
     */
    public function wantsEmailNotification(string $type): bool
    {
        if (!$this->email_notifications) {
            return false;
        }

        return match($type) {
            'order_placed', 'order_confirmed', 'order_shipped', 'order_delivered', 'order_cancelled' => $this->order_updates,
            'promo', 'promotion', 'offer', 'discount' => $this->promotions,
            default => true,
        };
    }
}
