<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\UserNotification;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDeliveryUpdateNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected Order $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;

        // 1. Send Email if customer has email
        if ($order->customer_email) {
            $this->sendEmail($order);
        }

        // 2. Create in-app notification
        if ($order->user_id) {
            $this->createInAppNotification($order);
        }

        // 3. Send push notification
        if ($order->user_id) {
            $this->sendPushNotification($order);
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmail(Order $order): void
    {
        try {
            Mail::send('emails.orders.delivery-update', [
                'order' => $order,
            ], function ($message) use ($order) {
                $message->to($order->customer_email, $order->customer_name)
                    ->subject("Delivery Update for Order #{$order->order_number}");
            });

            Log::info('Delivery update email sent', [
                'order_id' => $order->id,
                'email' => $order->customer_email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send delivery update email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create in-app notification
     */
    protected function createInAppNotification(Order $order): void
    {
        try {
            $hasAttachments = $order->hasDeliveryAttachments();
            
            UserNotification::create([
                'user_id' => $order->user_id,
                'type' => 'delivery_update',
                'title' => 'Delivery Update 🚚',
                'message' => $this->getNotificationMessage($order, $hasAttachments),
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'delivery_partner' => $order->delivery_partner,
                    'tracking_number' => $order->tracking_number,
                    'expected_delivery_date' => $order->expected_delivery_date?->format('d M Y'),
                    'has_attachments' => $hasAttachments,
                ],
            ]);

            Log::info('Delivery update in-app notification created', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create delivery update in-app notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send push notification
     */
    protected function sendPushNotification(Order $order): void
    {
        try {
            $hasAttachments = $order->hasDeliveryAttachments();
            
            PushNotificationService::sendToUser(
                $order->user_id,
                'Delivery Update 🚚',
                $this->getNotificationMessage($order, $hasAttachments),
                [
                    'type' => 'delivery_update',
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]
            );

            Log::info('Delivery update push notification sent', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send delivery update push notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get notification message based on delivery info
     */
    protected function getNotificationMessage(Order $order, bool $hasAttachments): string
    {
        $parts = ["Your order #{$order->order_number} delivery details have been updated."];

        if ($order->delivery_partner) {
            $parts[] = "Delivery Partner: {$order->delivery_partner}";
        }

        if ($order->tracking_number) {
            $parts[] = "Tracking: {$order->tracking_number}";
        }

        if ($order->expected_delivery_date) {
            $parts[] = "Expected Delivery: " . $order->expected_delivery_date->format('d M Y');
        }

        if ($hasAttachments) {
            $parts[] = "📎 Attachments available";
        }

        return implode(' | ', $parts);
    }
}
