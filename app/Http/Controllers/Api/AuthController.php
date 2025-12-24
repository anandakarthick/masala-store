<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);

        $customerRole = Role::where('slug', 'customer')->first();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $customerRole?->id,
            'is_active' => true,
        ];

        // Handle referral
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', $validated['referral_code'])->first();
            if ($referrer) {
                $userData['referred_by'] = $referrer->id;
                $userData['referred_at'] = now();
            }
        }

        $user = User::create($userData);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.',
            ], 403);
        }

        // Revoke all existing tokens
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        // Merge guest cart if session ID provided
        $sessionId = $request->header('X-Session-Id');
        if ($sessionId) {
            $this->mergeGuestCartForMobile($user, $sessionId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
            ]
        ]);
    }

    /**
     * Login with Google using user info directly (for mobile apps)
     */
    public function googleLoginWithInfo(Request $request)
    {
        Log::info('Google Login Request:', $request->all());
        
        try {
            $validated = $request->validate([
                'google_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'required|string',
                'avatar' => 'nullable|string',
            ]);

            // Get avatar safely (it's optional)
            $avatar = $request->input('avatar', null);

            Log::info('Validated data:', $validated);
            Log::info('Avatar:', ['avatar' => $avatar]);

            // Find user by google_id first
            $user = User::where('google_id', $validated['google_id'])->first();
            Log::info('User by google_id:', ['found' => $user ? true : false]);

            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $validated['email'])->first();
                Log::info('User by email:', ['found' => $user ? true : false]);

                if ($user) {
                    // Link Google account to existing user
                    $updateData = [
                        'google_id' => $validated['google_id'],
                        'provider' => 'google',
                    ];
                    if ($avatar) {
                        $updateData['avatar'] = $avatar;
                    }
                    $user->update($updateData);
                    Log::info('Updated existing user with google_id');
                } else {
                    // Create new user
                    $customerRole = Role::where('slug', 'customer')->first();
                    Log::info('Customer role:', ['id' => $customerRole?->id]);

                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'google_id' => $validated['google_id'],
                        'avatar' => $avatar,
                        'provider' => 'google',
                        'password' => Hash::make(Str::random(24)),
                        'role_id' => $customerRole?->id,
                        'email_verified_at' => now(),
                        'is_active' => true,
                    ]);
                    Log::info('Created new user:', ['id' => $user->id]);
                }
            } else {
                // Update avatar if provided
                if ($avatar) {
                    $user->update(['avatar' => $avatar]);
                }
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated.',
                ], 403);
            }

            // Revoke existing tokens
            $user->tokens()->delete();

            $token = $user->createToken('mobile-app')->plainTextToken;
            Log::info('Token created successfully');

            // Merge guest cart if session ID provided
            $sessionId = $request->header('X-Session-Id');
            if ($sessionId) {
                $this->mergeGuestCartForMobile($user, $sessionId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $this->formatUser($user),
                    'token' => $token,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login with phone (OTP based - simplified version)
     */
    public function loginWithPhone(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:15',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this phone number.',
            ], 404);
        }

        // In production, send OTP here
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'phone' => $validated['phone'],
            ]
        ]);
    }

    /**
     * Verify OTP and login
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:15',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this phone number.',
            ], 404);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
            ]
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $this->formatUser($request->user()),
            ]
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15|unique:users,phone,' . $user->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $this->formatUser($user->fresh()),
            ]
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Format user for response
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'pincode' => $user->pincode,
            'avatar' => $user->avatar,
            'wallet_balance' => (float) ($user->wallet_balance ?? 0),
            'referral_code' => $user->referral_code,
            'referral_link' => $user->referral_link ?? '',
            'created_at' => $user->created_at->toISOString(),
        ];
    }

    /**
     * Merge guest cart into user cart for mobile app
     */
    private function mergeGuestCartForMobile(User $user, string $sessionId): void
    {
        try {
            Log::info('Merging guest cart', ['user_id' => $user->id, 'session_id' => $sessionId]);
            
            // Find guest cart by session ID
            $guestCart = Cart::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->first();

            if (!$guestCart) {
                Log::info('No guest cart found');
                return;
            }

            Log::info('Guest cart found', ['cart_id' => $guestCart->id, 'items' => $guestCart->items->count()]);

            // Get or create user's cart
            $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

            // Merge regular items
            foreach ($guestCart->items as $item) {
                // Check if item already exists in user cart
                $existingItem = $userCart->items()
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if ($existingItem) {
                    // Add quantities
                    $existingItem->increment('quantity', $item->quantity);
                } else {
                    // Create new item in user cart
                    $userCart->items()->create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                    ]);
                }
            }

            // Merge custom combos if any
            foreach ($guestCart->customCombos as $combo) {
                $newCombo = $userCart->customCombos()->create([
                    'combo_setting_id' => $combo->combo_setting_id,
                    'combo_name' => $combo->combo_name,
                    'quantity' => $combo->quantity,
                    'calculated_price' => $combo->calculated_price,
                    'discount_amount' => $combo->discount_amount,
                    'final_price' => $combo->final_price,
                ]);

                foreach ($combo->items as $item) {
                    $newCombo->items()->create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ]);
                }
            }

            // Delete guest cart
            $guestCart->items()->delete();
            $guestCart->customCombos()->delete();
            $guestCart->delete();

            Log::info('Guest cart merged successfully');
        } catch (\Exception $e) {
            Log::error('Error merging guest cart: ' . $e->getMessage());
        }
    }
}
