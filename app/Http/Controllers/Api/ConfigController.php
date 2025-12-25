<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ConfigController extends Controller
{
    /**
     * Get app configuration for mobile app
     */
    public function index(): JsonResponse
    {
        // Use getFresh to always get latest values from database
        
        // App Info
        $appName = Setting::getFresh('app_name', config('app.name', 'SV Products'));
        $appVersion = Setting::getFresh('app_version', '1.0.0');
        $appBuildNumber = Setting::getFresh('app_build_number', '1');
        $minAppVersion = Setting::getFresh('min_app_version', '1.0.0');
        $forceUpdate = Setting::getFresh('force_app_update', '0');
        $updateMessage = Setting::getFresh('app_update_message', 'A new version is available. Please update for the best experience.');
        
        // Store URLs
        $playStoreUrl = Setting::getFresh('play_store_url', 'https://play.google.com/store/apps/details?id=com.svproducts');
        $appStoreUrl = Setting::getFresh('app_store_url', '');
        
        // Business Info
        $businessName = Setting::getFresh('business_name', 'SV Products');
        $businessEmail = Setting::getFresh('business_email', 'support@svproducts.com');
        $businessPhone = Setting::getFresh('business_phone', '');
        $businessAddress = Setting::getFresh('business_address', '');
        $whatsappNumber = Setting::getFresh('whatsapp_number', '');
        $whatsappEnabled = Setting::getFresh('whatsapp_enabled', '0');
        
        // Convert to boolean properly
        $whatsappEnabledBool = in_array($whatsappEnabled, ['1', 'true', true, 1], true);
        $forceUpdateBool = in_array($forceUpdate, ['1', 'true', true, 1], true);
        
        // Log for debugging
        Log::info('Config API - WhatsApp settings', [
            'whatsapp_number' => $whatsappNumber,
            'whatsapp_enabled_raw' => $whatsappEnabled,
            'whatsapp_enabled_bool' => $whatsappEnabledBool,
            'business_phone' => $businessPhone,
        ]);
        
        // Currency
        $currency = Setting::getFresh('currency', '₹');
        $currencyCode = Setting::getFresh('currency_code', 'INR');
        
        // Order Settings
        $minOrderAmount = (float) Setting::getFresh('min_order_amount', 0);
        $freeShippingAmount = (float) Setting::getFresh('free_shipping_amount', 500);
        $defaultShippingCharge = (float) Setting::getFresh('default_shipping_charge', 50);
        $codEnabled = in_array(Setting::getFresh('cod_enabled', '1'), ['1', 'true', true, 1], true);
        $onlinePaymentEnabled = in_array(Setting::getFresh('online_payment_enabled', '0'), ['1', 'true', true, 1], true);
        
        // Referral Settings
        $referralEnabled = in_array(Setting::getFresh('referral_enabled', '1'), ['1', 'true', true, 1], true);
        $referralRewardPercentage = (float) Setting::getFresh('referral_reward_percentage', 5);
        $referralMinOrderAmount = (float) Setting::getFresh('referral_min_order_amount', 100);
        $referralMaxRewardPerOrder = (float) Setting::getFresh('referral_max_reward_per_order', 100);
        
        // Social Media Links
        $socialLinks = [];
        if (Schema::hasTable('social_media_links')) {
            try {
                $socialLinks = \App\Models\SocialMediaLink::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(function ($link) {
                        return [
                            'platform' => $link->platform,
                            'url' => $link->url,
                        ];
                    })->toArray();
            } catch (\Exception $e) {
                // Ignore
            }
        }
        
        // Policy Pages Content
        $privacyPolicy = Setting::getFresh('privacy_policy', $this->getDefaultPrivacyPolicy());
        $termsConditions = Setting::getFresh('terms_conditions', $this->getDefaultTermsConditions());
        $refundPolicy = Setting::getFresh('refund_policy', $this->getDefaultRefundPolicy());
        $shippingPolicy = Setting::getFresh('shipping_policy', $this->getDefaultShippingPolicy());
        $aboutUs = Setting::getFresh('about_us', '');
        
        // Support Info
        $supportEmail = Setting::getFresh('support_email', $businessEmail);
        $supportPhone = Setting::getFresh('support_phone', $businessPhone);
        $supportHours = Setting::getFresh('support_hours', 'Mon-Sat, 9 AM - 6 PM');
        
        // FAQs
        $faqs = $this->getFaqs();

        return response()->json([
            'success' => true,
            'data' => [
                // App Info
                'app' => [
                    'name' => $appName,
                    'version' => $appVersion,
                    'build_number' => $appBuildNumber,
                    'min_version' => $minAppVersion,
                    'force_update' => $forceUpdateBool,
                    'update_message' => $updateMessage,
                    'play_store_url' => $playStoreUrl,
                    'app_store_url' => $appStoreUrl,
                    'logo_url' => Setting::logo(),
                ],
                
                // Business Info
                'business' => [
                    'name' => $businessName,
                    'email' => $businessEmail,
                    'phone' => $businessPhone,
                    'address' => $businessAddress,
                    'whatsapp' => $whatsappNumber, // Always return the number
                    'whatsapp_enabled' => $whatsappEnabledBool,
                ],
                
                // Currency
                'currency' => $currency,
                'currency_code' => $currencyCode,
                
                // Order Settings
                'order' => [
                    'min_amount' => $minOrderAmount,
                    'free_shipping_amount' => $freeShippingAmount,
                    'default_shipping_charge' => $defaultShippingCharge,
                    'cod_enabled' => $codEnabled,
                    'online_payment_enabled' => $onlinePaymentEnabled,
                ],
                
                // Referral Settings
                'referral' => [
                    'enabled' => $referralEnabled,
                    'reward_percentage' => $referralRewardPercentage,
                    'min_order_amount' => $referralMinOrderAmount,
                    'max_reward_per_order' => $referralMaxRewardPerOrder,
                ],
                
                // Social Media
                'social_links' => $socialLinks,
                
                // Support
                'support' => [
                    'email' => $supportEmail,
                    'phone' => $supportPhone,
                    'hours' => $supportHours,
                    'faqs' => $faqs,
                ],
                
                // Policy Pages
                'pages' => [
                    'privacy_policy' => $privacyPolicy,
                    'terms_conditions' => $termsConditions,
                    'refund_policy' => $refundPolicy,
                    'shipping_policy' => $shippingPolicy,
                    'about_us' => $aboutUs,
                ],
                
                // API Keys (only non-sensitive ones)
                'google_maps_api_key' => config('services.google_maps.api_key'),
            ],
        ]);
    }

    /**
     * Get FAQs from settings or return defaults
     */
    private function getFaqs(): array
    {
        $faqsJson = Setting::getFresh('faqs');
        
        if ($faqsJson) {
            $faqs = json_decode($faqsJson, true);
            if (is_array($faqs)) {
                return $faqs;
            }
        }

        // Default FAQs
        return [
            [
                'question' => 'How do I track my order?',
                'answer' => 'You can track your order from the "My Orders" section in your account. Click on any order to see its current status and tracking details.',
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept Cash on Delivery (COD), UPI payments, Credit/Debit cards, and Net Banking.',
            ],
            [
                'question' => 'How can I cancel my order?',
                'answer' => 'You can cancel your order before it is shipped by contacting our support team via phone or WhatsApp.',
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We accept returns within 7 days of delivery for damaged or incorrect products. Please contact our support team with photos of the issue.',
            ],
            [
                'question' => 'How do I use my wallet balance?',
                'answer' => 'Your wallet balance will be automatically shown at checkout. You can choose to use it partially or fully towards your order.',
            ],
            [
                'question' => 'How does the referral program work?',
                'answer' => 'Share your referral code with friends. When they make their first purchase, both you and your friend earn rewards!',
            ],
        ];
    }

    /**
     * Default Privacy Policy
     */
    private function getDefaultPrivacyPolicy(): string
    {
        return "Privacy Policy\n\n" .
            "Last updated: " . date('F d, Y') . "\n\n" .
            "Your privacy is important to us. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our mobile application.\n\n" .
            "Information We Collect\n" .
            "We collect information that you provide directly to us, including:\n" .
            "• Name, email address, and phone number\n" .
            "• Delivery address\n" .
            "• Order history and preferences\n" .
            "• Payment information (processed securely)\n\n" .
            "How We Use Your Information\n" .
            "We use the information we collect to:\n" .
            "• Process and deliver your orders\n" .
            "• Send order updates and notifications\n" .
            "• Improve our services and user experience\n" .
            "• Send promotional offers (with your consent)\n\n" .
            "Data Security\n" .
            "We implement appropriate security measures to protect your personal information.\n\n" .
            "Contact Us\n" .
            "If you have questions about this Privacy Policy, please contact us.";
    }

    /**
     * Default Terms & Conditions
     */
    private function getDefaultTermsConditions(): string
    {
        return "Terms & Conditions\n\n" .
            "Last updated: " . date('F d, Y') . "\n\n" .
            "By using our app, you agree to these Terms and Conditions. Please read them carefully.\n\n" .
            "1. Account Registration\n" .
            "You must provide accurate information when creating an account. You are responsible for maintaining the security of your account.\n\n" .
            "2. Orders and Payments\n" .
            "All orders are subject to availability. Prices are subject to change without notice. Payment must be made at the time of order or upon delivery (COD).\n\n" .
            "3. Delivery\n" .
            "We will make every effort to deliver your order within the estimated timeframe. Delivery times may vary based on location and availability.\n\n" .
            "4. Cancellations\n" .
            "Orders can be cancelled before they are shipped. Once shipped, cancellations may not be possible.\n\n" .
            "5. Limitation of Liability\n" .
            "We are not liable for any indirect, incidental, or consequential damages arising from your use of our services.\n\n" .
            "6. Changes to Terms\n" .
            "We reserve the right to modify these terms at any time. Continued use of the app constitutes acceptance of modified terms.";
    }

    /**
     * Default Refund Policy
     */
    private function getDefaultRefundPolicy(): string
    {
        return "Refund Policy\n\n" .
            "Last updated: " . date('F d, Y') . "\n\n" .
            "We want you to be completely satisfied with your purchase.\n\n" .
            "Eligibility for Refund\n" .
            "You may request a refund within 7 days of delivery if:\n" .
            "• The product is damaged or defective\n" .
            "• You received the wrong item\n" .
            "• The product quality does not match the description\n\n" .
            "How to Request a Refund\n" .
            "1. Contact our support team within 7 days of delivery\n" .
            "2. Provide your order number and photos of the issue\n" .
            "3. Our team will review your request within 24-48 hours\n\n" .
            "Refund Process\n" .
            "• Approved refunds will be processed within 5-7 business days\n" .
            "• Refunds will be credited to your wallet or original payment method\n" .
            "• Shipping charges are non-refundable unless the return is due to our error\n\n" .
            "Non-Refundable Items\n" .
            "• Items that have been opened or used\n" .
            "• Items returned after 7 days\n" .
            "• Items damaged due to mishandling by the customer";
    }

    /**
     * Default Shipping Policy
     */
    private function getDefaultShippingPolicy(): string
    {
        $freeShipping = Setting::getFresh('free_shipping_amount', 500);
        $shippingCharge = Setting::getFresh('default_shipping_charge', 50);
        
        return "Shipping Policy\n\n" .
            "Last updated: " . date('F d, Y') . "\n\n" .
            "Shipping Charges\n" .
            "• Free shipping on orders above ₹{$freeShipping}\n" .
            "• Standard shipping charge: ₹{$shippingCharge} for orders below ₹{$freeShipping}\n\n" .
            "Delivery Time\n" .
            "• Metro cities: 2-4 business days\n" .
            "• Other cities: 4-7 business days\n" .
            "• Remote areas: 7-10 business days\n\n" .
            "Order Processing\n" .
            "• Orders placed before 2 PM are processed the same day\n" .
            "• Orders placed after 2 PM are processed the next business day\n" .
            "• Orders are not processed on Sundays and public holidays\n\n" .
            "Tracking Your Order\n" .
            "Once your order is shipped, you will receive a notification with tracking details. You can also track your order from the 'My Orders' section in the app.\n\n" .
            "Delivery Issues\n" .
            "If you face any issues with delivery, please contact our support team immediately.";
    }
}
