<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserNotification;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWalletNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $userId,
        public string $type, // 'credit' or 'debit'
        public float $amount,
        public float $newBalance,
        public string $description,
        public ?string $source = null
    ) {}

    public function handle(FCMService $fcmService): void
    {
        try {
            $user = User::find($this->userId);
            
            if (!$user) {
                Log::warning('Wallet notification - user not found', [
                    'user_id' => $this->userId,
                ]);
                return;
            }

            // Check notification preferences
            $preference = $user->notificationPreference;
            
            if ($this->type === 'credit') {
                $title = 'Money Added to Wallet! 💰';
                $body = "₹" . number_format($this->amount, 2) . " has been added to your wallet. " .
                        "New balance: ₹" . number_format($this->newBalance, 2);
                $emoji = '💰';
            } else {
                $title = 'Wallet Payment Made 💳';
                $body = "₹" . number_format($this->amount, 2) . " has been used from your wallet. " .
                        "Remaining balance: ₹" . number_format($this->newBalance, 2);
                $emoji = '💳';
            }

            // Send push notification if enabled
            if (!$preference || $preference->push_notifications) {
                $fcmService->sendCustomNotification(
                    $user->id,
                    $title,
                    $body,
                    [
                        'type' => 'wallet_' . $this->type,
                        'amount' => (string) $this->amount,
                        'new_balance' => (string) $this->newBalance,
                        'source' => $this->source ?? '',
                    ]
                );

                Log::info('Wallet push notification sent', [
                    'user_id' => $user->id,
                    'type' => $this->type,
                    'amount' => $this->amount,
                ]);
            }

            // Save in-app notification
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'wallet_' . $this->type,
                'title' => $title,
                'message' => $body,
                'data' => [
                    'amount' => $this->amount,
                    'new_balance' => $this->newBalance,
                    'description' => $this->description,
                    'source' => $this->source,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send wallet notification', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Wallet notification job failed permanently', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
