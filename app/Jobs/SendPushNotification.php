<?php

namespace App\Jobs;

use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    private string $type;
    private int $userId;
    private array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, int $userId, array $data = [])
    {
        $this->type = $type;
        $this->userId = $userId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(FCMService $fcmService): void
    {
        try {
            switch ($this->type) {
                case 'order_placed':
                    $fcmService->sendOrderPlacedNotification(
                        $this->userId,
                        $this->data['order_number'],
                        $this->data['total']
                    );
                    break;

                case 'order_status':
                    $fcmService->sendOrderStatusNotification(
                        $this->userId,
                        $this->data['order_number'],
                        $this->data['status']
                    );
                    break;

                case 'custom':
                    $fcmService->sendCustomNotification(
                        $this->userId,
                        $this->data['title'],
                        $this->data['body'],
                        $this->data['extra'] ?? []
                    );
                    break;

                default:
                    Log::warning('Unknown push notification type', ['type' => $this->type]);
            }

            Log::info('Push notification job completed', [
                'user_id' => $this->userId,
                'type' => $this->type,
            ]);

        } catch (\Exception $e) {
            Log::error('Push notification job failed', [
                'user_id' => $this->userId,
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
