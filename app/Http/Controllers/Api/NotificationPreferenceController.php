<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationPreferenceController extends Controller
{
    /**
     * Get user's notification preferences
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $preferences = UserNotificationPreference::getOrCreate($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'push_notifications' => $preferences->push_notifications,
                'email_notifications' => $preferences->email_notifications,
                'order_updates' => $preferences->order_updates,
                'promotions' => $preferences->promotions,
                'sms_notifications' => $preferences->sms_notifications,
            ],
        ]);
    }

    /**
     * Update user's notification preferences
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'push_notifications' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
            'order_updates' => 'sometimes|boolean',
            'promotions' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
        ]);

        $user = $request->user();
        $preferences = UserNotificationPreference::getOrCreate($user->id);
        
        $preferences->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated',
            'data' => [
                'push_notifications' => $preferences->push_notifications,
                'email_notifications' => $preferences->email_notifications,
                'order_updates' => $preferences->order_updates,
                'promotions' => $preferences->promotions,
                'sms_notifications' => $preferences->sms_notifications,
            ],
        ]);
    }

    /**
     * Update a single preference
     */
    public function updateSingle(Request $request, string $key): JsonResponse
    {
        $allowedKeys = ['push_notifications', 'email_notifications', 'order_updates', 'promotions', 'sms_notifications'];
        
        if (!in_array($key, $allowedKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid preference key',
            ], 400);
        }

        $validated = $request->validate([
            'value' => 'required|boolean',
        ]);

        $user = $request->user();
        $preferences = UserNotificationPreference::getOrCreate($user->id);
        
        $preferences->update([$key => $validated['value']]);

        return response()->json([
            'success' => true,
            'message' => 'Preference updated',
            'data' => [
                $key => $preferences->$key,
            ],
        ]);
    }
}
