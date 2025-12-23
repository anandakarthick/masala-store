<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class FirstTimeCustomerService
{
    /**
     * Check if first-time customer discount is enabled
     */
    public static function isEnabled(): bool
    {
        return (bool) Setting::get('first_time_discount_enabled', false);
    }

    /**
     * Get the discount percentage
     */
    public static function getDiscountPercentage(): float
    {
        return (float) Setting::get('first_time_discount_percentage', 0);
    }

    /**
     * Get the maximum number of unique customers eligible
     */
    public static function getMaxCustomers(): int
    {
        return (int) Setting::get('first_time_discount_max_customers', 0);
    }

    /**
     * Get minimum order amount required for discount
     */
    public static function getMinOrderAmount(): float
    {
        return (float) Setting::get('first_time_discount_min_order', 0);
    }

    /**
     * Get maximum discount amount (cap)
     */
    public static function getMaxDiscountAmount(): float
    {
        return (float) Setting::get('first_time_discount_max_amount', 0);
    }

    /**
     * Get number of customers who have already used this offer
     */
    public static function getUsedCount(): int
    {
        return Order::whereNotNull('first_time_discount_applied')
            ->where('first_time_discount_applied', '>', 0)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get remaining slots
     */
    public static function getRemainingSlots(): int
    {
        $max = self::getMaxCustomers();
        if ($max <= 0) {
            return 0;
        }
        $used = self::getUsedCount();
        return max(0, $max - $used);
    }

    /**
     * Check if current user is a first-time customer
     */
    public static function isFirstTimeCustomer(?int $userId = null): bool
    {
        if ($userId === null) {
            if (!Auth::check()) {
                return false;
            }
            $userId = Auth::id();
        }

        // Check if user has any completed orders (not cancelled)
        $hasOrders = Order::where('user_id', $userId)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        return !$hasOrders;
    }

    /**
     * Check if customer is eligible for first-time discount
     */
    public static function isEligible(?int $userId = null, float $orderAmount = 0): array
    {
        $result = [
            'eligible' => false,
            'reason' => '',
            'discount_percentage' => 0,
            'discount_amount' => 0,
        ];

        // Check if feature is enabled
        if (!self::isEnabled()) {
            $result['reason'] = 'First-time customer discount is not active.';
            return $result;
        }

        // Check if user is logged in
        if ($userId === null) {
            if (!Auth::check()) {
                $result['reason'] = 'Please login to avail first-time customer discount.';
                return $result;
            }
            $userId = Auth::id();
        }

        // Check if user is first-time customer
        if (!self::isFirstTimeCustomer($userId)) {
            $result['reason'] = 'This offer is only for first-time customers.';
            return $result;
        }

        // Check if slots are available
        $remainingSlots = self::getRemainingSlots();
        if ($remainingSlots <= 0) {
            $result['reason'] = 'Sorry, the first-time customer offer has reached its limit.';
            return $result;
        }

        // Check minimum order amount
        $minOrder = self::getMinOrderAmount();
        if ($orderAmount > 0 && $orderAmount < $minOrder) {
            $result['reason'] = "Minimum order of ₹" . number_format($minOrder, 2) . " required for first-time discount.";
            return $result;
        }

        // Calculate discount
        $percentage = self::getDiscountPercentage();
        $discountAmount = ($orderAmount * $percentage) / 100;

        // Apply cap if set
        $maxDiscount = self::getMaxDiscountAmount();
        if ($maxDiscount > 0 && $discountAmount > $maxDiscount) {
            $discountAmount = $maxDiscount;
        }

        $result['eligible'] = true;
        $result['discount_percentage'] = $percentage;
        $result['discount_amount'] = round($discountAmount, 2);
        $result['remaining_slots'] = $remainingSlots;

        return $result;
    }

    /**
     * Calculate discount amount
     */
    public static function calculateDiscount(float $orderAmount, ?int $userId = null): float
    {
        $eligibility = self::isEligible($userId, $orderAmount);
        
        if (!$eligibility['eligible']) {
            return 0;
        }

        return $eligibility['discount_amount'];
    }

    /**
     * Get display message for eligible customers
     */
    public static function getOfferMessage(): ?string
    {
        if (!self::isEnabled()) {
            return null;
        }

        $percentage = self::getDiscountPercentage();
        $remaining = self::getRemainingSlots();
        $minOrder = self::getMinOrderAmount();
        $maxDiscount = self::getMaxDiscountAmount();

        if ($remaining <= 0) {
            return null;
        }

        $message = "🎉 First {$remaining} customers get {$percentage}% OFF!";
        
        if ($minOrder > 0) {
            $message .= " (Min. order ₹" . number_format($minOrder, 0) . ")";
        }
        
        if ($maxDiscount > 0) {
            $message .= " (Max ₹" . number_format($maxDiscount, 0) . " off)";
        }

        return $message;
    }
}
