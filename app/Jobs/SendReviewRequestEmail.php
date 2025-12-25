<?php

namespace App\Jobs;

use App\Mail\ReviewRequestMail;
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

class SendReviewRequestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;
    public int $tries = 3;
    public int $backoff = 60;

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
    public function handle(FCMService $fcmService): void
    {
        try {
            // Only send if order is delivered
            if ($this->order->status !== 'delivered') {
                Log::info('Review request skipped - order not delivered', [
                    'order_id' => $this->order->id,
                    'status' => $this->order->status
                ]);
                return;
            }

            // Generate review token if not already set
            if (!$this->order->review_token) {
                $this->order->generateReviewToken();
            }

            // Send email notification
            if ($this->order->customer_email) {
                Mail::to($this->order->customer_email)
                    ->send(new ReviewRequestMail($this->order));

                Log::info('Review request email sent successfully', [
                    'order_id' => $this->order->id,
                    'email' => $this->order->customer_email,
                ]);
            }

            // Send push notification
            if ($this->order->user_id) {
                $title = 'How was your order? ⭐';
                $body = "We'd love to hear your feedback on order #{$this->order->order_number}. Leave a review!";

                $fcmService->sendCustomNotification(
                    $this->order->user_id,
                    $title,
                    $body,
                    [
                        'type' => 'review_request',
                        'order_number' => $this->order->order_number,
                        'order_id' => (string) $this->order->id,
                    ]
                );

                // Save in-app notification
                UserNotification::create([
                    'user_id' => $this->order->user_id,
                    'type' => 'review_request',
                    'title' => $title,
                    'message' => $body,
                    'data' => [
                        'order_id' => $this->order->id,
                        'order_number' => $this->order->order_number,
                    ],
                ]);

                Log::info('Review request push notification sent', [
                    'order_id' => $this->order->id,
                    'user_id' => $this->order->user_id,
                ]);
            }

            // Update order to mark review as requested
            $this->order->update([
                'review_requested_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send review request email/notification', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Review request email/notification job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
