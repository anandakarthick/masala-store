<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralRewardMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $referrer,
        public User $referredUser,
        public Order $order,
        public float $rewardAmount
    ) {}

    public function envelope(): Envelope
    {
        $businessName = Setting::get('business_name', 'Masala Store');
        
        return new Envelope(
            subject: "🎉 You've earned ₹" . number_format($this->rewardAmount, 0) . " - Referral Reward!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.referral-reward',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
