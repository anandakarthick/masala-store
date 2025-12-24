<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class SupportController extends Controller
{
    /**
     * Get help & support information
     */
    public function index(): JsonResponse
    {
        // Get contact information
        $businessName = Setting::get('business_name', 'SV Products');
        $businessEmail = Setting::get('business_email', 'support@svproducts.com');
        $businessPhone = Setting::get('business_phone', '');
        $whatsappNumber = Setting::get('whatsapp_number', '');
        $whatsappEnabled = Setting::get('whatsapp_enabled', false);
        $businessAddress = Setting::get('business_address', '');

        // Get social media links (check if table exists)
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
                            'icon' => $link->icon ?? null,
                        ];
                    })->toArray();
            } catch (\Exception $e) {
                // Table might exist but model might have issues
                $socialLinks = [];
            }
        }

        // Get pages (check if table exists)
        $pages = [
            'faq' => null,
            'terms' => null,
            'privacy' => null,
            'refund' => null,
            'shipping' => null,
        ];

        if (Schema::hasTable('pages')) {
            try {
                $faqPage = Page::where('slug', 'faq')->where('is_active', true)->first();
                $termsPage = Page::where('slug', 'terms-and-conditions')->where('is_active', true)->first();
                $privacyPage = Page::where('slug', 'privacy-policy')->where('is_active', true)->first();
                $refundPage = Page::where('slug', 'refund-policy')->where('is_active', true)->first();
                $shippingPage = Page::where('slug', 'shipping-policy')->where('is_active', true)->first();

                $pages = [
                    'faq' => $faqPage ? ['title' => $faqPage->title, 'slug' => $faqPage->slug] : null,
                    'terms' => $termsPage ? ['title' => $termsPage->title, 'slug' => $termsPage->slug] : null,
                    'privacy' => $privacyPage ? ['title' => $privacyPage->title, 'slug' => $privacyPage->slug] : null,
                    'refund' => $refundPage ? ['title' => $refundPage->title, 'slug' => $refundPage->slug] : null,
                    'shipping' => $shippingPage ? ['title' => $shippingPage->title, 'slug' => $shippingPage->slug] : null,
                ];
            } catch (\Exception $e) {
                // Keep default null values
            }
        }

        // Common FAQs
        $faqs = [
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

        return response()->json([
            'success' => true,
            'data' => [
                'contact' => [
                    'business_name' => $businessName,
                    'email' => $businessEmail,
                    'phone' => $businessPhone,
                    'whatsapp' => $whatsappEnabled ? $whatsappNumber : null,
                    'address' => $businessAddress,
                ],
                'social_links' => $socialLinks,
                'faqs' => $faqs,
                'pages' => $pages,
            ],
        ]);
    }

    /**
     * Get page content
     */
    public function getPage(string $slug): JsonResponse
    {
        if (!Schema::hasTable('pages')) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        $page = Page::where('slug', $slug)->where('is_active', true)->first();

        if (!$page) {
            // Return default content for common pages
            $defaultPages = [
                'privacy-policy' => [
                    'title' => 'Privacy Policy',
                    'content' => 'Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information when you use our app and services.',
                ],
                'terms-and-conditions' => [
                    'title' => 'Terms & Conditions',
                    'content' => 'By using our app, you agree to these Terms and Conditions. Please read them carefully before making any purchases.',
                ],
                'refund-policy' => [
                    'title' => 'Refund Policy',
                    'content' => 'We want you to be satisfied with your purchase. If you are not happy with your order, you may request a refund within 7 days of delivery.',
                ],
                'shipping-policy' => [
                    'title' => 'Shipping Policy',
                    'content' => 'We ship to all locations across India. Standard delivery takes 3-7 business days. Free shipping on orders above ₹500.',
                ],
            ];

            if (isset($defaultPages[$slug])) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'title' => $defaultPages[$slug]['title'],
                        'content' => $defaultPages[$slug]['content'],
                        'slug' => $slug,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $page->title,
                'content' => $page->content,
                'slug' => $page->slug,
            ],
        ]);
    }
}
