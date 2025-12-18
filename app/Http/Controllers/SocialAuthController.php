<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if ($user) {
                    // Link Google account to existing user
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'provider' => 'google',
                    ]);
                } else {
                    // Create new user
                    $customerRole = Role::getCustomerRole();
                    
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'provider' => 'google',
                        'password' => Hash::make(Str::random(24)), // Random password for OAuth users
                        'role_id' => $customerRole?->id,
                        'email_verified_at' => now(),
                        'is_active' => true,
                    ]);
                }
            } else {
                // Update avatar if changed
                $user->update([
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }
            
            // Login the user
            Auth::login($user, true);
            
            // Merge guest cart
            $cart = Cart::getCart();
            $cart->mergeGuestCart();
            
            // Redirect based on role
            if ($user->isAdmin() || $user->isStaff()) {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
            }
            
            return redirect()->route('home')->with('success', 'Welcome, ' . $user->name . '! You are now signed in.');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again or use email/password.');
        }
    }
}
