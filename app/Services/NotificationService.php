<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotificationPreference;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Check if user wants to receive a specific type of notification
     */
    public static function shouldSendPushNotification(int $userId, string $type): bool
    {
        $preferences = UserNotificationPreference::where('user_id', $userId)->first();
        
        if (!$preferences) {
            // Default: send order updates, don't send promotions
            return !in_array($type, ['promo', 'promotion', 'offer', 'discount']);
        }

        return $preferences->wantsPushNotification($type);
    }

    /**
     * Check if user wants to receive email for a specific type
     */
    public static function shouldSendEmail(int $userId, string $type): bool
    {
        $preferences = UserNotificationPreference::where('user_id', $userId)->first();
        
        if (!$preferences) {
            // Default: send order updates, don't send promotions
            return !in_array($type, ['promo', 'promotion', 'offer', 'discount']);
        }

        return $preferences->wantsEmailNotification($type);
    }

    /**
     * Send push notification to user (respecting preferences)
     */
    public static function sendPushNotification(User $user, string $type, string $title, string $message, ?array $data = null): bool
    {
        // Check if user wants this type of notification
        if (!self::shouldSendPushNotification($user->id, $type)) {
            Log::info("Push notification skipped for user {$user->id} - preferences disabled for type: {$type}");
            return false;
        }

        // Check if user has FCM token
        if (!$user->fcm_token) {
            Log::info("Push notification skipped for user {$user->id} - no FCM token");
            return false;
        }

        try {
            // Send via Firebase
            $fcmService = app(\App\Services\FcmService::class);
            $fcmService->sendToUser($user, $title, $message, $data);
            
            Log::info("Push notification sent to user {$user->id}", [
                'type' => $type,
                'title' => $title,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send push notification to user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification to user (respecting preferences)
     */
    public static function sendEmailNotification(User $user, string $type, string $subject, string $content): bool
    {
        // Check if user wants this type of email
        if (!self::shouldSendEmail($user->id, $type)) {
            Log::info("Email notification skipped for user {$user->id} - preferences disabled for type: {$type}");
            return false;
        }

        // Check if user has email
        if (!$user->email) {
            Log::info("Email notification skipped for user {$user->id} - no email address");
            return false;
        }

        try {
            // Send email (implement your email sending logic here)
            // Mail::to($user->email)->send(new NotificationMail($subject, $content));
            
            Log::info("Email notification sent to user {$user->id}", [
                'type' => $type,
                'subject' => $subject,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email notification to user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create in-app notification and optionally send push/email
     */
    public static function notify(
        User $user, 
        string $type, 
        string $title, 
        string $message, 
        ?array $data = null,
        bool $sendPush = true,
        bool $sendEmail = false
    ): UserNotification {
        // Always create in-app notification
        $notification = UserNotification::notify($user->id, $type, $title, $message, $data);

        // Optionally send push notification
        if ($sendPush) {
            self::sendPushNotification($user, $type, $title, $message, $data);
        }

        // Optionally send email
        if ($sendEmail) {
            self::sendEmailNotification($user, $type, $title, $message);
        }

        return $notification;
    }

    /**
     * Send order notification
     */
    public static function notifyOrderStatus(User $user, string $orderNumber, string $status): void
    {
        $titles = [
            'placed' => 'Order Placed',
            'confirmed' => 'Order Confirmed',
            'processing' => 'Order Processing',
            'shipped' => 'Order Shipped',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
        ];

        $messages = [
            'placed' => "Your order #{$orderNumber} has been placed successfully!",
            'confirmed' => "Your order #{$orderNumber} has been confirmed.",
            'processing' => "Your order #{$orderNumber} is being processed.",
            'shipped' => "Your order #{$orderNumber} has been shipped!",
            'out_for_delivery' => "Your order #{$orderNumber} is out for delivery!",
            'delivered' => "Your order #{$orderNumber} has been delivered. Enjoy!",
            'cancelled' => "Your order #{$orderNumber} has been cancelled.",
        ];

        $type = "order_{$status}";
        $title = $titles[$status] ?? 'Order Update';
        $message = $messages[$status] ?? "Your order #{$orderNumber} status has been updated.";

        self::notify(
            $user,
            $type,
            $title,
            $message,
            ['order_number' => $orderNumber],
            sendPush: true,
            sendEmail: in_array($status, ['placed', 'shipped', 'delivered'])
        );
    }

    /**
     * Send promotional notification (respects promotions preference)
     */
    public static function notifyPromotion(User $user, string $title, string $message, ?array $data = null): void
    {
        self::notify(
            $user,
            'promo',
            $title,
            $message,
            $data,
            sendPush: true,
            sendEmail: true
        );
    }

    /**
     * Send wallet notification
     */
    public static function notifyWalletTransaction(User $user, string $type, float $amount, string $description): void
    {
        $title = $type === 'credit' ? 'Wallet Credited' : 'Wallet Debited';
        $sign = $type === 'credit' ? '+' : '-';
        $message = "{$sign}₹{$amount} - {$description}";

        self::notify(
            $user,
            "wallet_{$type}",
            $title,
            $message,
            ['amount' => $amount, 'type' => $type],
            sendPush: true,
            sendEmail: false
        );
    }
}
