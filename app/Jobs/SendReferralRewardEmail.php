<?php

namespace App\Jobs;

use App\Mail\ReferralRewardMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReferralRewardEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public User $referrer,
        public User $referredUser,
        public Order $order,
        public float $rewardAmount
    ) {}

    public function handle(): void
    {
        try {
            if ($this->referrer->email) {
                Mail::to($this->referrer->email)
                    ->send(new ReferralRewardMail(
                        $this->referrer,
                        $this->referredUser,
                        $this->order,
                        $this->rewardAmount
                    ));

                Log::info('Referral reward email sent', [
                    'referrer_id' => $this->referrer->id,
                    'referrer_email' => $this->referrer->email,
                    'referred_user_id' => $this->referredUser->id,
                    'order_id' => $this->order->id,
                    'reward_amount' => $this->rewardAmount,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send referral reward email', [
                'referrer_id' => $this->referrer->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Referral reward email job failed permanently', [
            'referrer_id' => $this->referrer->id,
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
