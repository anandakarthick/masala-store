<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Role;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Merge guest cart
            $cart = Cart::getCart();
            $cart->mergeGuestCart();

            // Redirect based on role
            if (auth()->user()->isAdmin() || auth()->user()->isStaff()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request)
    {
        $referralCode = $request->get('ref');
        $referralInfo = null;
        
        if ($referralCode) {
            $referralInfo = ReferralService::validateReferralCode($referralCode);
        }
        
        return view('auth.register', compact('referralCode', 'referralInfo'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|min:8|confirmed',
            'referral_code' => 'nullable|string|max:20',
        ]);

        $customerRole = Role::getCustomerRole();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $customerRole?->id,
        ]);

        // Process referral code if provided
        $referralCode = $validated['referral_code'] ?? $request->get('ref');
        if ($referralCode) {
            ReferralService::processReferralCode($user, $referralCode);
        }

        Auth::login($user);

        // Merge guest cart
        $cart = Cart::getCart();
        $cart->mergeGuestCart();

        $message = 'Registration successful! Welcome to our store.';
        if ($user->wasReferred()) {
            $message .= ' Your referral bonus will be applied when your referrer earns rewards!';
        }

        return redirect()->route('home')->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Here you would implement password reset logic
        // Password::sendResetLink($request->only('email'));

        return back()->with('success', 'Password reset link sent to your email.');
    }

    /**
     * Validate referral code (AJAX)
     */
    public function validateReferralCode(Request $request)
    {
        $code = $request->get('code');
        $result = ReferralService::validateReferralCode($code);
        
        return response()->json($result);
    }
}
