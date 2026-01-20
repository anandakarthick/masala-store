<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use Illuminate\Console\Command;

class SetupRazorpay extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'razorpay:setup 
                            {--key= : Razorpay Key ID}
                            {--secret= : Razorpay Key Secret}
                            {--webhook-secret= : Razorpay Webhook Secret (optional)}
                            {--test : Use test mode placeholder keys}
                            {--disable : Disable Razorpay instead of enabling}';

    /**
     * The console command description.
     */
    protected $description = 'Setup or update Razorpay payment gateway configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('====================================');
        $this->info('  RAZORPAY SETUP WIZARD');
        $this->info('====================================');
        $this->info('');

        // Find or create Razorpay payment method
        $razorpay = PaymentMethod::where('code', 'razorpay')->first();

        if (!$razorpay) {
            $this->error('Razorpay payment method not found in database.');
            $this->info('Please run migrations first: php artisan migrate');
            return 1;
        }

        // Handle disable option
        if ($this->option('disable')) {
            $razorpay->update(['is_active' => false]);
            $this->warn('Razorpay has been DISABLED.');
            return 0;
        }

        // Get credentials
        $keyId = $this->option('key');
        $keySecret = $this->option('secret');
        $webhookSecret = $this->option('webhook-secret') ?? '';

        // If test mode, use placeholder
        if ($this->option('test')) {
            $keyId = 'rzp_test_xxxxxxxxxx';
            $keySecret = 'test_secret_xxxxxxxxxx';
            $this->warn('Using TEST MODE placeholder keys.');
            $this->warn('You must replace these with real keys from Razorpay Dashboard!');
        }

        // Interactive mode if no keys provided
        if (!$keyId || !$keySecret) {
            $this->info('No keys provided via options. Entering interactive mode...');
            $this->info('');
            $this->info('Get your API keys from: https://dashboard.razorpay.com/app/keys');
            $this->info('');

            $keyId = $this->ask('Enter Razorpay Key ID (starts with rzp_test_ or rzp_live_)');
            
            if (!$keyId) {
                $this->error('Key ID is required.');
                return 1;
            }

            $keySecret = $this->secret('Enter Razorpay Key Secret');
            
            if (!$keySecret) {
                $this->error('Key Secret is required.');
                return 1;
            }

            $webhookSecret = $this->ask('Enter Webhook Secret (optional, press Enter to skip)', '');
        }

        // Validate key format
        if (!str_starts_with($keyId, 'rzp_')) {
            $this->warn('Warning: Key ID should start with "rzp_test_" (test) or "rzp_live_" (production)');
            
            if (!$this->confirm('Continue anyway?', false)) {
                return 1;
            }
        }

        // Update settings
        $settings = [
            'key_id' => $keyId,
            'key_secret' => $keySecret,
            'webhook_secret' => $webhookSecret,
        ];

        $razorpay->update([
            'is_active' => true,
            'settings' => $settings,
        ]);

        $this->info('');
        $this->info('====================================');
        $this->info('  RAZORPAY SETUP COMPLETED!');
        $this->info('====================================');
        $this->info('');
        $this->info('Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Status', '<fg=green>ENABLED</>'],
                ['Key ID', $this->maskSecret($keyId, 12)],
                ['Key Secret', $this->maskSecret($keySecret, 8)],
                ['Webhook Secret', $webhookSecret ? $this->maskSecret($webhookSecret, 8) : 'Not Set'],
                ['Mode', str_contains($keyId, '_test_') ? 'TEST' : 'LIVE'],
            ]
        );

        $this->info('');
        
        if (str_contains($keyId, '_test_')) {
            $this->info('Test Card: 4111 1111 1111 1111');
            $this->info('Test UPI: success@razorpay');
            $this->info('OTP: 1234');
        }

        $this->info('');
        $this->info('You can now accept online payments!');
        $this->info('Admin Panel: /admin/payment-methods');
        $this->info('');

        return 0;
    }

    /**
     * Mask a secret string for display
     */
    private function maskSecret(string $secret, int $showChars = 4): string
    {
        if (strlen($secret) <= $showChars) {
            return str_repeat('*', strlen($secret));
        }

        return substr($secret, 0, $showChars) . str_repeat('*', strlen($secret) - $showChars);
    }
}
