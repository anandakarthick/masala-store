<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // cod, razorpay, upi, bank_transfer
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->text('instructions')->nullable(); // Payment instructions
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false); // Online payment gateway
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('max_order_amount', 10, 2)->nullable();
            $table->decimal('extra_charge', 10, 2)->default(0); // Extra charge for this method
            $table->string('extra_charge_type')->default('fixed'); // fixed or percentage
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // Gateway specific settings
            $table->timestamps();
        });
        
        // Insert default payment methods
        DB::table('payment_methods')->insert([
            [
                'name' => 'Cash on Delivery',
                'code' => 'cod',
                'display_name' => 'Cash on Delivery (COD)',
                'description' => 'Pay when you receive your order',
                'icon' => 'fa-money-bill-wave',
                'instructions' => 'Pay cash to the delivery person when you receive your order.',
                'is_active' => true,
                'is_online' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Razorpay',
                'code' => 'razorpay',
                'display_name' => 'Pay Online (Card/UPI/NetBanking)',
                'description' => 'Secure online payment via Razorpay',
                'icon' => 'fa-credit-card',
                'instructions' => 'You will be redirected to Razorpay secure payment page.',
                'is_active' => false, // Will be enabled after configuration
                'is_online' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UPI',
                'code' => 'upi',
                'display_name' => 'UPI Payment',
                'description' => 'Pay using any UPI app',
                'icon' => 'fa-mobile-alt',
                'instructions' => 'Scan the QR code or use UPI ID to make payment.',
                'is_active' => false,
                'is_online' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'display_name' => 'Bank Transfer (NEFT/IMPS)',
                'description' => 'Direct bank transfer',
                'icon' => 'fa-university',
                'instructions' => 'Transfer the amount to our bank account and share the transaction details.',
                'is_active' => false,
                'is_online' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
