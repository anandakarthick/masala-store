<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Register device for push notifications (works for both guests and logged-in users)
     * This endpoint doesn't require authentication
     */
    public function registerDevice(Request $request)
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
            'device_type' => 'required|in:android,ios,web',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        try {
            // Check if user is authenticated
            $user = $request->user('sanctum');
            
            if (!$user) {
                // Guest user - find or create by device_id
                $user = User::findOrCreateGuestByDeviceId($validated['device_id']);
                Log::info('Guest user for FCM', [
                    'user_id' => $user->id,
                    'device_id' => $validated['device_id'],
                    'is_guest' => $user->is_guest,
                ]);
            }

            // Register device token
            UserDevice::registerDevice(
                $user->id,
                $validated['fcm_token'],
                $validated['device_type'],
                $validated['device_id'],
                $validated['device_name'] ?? null
            );

            // Also update user's primary fcm_token for backwards compatibility
            $user->update([
                'fcm_token' => $validated['fcm_token'],
                'device_type' => $validated['device_type'],
                'fcm_token_updated_at' => now(),
            ]);

            Log::info('FCM device registered', [
                'user_id' => $user->id,
                'is_guest' => $user->is_guest,
                'device_type' => $validated['device_type'],
                'device_id' => $validated['device_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device registered for notifications',
                'data' => [
                    'user_id' => $user->id,
                    'is_guest' => $user->is_guest,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error registering device for FCM: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to register device',
            ], 500);
        }
    }
}
