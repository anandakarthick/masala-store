<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Display referral statistics and list
     */
    public function index(Request $request)
    {
        // Get statistics
        $stats = [
            'total_referrals' => Referral::count(),
            'pending_referrals' => Referral::pending()->count(),
            'completed_referrals' => Referral::completed()->count(),
            'total_rewards_paid' => WalletTransaction::where('source', 'referral')->sum('amount'),
            'total_users_with_referrals' => User::whereHas('referralsMade')->count(),
        ];

        // Build query
        $query = Referral::with(['referrer', 'referred', 'firstOrder']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('referrer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('referred', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $referrals = $query->latest()->paginate(20)->withQueryString();

        return view('admin.referrals.index', compact('referrals', 'stats'));
    }

    /**
     * View user's wallet transactions
     */
    public function userWallet(User $user)
    {
        $transactions = $user->walletTransactions()
            ->with(['order', 'referenceUser'])
            ->latest()
            ->paginate(20);

        return view('admin.referrals.user-wallet', compact('user', 'transactions'));
    }

    /**
     * Add/Deduct wallet balance manually
     */
    public function adjustWallet(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        if ($validated['type'] === 'credit') {
            $user->addToWallet(
                $validated['amount'],
                'admin',
                $validated['description']
            );
            $message = "₹{$validated['amount']} credited to {$user->name}'s wallet.";
        } else {
            if ($user->wallet_balance < $validated['amount']) {
                return back()->with('error', 'Insufficient wallet balance for debit.');
            }
            $user->deductFromWallet(
                $validated['amount'],
                'admin',
                $validated['description']
            );
            $message = "₹{$validated['amount']} debited from {$user->name}'s wallet.";
        }

        return back()->with('success', $message);
    }

    /**
     * Top referrers list
     */
    public function topReferrers()
    {
        $topReferrers = User::withCount(['referralsMade as total_referrals'])
            ->withSum('walletTransactions as referral_earnings', 'amount')
            ->whereHas('referralsMade')
            ->orderByDesc('total_referrals')
            ->take(50)
            ->get();

        return view('admin.referrals.top-referrers', compact('topReferrers'));
    }
}
