<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReferralController extends Controller
{
    /**
     * Get referral details and history
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get referrals (people this user has referred)
        $referrals = Referral::with(['referred:id,name,email,created_at'])
            ->where('referrer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'referred_user' => [
                        'name' => $referral->referred->name ?? 'User',
                        'joined_at' => $referral->referred->created_at->format('d M Y'),
                    ],
                    'status' => $referral->status,
                    'reward_amount' => (float) $referral->reward_amount,
                    'orders_rewarded' => $referral->orders_rewarded,
                    'created_at' => $referral->created_at->format('d M Y'),
                ];
            });

        // Get referral settings
        $referralRewardPercentage = (float) Setting::get('referral_reward_percentage', 5);
        $referralMinOrderAmount = (float) Setting::get('referral_min_order_amount', 100);
        $referralMaxRewardPerOrder = (float) Setting::get('referral_max_reward_per_order', 100);

        // Calculate stats
        $totalReferrals = $referrals->count();
        $completedReferrals = $referrals->where('status', 'completed')->count();
        $totalEarned = $referrals->sum('reward_amount');
        $pendingReferrals = $referrals->where('status', 'pending')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'referral_code' => $user->referral_code,
                'referral_link' => url('/register?ref=' . $user->referral_code),
                'stats' => [
                    'total_referrals' => $totalReferrals,
                    'completed_referrals' => $completedReferrals,
                    'pending_referrals' => $pendingReferrals,
                    'total_earned' => $totalEarned,
                ],
                'settings' => [
                    'reward_percentage' => $referralRewardPercentage,
                    'min_order_amount' => $referralMinOrderAmount,
                    'max_reward_per_order' => $referralMaxRewardPerOrder,
                ],
                'referrals' => $referrals,
            ],
        ]);
    }
}
