<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\ReferralService;
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

    /**
     * Process referral reward manually
     */
    public function processReward(Referral $referral)
    {
        // Check if already completed
        if ($referral->status === 'completed') {
            return back()->with('error', 'This referral has already been completed.');
        }

        // Get the referred user's first valid order
        $referredUser = $referral->referred;
        $order = $referredUser->orders()
            ->whereNotIn('status', ['cancelled'])
            ->oldest()
            ->first();

        if (!$order) {
            return back()->with('error', 'The referred user has not placed any valid orders yet.');
        }

        // Check if referral system is enabled
        if (!ReferralService::isEnabled()) {
            return back()->with('error', 'Referral program is currently disabled. Enable it in settings first.');
        }

        // Process the reward
        $result = ReferralService::processOrderReferralReward($order);

        if ($result) {
            return back()->with('success', 'Referral reward processed successfully! The referrer has been credited.');
        } else {
            return back()->with('error', 'Could not process the referral reward. Check the logs for details.');
        }
    }

    /**
     * Process all pending referrals that have valid orders
     */
    public function processAllPending()
    {
        if (!ReferralService::isEnabled()) {
            return back()->with('error', 'Referral program is currently disabled. Enable it in settings first.');
        }

        $pendingReferrals = Referral::pending()->with('referred')->get();
        $processed = 0;
        $skipped = 0;

        foreach ($pendingReferrals as $referral) {
            $referredUser = $referral->referred;
            
            // Get the referred user's first valid order
            $order = $referredUser->orders()
                ->whereNotIn('status', ['cancelled'])
                ->oldest()
                ->first();

            if ($order) {
                $result = ReferralService::processOrderReferralReward($order);
                if ($result) {
                    $processed++;
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }

        return back()->with('success', "Processed {$processed} referrals. Skipped {$skipped} (no valid orders or already rewarded).");
    }

    /**
     * Mark referral as completed manually (without reward)
     */
    public function markCompleted(Referral $referral)
    {
        if ($referral->status === 'completed') {
            return back()->with('error', 'This referral is already completed.');
        }

        $referral->markAsCompleted();

        return back()->with('success', 'Referral marked as completed.');
    }

    /**
     * Mark referral as expired
     */
    public function markExpired(Referral $referral)
    {
        $referral->update(['status' => 'expired']);

        return back()->with('success', 'Referral marked as expired.');
    }
}
