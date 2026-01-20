<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class RazorpaySetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder sets up Razorpay payment method with test credentials.
     * 
     * IMPORTANT: Replace with your actual Razorpay credentials before going live!
     * 
     * To get Razorpay credentials:
     * 1. Go to https://dashboard.razorpay.com/
     * 2. Sign up or login
     * 3. Go to Settings > API Keys
     * 4. Generate Test/Live keys
     */
    public function run(): void
    {
        // Find or create Razorpay payment method
        $razorpay = PaymentMethod::updateOrCreate(
            ['code' => 'razorpay'],
            [
                'name' => 'Razorpay',
                'display_name' => 'Pay Online (Card/UPI/NetBanking)',
                'description' => 'Secure online payment via Razorpay - Cards, UPI, Net Banking, Wallets',
                'icon' => 'fa-credit-card',
                'instructions' => 'You will be redirected to Razorpay secure payment page. All major cards, UPI, Net Banking & Wallets accepted.',
                'is_active' => true, // Enable Razorpay
                'is_online' => true,
                'min_order_amount' => 1, // Minimum ₹1
                'max_order_amount' => 500000, // Maximum ₹5,00,000
                'extra_charge' => 0, // No extra charge
                'extra_charge_type' => 'fixed',
                'sort_order' => 2,
                'settings' => [
                    // ========================================
                    // RAZORPAY TEST CREDENTIALS
                    // ========================================
                    // Replace these with your actual Razorpay API keys
                    // Get them from: https://dashboard.razorpay.com/app/keys
                    
                    'key_id' => 'rzp_test_xxxxxxxxxx', // Your Razorpay Key ID
                    'key_secret' => 'xxxxxxxxxxxxxxxxxx', // Your Razorpay Key Secret
                    'webhook_secret' => '', // Optional: Webhook secret for server-to-server verification
                ],
            ]
        );

        $this->command->info('');
        $this->command->info('====================================');
        $this->command->info('  RAZORPAY SETUP COMPLETED!');
        $this->command->info('====================================');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: You need to add your Razorpay API keys!');
        $this->command->info('');
        $this->command->info('Steps to complete setup:');
        $this->command->info('');
        $this->command->info('1. Go to https://dashboard.razorpay.com/');
        $this->command->info('2. Sign up or login to your account');
        $this->command->info('3. Go to Settings > API Keys');
        $this->command->info('4. Generate Test Mode keys (for local development)');
        $this->command->info('5. Update the keys in one of these ways:');
        $this->command->info('');
        $this->command->info('   Option A: Admin Panel');
        $this->command->info('   - Go to Admin > Payment Methods > Razorpay > Edit');
        $this->command->info('   - Enter your Key ID and Key Secret');
        $this->command->info('');
        $this->command->info('   Option B: Update this seeder and run again');
        $this->command->info('   - Edit database/seeders/RazorpaySetupSeeder.php');
        $this->command->info('   - Replace key_id and key_secret values');
        $this->command->info('   - Run: php artisan db:seed --class=RazorpaySetupSeeder');
        $this->command->info('');
        $this->command->info('====================================');
        $this->command->info('');
    }
}
