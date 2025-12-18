<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Mail\AdminNewOrderMail;
use App\Models\Order;
use App\Models\Setting;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(InvoiceService $invoiceService): void
    {
        try {
            // Generate invoice PDF
            $pdfPath = $invoiceService->generateInvoice($this->order);

            // Send confirmation email to customer
            if ($this->order->customer_email) {
                Mail::to($this->order->customer_email)
                    ->send(new OrderConfirmationMail($this->order, $pdfPath));
                
                Log::info('Order confirmation email sent to customer', [
                    'order_id' => $this->order->id,
                    'email' => $this->order->customer_email
                ]);
            }

            // Send notification email to admin
            $adminEmail = Setting::get('business_email', config('mail.admin_email', 'admin@masalastore.com'));
            if ($adminEmail) {
                Mail::to($adminEmail)
                    ->send(new AdminNewOrderMail($this->order, $pdfPath));
                
                Log::info('Order notification email sent to admin', [
                    'order_id' => $this->order->id,
                    'email' => $adminEmail
                ]);
            }

            // Clean up temp PDF file after sending
            if ($pdfPath && file_exists($pdfPath)) {
                unlink($pdfPath);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order emails', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Order email job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}
