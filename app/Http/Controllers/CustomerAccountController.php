<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Referral;
use App\Models\WalletTransaction;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerAccountController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        
        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->whereIn('status', ['pending', 'confirmed', 'processing', 'packed', 'shipped'])->count(),
            'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'wallet_balance' => $user->wallet_balance,
            'referral_earnings' => $user->total_referral_earnings,
            'successful_referrals' => $user->successful_referrals_count,
        ];

        return view('frontend.account.dashboard', compact('user', 'recentOrders', 'stats'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('frontend.account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');
        return view('frontend.account.order-detail', compact('order'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('frontend.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('frontend.account.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function wishlist()
    {
        $wishlists = auth()->user()->wishlists()->with('product.primaryImage')->paginate(12);
        return view('frontend.account.wishlist', compact('wishlists'));
    }

    /**
     * Wallet page
     */
    public function wallet()
    {
        $user = auth()->user();
        $transactions = $user->walletTransactions()
            ->with(['order', 'referenceUser'])
            ->latest()
            ->paginate(15);

        $stats = [
            'balance' => $user->wallet_balance,
            'total_credits' => $user->walletTransactions()->credits()->sum('amount'),
            'total_debits' => $user->walletTransactions()->debits()->sum('amount'),
            'referral_earnings' => $user->walletTransactions()->referral()->credits()->sum('amount'),
        ];

        return view('frontend.account.wallet', compact('user', 'transactions', 'stats'));
    }

    /**
     * Referrals page
     */
    public function referrals()
    {
        $user = auth()->user();
        
        // Get referral program info
        $programInfo = ReferralService::getProgramInfo();
        
        // Get user's referrals
        $referrals = Referral::where('referrer_id', $user->id)
            ->with(['referred', 'firstOrder'])
            ->latest()
            ->paginate(15);

        // Referral stats
        $stats = ReferralService::getUserStats($user);

        return view('frontend.account.referrals', compact('user', 'referrals', 'stats', 'programInfo'));
    }
}
