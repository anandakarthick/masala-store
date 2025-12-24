<?php

namespace App\Services;

use App\Models\UserDevice;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FCMService
{
    private string $projectId;
    private string $serviceAccountPath;
    private string $fcmUrl;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id');
        $this->serviceAccountPath = config('services.fcm.service_account_path');
        $this->fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Get OAuth2 access token using Service Account
     */
    private function getAccessToken(): ?string
    {
        // Cache the token for 50 minutes (tokens expire in 1 hour)
        return Cache::remember('fcm_access_token', 3000, function () {
            try {
                if (!file_exists($this->serviceAccountPath)) {
                    Log::error('FCM Service Account file not found: ' . $this->serviceAccountPath);
                    return null;
                }

                $client = new GoogleClient();
                $client->setAuthConfig($this->serviceAccountPath);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                
                $token = $client->fetchAccessTokenWithAssertion();
                
                if (isset($token['access_token'])) {
                    return $token['access_token'];
                }

                Log::error('Failed to get FCM access token', $token);
                return null;
            } catch (\Exception $e) {
                Log::error('FCM OAuth Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Send push notification to a single device
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        return $this->send($fcmToken, $title, $body, $data);
    }

    /**
     * Send push notification to all devices of a user
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): bool
    {
        $tokens = UserDevice::getActiveTokensForUser($userId);
        
        if (empty($tokens)) {
            Log::info('No active devices for user', ['user_id' => $userId]);
            return false;
        }

        $success = true;
        $invalidTokens = [];

        foreach ($tokens as $token) {
            $result = $this->send($token, $title, $body, $data);
            if (!$result) {
                $invalidTokens[] = $token;
                $success = false;
            }
        }

        // Remove invalid tokens
        if (!empty($invalidTokens)) {
            UserDevice::removeInvalidTokens($invalidTokens);
        }

        Log::info('Push notification sent to user devices', [
            'user_id' => $userId,
            'total_devices' => count($tokens),
            'failed' => count($invalidTokens),
        ]);

        return $success;
    }

    /**
     * Send notification to a single device
     */
    private function send(string $token, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            Log::error('Cannot send FCM - no access token');
            return false;
        }

        try {
            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data), // FCM requires string values
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'sv_products_orders',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $message);

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', [
                    'token_preview' => substr($token, 0, 20) . '...',
                ]);
                return true;
            }

            $error = $response->json();
            Log::error('FCM notification failed', [
                'status' => $response->status(),
                'error' => $error,
            ]);

            // Check for invalid token errors
            if (isset($error['error']['details'])) {
                foreach ($error['error']['details'] as $detail) {
                    if (isset($detail['errorCode']) && 
                        in_array($detail['errorCode'], ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        // Token is invalid, should be removed
                        return false;
                    }
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('FCM notification exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order placed notification
     */
    public function sendOrderPlacedNotification(int $userId, string $orderNumber, float $total): bool
    {
        return $this->sendToUser(
            $userId,
            'Order Placed Successfully! 🎉',
            "Your order #{$orderNumber} has been placed. Total: ₹" . number_format($total, 2),
            [
                'type' => 'order_placed',
                'order_number' => $orderNumber,
                'total' => (string) $total,
            ]
        );
    }

    /**
     * Send order status update notification
     */
    public function sendOrderStatusNotification(int $userId, string $orderNumber, string $status): bool
    {
        $messages = [
            'confirmed' => ['Order Confirmed ✅', "Your order #{$orderNumber} has been confirmed."],
            'processing' => ['Order Processing 📦', "Your order #{$orderNumber} is being prepared."],
            'packed' => ['Order Packed 📦', "Your order #{$orderNumber} has been packed."],
            'shipped' => ['Order Shipped 🚚', "Your order #{$orderNumber} has been shipped."],
            'out_for_delivery' => ['Out for Delivery 🛵', "Your order #{$orderNumber} is out for delivery."],
            'delivered' => ['Order Delivered 🎊', "Your order #{$orderNumber} has been delivered. Enjoy!"],
            'cancelled' => ['Order Cancelled ❌', "Your order #{$orderNumber} has been cancelled."],
        ];

        $message = $messages[$status] ?? ['Order Update', "Your order #{$orderNumber} status: {$status}"];

        return $this->sendToUser(
            $userId,
            $message[0],
            $message[1],
            [
                'type' => 'order_status',
                'order_number' => $orderNumber,
                'status' => $status,
            ]
        );
    }

    /**
     * Send custom notification to user
     */
    public function sendCustomNotification(int $userId, string $title, string $body, array $data = []): bool
    {
        return $this->sendToUser($userId, $title, $body, $data);
    }
}
