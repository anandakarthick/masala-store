<?php

// Quick script to check and fix shipping settings
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

echo "=== SHIPPING SETTINGS CHECK ===\n\n";

// Check database values directly
echo "1. Database Values (Raw):\n";
$freeShipping = \DB::table('settings')->where('key', 'free_shipping_amount')->first();
$defaultShipping = \DB::table('settings')->where('key', 'default_shipping_charge')->first();

echo "   free_shipping_amount: " . ($freeShipping ? $freeShipping->value : "NOT FOUND") . "\n";
echo "   default_shipping_charge: " . ($defaultShipping ? $defaultShipping->value : "NOT FOUND") . "\n";

// Check cached values
echo "\n2. Cached Values:\n";
echo "   free_shipping_amount (cached): " . Setting::get('free_shipping_amount', 'NOT SET') . "\n";
echo "   default_shipping_charge (cached): " . Setting::get('default_shipping_charge', 'NOT SET') . "\n";

// Check fresh values (bypass cache)
echo "\n3. Fresh Values (Bypass Cache):\n";
echo "   free_shipping_amount (fresh): " . Setting::getFresh('free_shipping_amount', 'NOT SET') . "\n";
echo "   default_shipping_charge (fresh): " . Setting::getFresh('default_shipping_charge', 'NOT SET') . "\n";

// Check helper methods
echo "\n4. Helper Method Values:\n";
echo "   Setting::freeShippingAmount(): " . Setting::freeShippingAmount() . "\n";
echo "   Setting::defaultShippingCharge(): " . Setting::defaultShippingCharge() . "\n";

// Clear cache and recheck
echo "\n5. Clearing Cache...\n";
Cache::forget('setting.free_shipping_amount');
Cache::forget('setting.default_shipping_charge');
echo "   Cache cleared!\n";

echo "\n6. After Cache Clear:\n";
echo "   Setting::freeShippingAmount(): " . Setting::freeShippingAmount() . "\n";
echo "   Setting::defaultShippingCharge(): " . Setting::defaultShippingCharge() . "\n";

// If settings don't exist, create them
if (!$freeShipping || !$defaultShipping) {
    echo "\n7. Creating missing settings...\n";
    
    if (!$freeShipping) {
        Setting::set('free_shipping_amount', '0', 'text', 'general');
        echo "   Created free_shipping_amount = 0\n";
    }
    
    if (!$defaultShipping) {
        Setting::set('default_shipping_charge', '0', 'text', 'general');
        echo "   Created default_shipping_charge = 0\n";
    }
    
    echo "\n8. Final Values:\n";
    echo "   Setting::freeShippingAmount(): " . Setting::freeShippingAmount() . "\n";
    echo "   Setting::defaultShippingCharge(): " . Setting::defaultShippingCharge() . "\n";
}

echo "\n=== DONE ===\n";
