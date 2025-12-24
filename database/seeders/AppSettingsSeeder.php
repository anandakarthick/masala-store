<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // App Info
            ['key' => 'app_name', 'value' => 'SV Products', 'type' => 'text', 'group' => 'app'],
            ['key' => 'app_version', 'value' => '1.0.0', 'type' => 'text', 'group' => 'app'],
            ['key' => 'app_build_number', 'value' => '1', 'type' => 'text', 'group' => 'app'],
            ['key' => 'min_app_version', 'value' => '1.0.0', 'type' => 'text', 'group' => 'app'],
            ['key' => 'force_app_update', 'value' => '0', 'type' => 'boolean', 'group' => 'app'],
            ['key' => 'app_update_message', 'value' => 'A new version is available. Please update for the best experience.', 'type' => 'text', 'group' => 'app'],
            ['key' => 'play_store_url', 'value' => 'https://play.google.com/store/apps/details?id=com.svproducts', 'type' => 'text', 'group' => 'app'],
            ['key' => 'app_store_url', 'value' => '', 'type' => 'text', 'group' => 'app'],
            
            // Business Info
            ['key' => 'business_name', 'value' => 'SV Products', 'type' => 'text', 'group' => 'business'],
            ['key' => 'business_email', 'value' => 'svproducts2025@gmail.com', 'type' => 'text', 'group' => 'business'],
            ['key' => 'business_phone', 'value' => '+919003096885', 'type' => 'text', 'group' => 'business'],
            ['key' => 'business_address', 'value' => 'Chennai, Tamil Nadu, India', 'type' => 'textarea', 'group' => 'business'],
            ['key' => 'whatsapp_number', 'value' => '919003096885', 'type' => 'text', 'group' => 'business'],
            ['key' => 'whatsapp_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'business'],
            
            // Support Info
            ['key' => 'support_email', 'value' => 'svproducts2025@gmail.com', 'type' => 'text', 'group' => 'support'],
            ['key' => 'support_phone', 'value' => '+919003096885', 'type' => 'text', 'group' => 'support'],
            ['key' => 'support_hours', 'value' => 'Mon-Sat, 9 AM - 6 PM', 'type' => 'text', 'group' => 'support'],
            
            // Currency
            ['key' => 'currency', 'value' => '₹', 'type' => 'text', 'group' => 'general'],
            ['key' => 'currency_code', 'value' => 'INR', 'type' => 'text', 'group' => 'general'],
            
            // Order Settings
            ['key' => 'min_order_amount', 'value' => '0', 'type' => 'number', 'group' => 'order'],
            ['key' => 'free_shipping_amount', 'value' => '500', 'type' => 'number', 'group' => 'order'],
            ['key' => 'default_shipping_charge', 'value' => '50', 'type' => 'number', 'group' => 'order'],
            ['key' => 'cod_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'order'],
            ['key' => 'online_payment_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'order'],
            
            // Referral Settings
            ['key' => 'referral_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'referral'],
            ['key' => 'referral_reward_percentage', 'value' => '5', 'type' => 'number', 'group' => 'referral'],
            ['key' => 'referral_min_order_amount', 'value' => '100', 'type' => 'number', 'group' => 'referral'],
            ['key' => 'referral_max_reward_per_order', 'value' => '100', 'type' => 'number', 'group' => 'referral'],
            
            // Policy Pages
            ['key' => 'privacy_policy', 'value' => $this->getPrivacyPolicy(), 'type' => 'textarea', 'group' => 'pages'],
            ['key' => 'terms_conditions', 'value' => $this->getTermsConditions(), 'type' => 'textarea', 'group' => 'pages'],
            ['key' => 'refund_policy', 'value' => $this->getRefundPolicy(), 'type' => 'textarea', 'group' => 'pages'],
            ['key' => 'shipping_policy', 'value' => $this->getShippingPolicy(), 'type' => 'textarea', 'group' => 'pages'],
            ['key' => 'about_us', 'value' => $this->getAboutUs(), 'type' => 'textarea', 'group' => 'pages'],
            
            // FAQs (JSON)
            ['key' => 'faqs', 'value' => json_encode($this->getFaqs()), 'type' => 'json', 'group' => 'support'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                ]
            );
        }

        $this->command->info('App settings seeded successfully!');
    }

    private function getPrivacyPolicy(): string
    {
        return <<<EOT
Privacy Policy

Last updated: December 2024

Your privacy is important to us. This Privacy Policy explains how SV Products ("we", "our", or "us") collects, uses, discloses, and safeguards your information when you use our mobile application.

INFORMATION WE COLLECT

Personal Information:
• Name, email address, and phone number
• Delivery address and location data
• Order history and preferences
• Payment information (processed securely through payment gateways)

Automatically Collected Information:
• Device information (device type, operating system)
• App usage data and analytics
• IP address and location data

HOW WE USE YOUR INFORMATION

We use the information we collect to:
• Process and deliver your orders
• Send order updates and delivery notifications
• Provide customer support
• Improve our services and user experience
• Send promotional offers and updates (with your consent)
• Prevent fraud and ensure security

DATA SHARING

We may share your information with:
• Delivery partners to fulfill orders
• Payment processors for secure transactions
• Analytics providers to improve our services

We do not sell your personal information to third parties.

DATA SECURITY

We implement appropriate security measures including:
• Encryption of sensitive data
• Secure servers and databases
• Regular security audits
• Access controls and authentication

YOUR RIGHTS

You have the right to:
• Access your personal data
• Correct inaccurate information
• Delete your account and data
• Opt-out of promotional communications

CONTACT US

For questions about this Privacy Policy, please contact us at:
Email: svproducts2025@gmail.com
Phone: +919003096885
EOT;
    }

    private function getTermsConditions(): string
    {
        return <<<EOT
Terms & Conditions

Last updated: December 2024

By downloading, installing, or using the SV Products mobile application, you agree to be bound by these Terms and Conditions.

1. ACCOUNT REGISTRATION

• You must provide accurate and complete information when creating an account
• You are responsible for maintaining the confidentiality of your account
• You must be at least 18 years old to use our services
• One account per person is allowed

2. ORDERS AND PAYMENTS

• All orders are subject to product availability
• Prices are subject to change without prior notice
• Payment must be made at the time of order or upon delivery (COD)
• We reserve the right to cancel orders in case of pricing errors

3. PRODUCT INFORMATION

• We strive to display accurate product information
• Actual products may slightly vary from images
• Product weights are approximate and may vary slightly

4. DELIVERY

• Delivery times are estimates and not guaranteed
• Risk of loss passes to you upon delivery
• Someone must be available to receive the delivery
• Additional charges may apply for remote areas

5. CANCELLATIONS AND MODIFICATIONS

• Orders can be cancelled before they are shipped
• Order modifications may not be possible after confirmation
• Refunds for cancelled orders will be processed within 7 business days

6. INTELLECTUAL PROPERTY

• All content in the app is owned by SV Products
• You may not copy, reproduce, or distribute our content
• Trademarks and logos are protected by law

7. LIMITATION OF LIABILITY

• We are not liable for indirect or consequential damages
• Our liability is limited to the order value
• We are not responsible for third-party services

8. GOVERNING LAW

These terms are governed by the laws of India. Any disputes shall be subject to the jurisdiction of courts in Chennai, Tamil Nadu.

9. CHANGES TO TERMS

We reserve the right to modify these terms at any time. Continued use of the app constitutes acceptance of modified terms.

10. CONTACT

For questions about these Terms, contact us at:
Email: svproducts2025@gmail.com
EOT;
    }

    private function getRefundPolicy(): string
    {
        return <<<EOT
Refund Policy

Last updated: December 2024

At SV Products, we want you to be completely satisfied with your purchase. This policy outlines our refund and return procedures.

ELIGIBILITY FOR REFUND

You may request a refund within 7 days of delivery if:
• The product is damaged during transit
• You received the wrong item
• The product is defective or of poor quality
• The product is expired or near expiry

NON-REFUNDABLE ITEMS

The following are not eligible for refunds:
• Items that have been opened or used (except for quality issues)
• Items returned after 7 days of delivery
• Items damaged due to customer mishandling
• Perishable items (unless quality issues)

HOW TO REQUEST A REFUND

1. Contact our support team within 7 days of delivery
2. Provide your order number and reason for refund
3. Attach clear photos showing the issue
4. Our team will review your request within 24-48 hours

REFUND PROCESS

Once your refund is approved:
• Refunds are processed within 5-7 business days
• Amount will be credited to your wallet or original payment method
• You will receive confirmation via email/SMS

PARTIAL REFUNDS

Partial refunds may be granted if:
• Some items in the order are acceptable
• Product quality is acceptable but not as described

SHIPPING CHARGES

• Shipping charges are non-refundable for change of mind
• Full shipping refund for damaged or wrong products
• We may arrange pickup for returns

EXCHANGES

• Exchanges are subject to product availability
• Exchange requests must be made within 7 days

CONTACT US

For refund requests or questions:
Email: svproducts2025@gmail.com
Phone: +919003096885
WhatsApp: +919003096885
EOT;
    }

    private function getShippingPolicy(): string
    {
        return <<<EOT
Shipping Policy

Last updated: December 2024

SV Products is committed to delivering your orders quickly and safely.

SHIPPING CHARGES

• FREE shipping on orders above ₹500
• Standard shipping charge: ₹50 for orders below ₹500
• Additional charges may apply for remote areas

DELIVERY TIME

Estimated delivery times:
• Chennai City: 1-2 business days
• Tamil Nadu (other cities): 2-4 business days
• Metro cities: 3-5 business days
• Other cities: 5-7 business days
• Remote areas: 7-10 business days

Note: Delivery times are estimates and may vary due to unforeseen circumstances.

ORDER PROCESSING

• Orders placed before 2 PM are processed the same day
• Orders placed after 2 PM are processed the next business day
• Orders are not processed on Sundays and public holidays
• You will receive order confirmation via SMS/email

TRACKING YOUR ORDER

• Tracking details are sent via SMS/email once shipped
• Track your order from the "My Orders" section in the app
• Contact support if tracking is not updated for 48 hours

DELIVERY PROCESS

• Our delivery partner will contact you before delivery
• Please ensure someone is available to receive the order
• Check the package before accepting delivery
• Report any damage immediately to the delivery person

DELIVERY ISSUES

If you face any delivery issues:
• Contact our support team immediately
• Provide your order number and tracking details
• We will investigate and resolve the issue promptly

AREAS WE SERVE

We currently deliver across India. Some remote areas may have limited service or additional charges.

CONTACT US

For shipping queries:
Email: svproducts2025@gmail.com
Phone: +919003096885
EOT;
    }

    private function getAboutUs(): string
    {
        return <<<EOT
About SV Products

Welcome to SV Products - Your trusted destination for premium quality masalas and spices!

OUR STORY

SV Products was founded with a passion to bring authentic, high-quality spices and masalas directly to your kitchen. We believe that great food starts with great ingredients, and we're committed to providing you with the finest products.

OUR MISSION

To make premium quality spices and masalas accessible to everyone while maintaining the highest standards of quality, hygiene, and customer service.

WHAT MAKES US DIFFERENT

• Premium Quality: We source only the finest ingredients
• Freshness Guaranteed: Our products are freshly ground and packaged
• No Additives: Pure spices without artificial colors or preservatives
• Hygienically Processed: State-of-the-art processing facility
• Fast Delivery: Quick and reliable delivery to your doorstep

OUR PRODUCTS

We offer a wide range of products including:
• Traditional Masala Powders
• Whole Spices
• Blended Spice Mixes
• Ready-to-Cook Pastes
• Pickles and Condiments

QUALITY ASSURANCE

Every product undergoes strict quality checks to ensure:
• Purity and authenticity
• Optimal freshness
• Proper packaging
• Safe handling

CONTACT US

We'd love to hear from you!
Email: svproducts2025@gmail.com
Phone: +919003096885

Thank you for choosing SV Products!
EOT;
    }

    private function getFaqs(): array
    {
        return [
            [
                'question' => 'How do I track my order?',
                'answer' => 'You can track your order from the "My Orders" section in your account. Click on any order to see its current status and tracking details. You will also receive tracking updates via SMS and email.',
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept Cash on Delivery (COD), UPI payments (Google Pay, PhonePe, Paytm), Credit/Debit cards, and Net Banking. All online payments are processed securely.',
            ],
            [
                'question' => 'How can I cancel my order?',
                'answer' => 'You can cancel your order before it is shipped by contacting our support team via phone or WhatsApp. Once the order is shipped, cancellation may not be possible, but you can refuse delivery.',
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We accept returns within 7 days of delivery for damaged, defective, or incorrect products. Please contact our support team with your order number and photos of the issue to initiate a return.',
            ],
            [
                'question' => 'How do I use my wallet balance?',
                'answer' => 'Your wallet balance is automatically applied at checkout. You can choose to use it partially or fully towards your order. Wallet balance can be earned through referrals, refunds, and promotional offers.',
            ],
            [
                'question' => 'How does the referral program work?',
                'answer' => 'Share your unique referral code with friends and family. When they make their first purchase using your code, both you and your friend earn wallet credits! Check the "Refer & Earn" section for more details.',
            ],
            [
                'question' => 'Is Cash on Delivery available?',
                'answer' => 'Yes, Cash on Delivery (COD) is available for most locations. COD availability will be shown at checkout based on your delivery address.',
            ],
            [
                'question' => 'How do I contact customer support?',
                'answer' => 'You can reach our support team via: Phone: +919003096885, WhatsApp: +919003096885, Email: svproducts2025@gmail.com. Our support hours are Mon-Sat, 9 AM - 6 PM.',
            ],
        ];
    }
}
