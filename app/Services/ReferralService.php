<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Jobs\SendReferralRewardEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Check if referral program is enabled
     */
    public static function isEnabled(): bool
    {
        return (bool) Setting::get('referral_enabled', false);
    }

    /**
     * Get reward amount per referral
     */
    public static function getRewardAmount(): float
    {
        return (float) Setting::get('referral_reward_amount', 50);
    }

    /**
     * Get minimum order amount for referral reward
     */
    public static function getMinOrderAmount(): float
    {
        return (float) Setting::get('referral_min_order_amount', 0);
    }

    /**
     * Check if reward is only for first order
     */
    public static function isFirstOrderOnly(): bool
    {
        return (bool) Setting::get('referral_first_order_only', true);
    }

    /**
     * Get maximum rewards per referral (0 = unlimited)
     */
    public static function getMaxRewardsPerReferral(): int
    {
        return (int) Setting::get('referral_max_rewards_per_referral', 1);
    }

    /**
     * Get reward type (fixed or percentage)
     */
    public static function getRewardType(): string
    {
        return Setting::get('referral_reward_type', 'fixed'); // fixed or percentage
    }

    /**
     * Get maximum reward amount (for percentage type)
     */
    public static function getMaxRewardAmount(): float
    {
        return (float) Setting::get('referral_max_reward_amount', 500);
    }

    /**
     * Process referral code during registration
     */
    public static function processReferralCode(User $newUser, ?string $referralCode): bool
    {
        if (!self::isEnabled() || empty($referralCode)) {
            return false;
        }

        // Find referrer by code
        $referrer = User::where('referral_code', strtoupper($referralCode))->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            return false;
        }

        try {
            DB::transaction(function () use ($newUser, $referrer) {
                // Update new user with referrer info
                $newUser->update([
                    'referred_by' => $referrer->id,
                    'referred_at' => now(),
                ]);

                // Create referral record
                Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $newUser->id,
                    'status' => 'pending',
                ]);

                Log::info('Referral code applied', [
                    'referrer_id' => $referrer->id,
                    'referred_id' => $newUser->id,
                    'code' => $referrer->referral_code,
                ]);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to process referral code', [
                'error' => $e->getMessage(),
                'referral_code' => $referralCode,
                'new_user_id' => $newUser->id,
            ]);
            return false;
        }
    }

    /**
     * Process referral reward when order is placed
     */
    public static function processOrderReferralReward(Order $order): bool
    {
        if (!self::isEnabled()) {
            Log::info('Referral system is disabled');
            return false;
        }

        // Get the user who placed the order
        $customer = $order->user;
        if (!$customer || !$customer->wasReferred()) {
            Log::info('Order user was not referred', ['order_id' => $order->id]);
            return false;
        }

        // Get the referrer
        $referrer = $customer->referrer;
        if (!$referrer) {
            Log::info('Referrer not found', ['order_id' => $order->id]);
            return false;
        }

        // Get referral record
        $referral = Referral::where('referrer_id', $referrer->id)
            ->where('referred_id', $customer->id)
            ->first();

        if (!$referral) {
            Log::info('Referral record not found', [
                'order_id' => $order->id,
                'referrer_id' => $referrer->id,
                'referred_id' => $customer->id,
            ]);
            return false;
        }

        // Check if first order only and user already has orders
        if (self::isFirstOrderOnly()) {
            $previousOrders = $customer->orders()
                ->where('id', '!=', $order->id)
                ->whereNotIn('status', ['cancelled'])
                ->exists();

            if ($previousOrders) {
                Log::info('Not first order, reward only for first order', ['order_id' => $order->id]);
                return false;
            }
        }

        // Check max rewards per referral
        $maxRewards = self::getMaxRewardsPerReferral();
        if ($maxRewards > 0 && $referral->orders_rewarded >= $maxRewards) {
            Log::info('Max rewards reached for referral', [
                'order_id' => $order->id,
                'referral_id' => $referral->id,
                'orders_rewarded' => $referral->orders_rewarded,
                'max_rewards' => $maxRewards,
            ]);
            return false;
        }

        // Check minimum order amount
        $minOrderAmount = self::getMinOrderAmount();
        if ($minOrderAmount > 0 && $order->total_amount < $minOrderAmount) {
            Log::info('Order amount below minimum', [
                'order_id' => $order->id,
                'order_amount' => $order->total_amount,
                'min_amount' => $minOrderAmount,
            ]);
            return false;
        }

        // Calculate reward amount
        $rewardAmount = self::calculateRewardAmount($order);

        if ($rewardAmount <= 0) {
            Log::info('Reward amount is zero', ['order_id' => $order->id]);
            return false;
        }

        try {
            DB::transaction(function () use ($referrer, $customer, $referral, $order, $rewardAmount) {
                // Add reward to referrer's wallet
                $referrer->addToWallet(
                    $rewardAmount,
                    'referral',
                    "Referral reward for {$customer->name}'s order #{$order->order_number}",
                    $order->id,
                    $customer->id,
                    [
                        'order_amount' => $order->total_amount,
                        'referred_user_name' => $customer->name,
                        'referred_user_email' => $customer->email,
                    ]
                );

                // Update referral record
                $referral->incrementOrdersRewarded($rewardAmount);

                // Set first order if not set
                if (!$referral->first_order_id) {
                    $referral->update(['first_order_id' => $order->id]);
                }

                // Mark as completed if first order only
                if (self::isFirstOrderOnly() || (self::getMaxRewardsPerReferral() > 0 && $referral->orders_rewarded >= self::getMaxRewardsPerReferral())) {
                    $referral->markAsCompleted();
                }

                Log::info('Referral reward processed', [
                    'order_id' => $order->id,
                    'referrer_id' => $referrer->id,
                    'referred_id' => $customer->id,
                    'reward_amount' => $rewardAmount,
                ]);

                // Queue email notification
                SendReferralRewardEmail::dispatch($referrer, $customer, $order, $rewardAmount);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to process referral reward', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Calculate reward amount based on settings
     */
    public static function calculateRewardAmount(Order $order): float
    {
        $rewardType = self::getRewardType();
        $rewardAmount = self::getRewardAmount();

        if ($rewardType === 'percentage') {
            $calculatedAmount = ($order->subtotal * $rewardAmount) / 100;
            $maxAmount = self::getMaxRewardAmount();
            
            if ($maxAmount > 0 && $calculatedAmount > $maxAmount) {
                return $maxAmount;
            }
            
            return round($calculatedAmount, 2);
        }

        return $rewardAmount;
    }

    /**
     * Get referral statistics for a user
     */
    public static function getUserStats(User $user): array
    {
        $referrals = Referral::where('referrer_id', $user->id)->get();
        
        return [
            'total_referrals' => $referrals->count(),
            'pending_referrals' => $referrals->where('status', 'pending')->count(),
            'completed_referrals' => $referrals->where('status', 'completed')->count(),
            'total_earnings' => $user->total_referral_earnings,
            'wallet_balance' => $user->wallet_balance,
            'referral_code' => $user->referral_code,
            'referral_link' => $user->referral_link,
        ];
    }

    /**
     * Get program settings for display
     */
    public static function getProgramInfo(): array
    {
        if (!self::isEnabled()) {
            return ['enabled' => false];
        }

        $rewardType = self::getRewardType();
        $rewardAmount = self::getRewardAmount();
        
        $rewardText = $rewardType === 'percentage' 
            ? "{$rewardAmount}%" 
            : "₹" . number_format($rewardAmount, 0);

        return [
            'enabled' => true,
            'reward_type' => $rewardType,
            'reward_amount' => $rewardAmount,
            'reward_text' => $rewardText,
            'min_order_amount' => self::getMinOrderAmount(),
            'first_order_only' => self::isFirstOrderOnly(),
            'max_rewards_per_referral' => self::getMaxRewardsPerReferral(),
            'max_reward_amount' => self::getMaxRewardAmount(),
        ];
    }

    /**
     * Validate referral code
     */
    public static function validateReferralCode(?string $code): array
    {
        if (empty($code)) {
            return ['valid' => false, 'message' => 'No referral code provided'];
        }

        if (!self::isEnabled()) {
            return ['valid' => false, 'message' => 'Referral program is not active'];
        }

        $referrer = User::where('referral_code', strtoupper($code))->first();

        if (!$referrer) {
            return ['valid' => false, 'message' => 'Invalid referral code'];
        }

        return [
            'valid' => true,
            'message' => "You were referred by {$referrer->name}!",
            'referrer_name' => $referrer->name,
        ];
    }
}
