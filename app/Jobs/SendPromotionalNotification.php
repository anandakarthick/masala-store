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
use Illuminate\Support\Facades\Mail;

class SendPromotionalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $imageUrl = null,
        public array $targetUserIds = [], // Empty means all users
        public bool $sendEmail = false,
        public ?string $emailSubject = null,
        public ?string $emailBody = null
    ) {}

    public function handle(FCMService $fcmService): void
    {
        try {
            // Get target users
            $query = User::where('is_active', true)
                ->where('is_guest', false)
                ->whereNotNull('fcm_token');

            if (!empty($this->targetUserIds)) {
                $query->whereIn('id', $this->targetUserIds);
            }

            $users = $query->get();

            $successCount = 0;
            $failCount = 0;

            foreach ($users as $user) {
                try {
                    // Check user notification preferences
                    $preference = $user->notificationPreference;
                    
                    // Skip if user has disabled promotions
                    if ($preference && !$preference->promotions) {
                        Log::info('Skipping promotional notification - user disabled', [
                            'user_id' => $user->id,
                        ]);
                        continue;
                    }

                    // Send push notification
                    if (!$preference || $preference->push_notifications) {
                        $fcmService->sendCustomNotification(
                            $user->id,
                            $this->title,
                            $this->body,
                            [
                                'type' => 'promotion',
                                'image_url' => $this->imageUrl ?? '',
                            ]
                        );
                    }

                    // Save in-app notification
                    UserNotification::create([
                        'user_id' => $user->id,
                        'type' => 'promotion',
                        'title' => $this->title,
                        'message' => $this->body,
                        'data' => [
                            'image_url' => $this->imageUrl,
                        ],
                    ]);

                    // Send email if enabled and user has email notifications on
                    if ($this->sendEmail && $user->email && 
                        (!$preference || $preference->email_notifications)) {
                        // You can create a PromotionalMail class for this
                        // Mail::to($user->email)->send(new PromotionalMail(...));
                    }

                    $successCount++;

                } catch (\Exception $e) {
                    $failCount++;
                    Log::warning('Failed to send promotional notification to user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Promotional notification job completed', [
                'title' => $this->title,
                'total_users' => $users->count(),
                'success' => $successCount,
                'failed' => $failCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send promotional notifications', [
                'title' => $this->title,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Promotional notification job failed permanently', [
            'title' => $this->title,
            'error' => $exception->getMessage(),
        ]);
    }
}
