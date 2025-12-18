<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class AdminNewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?string $pdfPath;

    public function __construct(Order $order, ?string $pdfPath = null)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛒 New Order Received - #' . $this->order->order_number . ' - ₹' . number_format($this->order->total_amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.admin-notification',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $attachments[] = Attachment::fromPath($this->pdfPath)
                ->as('Invoice-' . $this->order->order_number . '.pdf')
                ->withMime('application/pdf');
        }
        
        return $attachments;
    }
}
