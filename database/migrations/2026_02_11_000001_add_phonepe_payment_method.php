<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if PhonePe payment method already exists
        $exists = DB::table('payment_methods')->where('code', 'phonepe')->exists();

        if (!$exists) {
            DB::table('payment_methods')->insert([
                'name' => 'PhonePe',
                'code' => 'phonepe',
                'display_name' => 'Pay via PhonePe',
                'description' => 'Secure online payment via PhonePe',
                'icon' => 'fa-mobile-alt',
                'instructions' => 'You will be redirected to PhonePe secure payment page.',
                'is_active' => true,
                'is_online' => true,
                'min_order_amount' => 1.00,
                'max_order_amount' => null,
                'extra_charge' => 0,
                'extra_charge_type' => 'fixed',
                'sort_order' => 2,
                'settings' => json_encode([
                    'client_id' => 'SU2602101440438258181049',
                    'client_secret' => 'ac7823c7-ad1c-4fef-bab3-04b2b8de861e',
                    'merchant_id' => 'M23FCVNPSAT58',
                    'environment' => 'production',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Update existing PhonePe payment method with new credentials
            DB::table('payment_methods')->where('code', 'phonepe')->update([
                'settings' => json_encode([
                    'client_id' => 'SU2602101440438258181049',
                    'client_secret' => 'ac7823c7-ad1c-4fef-bab3-04b2b8de861e',
                    'merchant_id' => 'M23FCVNPSAT58',
                    'environment' => 'production',
                ]),
                'is_active' => true,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('payment_methods')->where('code', 'phonepe')->delete();
    }
};
