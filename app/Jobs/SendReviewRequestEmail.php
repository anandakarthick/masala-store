<?php

namespace App\Jobs;

use App\Mail\ReviewRequestMail;
use App\Models\Order;
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
    public function handle(): void
    {
        try {
            // Only send if order is delivered and has a user with email
            if ($this->order->status !== 'delivered') {
                Log::info('Review request email skipped - order not delivered', [
                    'order_id' => $this->order->id,
                    'status' => $this->order->status
                ]);
                return;
            }

            if (!$this->order->customer_email) {
                Log::info('Review request email skipped - no customer email', [
                    'order_id' => $this->order->id
                ]);
                return;
            }

            // Generate review token if not already set
            if (!$this->order->review_token) {
                $this->order->generateReviewToken();
            }

            // Send the review request email
            Mail::to($this->order->customer_email)
                ->send(new ReviewRequestMail($this->order));

            // Update order to mark review as requested
            $this->order->update([
                'review_requested_at' => now()
            ]);

            Log::info('Review request email sent successfully', [
                'order_id' => $this->order->id,
                'email' => $this->order->customer_email,
                'order_number' => $this->order->order_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send review request email', [
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
        Log::error('Review request email job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
