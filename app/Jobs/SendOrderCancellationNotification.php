<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\UserNotification;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderCancellationNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected Order $order;
    protected string $cancelledBy;
    protected ?string $reason;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $cancelledBy = 'customer', ?string $reason = null)
    {
        $this->order = $order;
        $this->cancelledBy = $cancelledBy;
        $this->reason = $reason;
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
            Mail::send('emails.orders.cancelled', [
                'order' => $order,
                'cancelledBy' => $this->cancelledBy,
                'reason' => $this->reason,
            ], function ($message) use ($order) {
                $message->to($order->customer_email, $order->customer_name)
                    ->subject("Order #{$order->order_number} Cancelled");
            });

            Log::info('Order cancellation email sent', [
                'order_id' => $order->id,
                'email' => $order->customer_email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order cancellation email', [
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
            $title = $this->cancelledBy === 'customer' 
                ? 'Order Cancelled Successfully' 
                : 'Order Cancelled';

            UserNotification::create([
                'user_id' => $order->user_id,
                'type' => 'order_cancelled',
                'title' => $title . ' ❌',
                'message' => $this->getNotificationMessage($order),
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'cancelled_by' => $this->cancelledBy,
                    'reason' => $this->reason,
                    'total_amount' => (float) $order->total_amount,
                    'wallet_refund' => (float) $order->wallet_amount_used,
                ],
            ]);

            Log::info('Order cancellation in-app notification created', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create order cancellation in-app notification', [
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
            $title = $this->cancelledBy === 'customer' 
                ? 'Order Cancelled Successfully ❌' 
                : 'Order Cancelled ❌';

            $fcmService = app(FCMService::class);
            $fcmService->sendToUser(
                $order->user_id,
                $title,
                $this->getNotificationMessage($order),
                [
                    'type' => 'order_cancelled',
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]
            );

            Log::info('Order cancellation push notification sent', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order cancellation push notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get notification message
     */
    protected function getNotificationMessage(Order $order): string
    {
        $message = "Your order #{$order->order_number} has been cancelled.";

        if ($this->reason) {
            $message .= " Reason: {$this->reason}";
        }

        if ($order->wallet_amount_used > 0) {
            $message .= " ₹" . number_format($order->wallet_amount_used, 2) . " has been refunded to your wallet.";
        }

        return $message;
    }
}
