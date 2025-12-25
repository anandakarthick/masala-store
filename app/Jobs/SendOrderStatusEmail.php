<?php

namespace App\Jobs;

use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use App\Models\UserNotification;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;
    public string $oldStatus;
    public string $newStatus;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function handle(FCMService $fcmService): void
    {
        try {
            // Status messages for notifications
            $statusMessages = [
                'confirmed' => [
                    'title' => 'Order Confirmed ✅',
                    'body' => "Your order #{$this->order->order_number} has been confirmed.",
                    'emoji' => '✅'
                ],
                'processing' => [
                    'title' => 'Order Processing 📦',
                    'body' => "Your order #{$this->order->order_number} is being prepared.",
                    'emoji' => '📦'
                ],
                'packed' => [
                    'title' => 'Order Packed 📦',
                    'body' => "Your order #{$this->order->order_number} has been packed and is ready for shipping.",
                    'emoji' => '📦'
                ],
                'shipped' => [
                    'title' => 'Order Shipped 🚚',
                    'body' => "Your order #{$this->order->order_number} has been shipped." . 
                        ($this->order->tracking_number ? " Tracking: {$this->order->tracking_number}" : ""),
                    'emoji' => '🚚'
                ],
                'out_for_delivery' => [
                    'title' => 'Out for Delivery 🛵',
                    'body' => "Your order #{$this->order->order_number} is out for delivery. It will arrive soon!",
                    'emoji' => '🛵'
                ],
                'delivered' => [
                    'title' => 'Order Delivered 🎊',
                    'body' => "Your order #{$this->order->order_number} has been delivered. Enjoy!",
                    'emoji' => '🎊'
                ],
                'cancelled' => [
                    'title' => 'Order Cancelled ❌',
                    'body' => "Your order #{$this->order->order_number} has been cancelled.",
                    'emoji' => '❌'
                ],
                'returned' => [
                    'title' => 'Order Returned 📦',
                    'body' => "Your order #{$this->order->order_number} return has been processed.",
                    'emoji' => '📦'
                ],
            ];

            $message = $statusMessages[$this->newStatus] ?? [
                'title' => 'Order Update',
                'body' => "Your order #{$this->order->order_number} status: {$this->newStatus}",
                'emoji' => '📋'
            ];

            // Send email notification
            if ($this->order->customer_email) {
                Mail::to($this->order->customer_email)
                    ->send(new OrderStatusUpdateMail($this->order, $this->oldStatus, $this->newStatus));
                
                Log::info('Order status update email sent', [
                    'order_id' => $this->order->id,
                    'email' => $this->order->customer_email,
                    'new_status' => $this->newStatus
                ]);
            }

            // Send push notification
            if ($this->order->user_id) {
                $fcmService->sendOrderStatusNotification(
                    $this->order->user_id,
                    $this->order->order_number,
                    $this->newStatus
                );
                
                // Save in-app notification
                UserNotification::create([
                    'user_id' => $this->order->user_id,
                    'type' => 'order_status',
                    'title' => $message['title'],
                    'message' => $message['body'],
                    'data' => [
                        'order_id' => $this->order->id,
                        'order_number' => $this->order->order_number,
                        'old_status' => $this->oldStatus,
                        'new_status' => $this->newStatus,
                    ],
                ]);
                
                Log::info('Order status push notification sent', [
                    'order_id' => $this->order->id,
                    'user_id' => $this->order->user_id,
                    'new_status' => $this->newStatus
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order status email/notification', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Order status email/notification job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
