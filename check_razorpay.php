<?php

// Quick script to check Razorpay configuration
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;

$razorpay = PaymentMethod::where('code', 'razorpay')->first();

if (!$razorpay) {
    echo "ERROR: Razorpay payment method not found in database!\n";
    echo "Run: php artisan migrate\n";
    exit(1);
}

echo "=== RAZORPAY CONFIGURATION STATUS ===\n\n";
echo "Status: " . ($razorpay->is_active ? "ENABLED" : "DISABLED") . "\n";
echo "Display Name: " . $razorpay->display_name . "\n";
echo "\n--- API Settings ---\n";

$keyId = $razorpay->getSetting('key_id');
$keySecret = $razorpay->getSetting('key_secret');
$webhookSecret = $razorpay->getSetting('webhook_secret');

echo "Key ID: " . ($keyId ? $keyId : "NOT SET ❌") . "\n";
echo "Key Secret: " . ($keySecret ? substr($keySecret, 0, 8) . "..." : "NOT SET ❌") . "\n";
echo "Webhook Secret: " . ($webhookSecret ? "SET ✓" : "NOT SET") . "\n";

echo "\n";

if (!$keyId || !$keySecret) {
    echo "⚠️  PROBLEM: Razorpay API keys are not configured!\n\n";
    echo "To fix this, you need to:\n";
    echo "1. Get API keys from https://dashboard.razorpay.com/app/keys\n";
    echo "2. Run: php artisan razorpay:setup\n";
    echo "   OR\n";
    echo "   Go to Admin Panel > Payment Methods > Razorpay > Edit\n";
} else {
    echo "✓ Razorpay is configured!\n";
    
    // Test the connection
    echo "\nTesting API connection...\n";
    
    $ch = curl_init('https://api.razorpay.com/v1/orders?count=1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $keyId . ':' . $keySecret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "✓ API Connection Successful!\n";
    } else {
        echo "❌ API Connection Failed! HTTP Code: $httpCode\n";
        $data = json_decode($response, true);
        if (isset($data['error']['description'])) {
            echo "Error: " . $data['error']['description'] . "\n";
        }
    }
}
