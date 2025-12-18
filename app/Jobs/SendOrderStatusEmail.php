<?php

namespace App\Jobs;

use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
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

    public function handle(): void
    {
        try {
            if ($this->order->customer_email) {
                Mail::to($this->order->customer_email)
                    ->send(new OrderStatusUpdateMail($this->order, $this->oldStatus, $this->newStatus));
                
                Log::info('Order status update email sent', [
                    'order_id' => $this->order->id,
                    'email' => $this->order->customer_email,
                    'new_status' => $this->newStatus
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order status email', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
