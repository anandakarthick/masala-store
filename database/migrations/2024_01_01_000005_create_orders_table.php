<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Customer Details (for guest orders)
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            
            // Shipping Address
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_pincode');
            
            // Billing Address
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_pincode')->nullable();
            
            // Order Details
            $table->enum('order_type', ['retail', 'bulk', 'return_gift'])->default('retail');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Payment
            $table->enum('payment_method', ['cod', 'upi', 'razorpay', 'phonepe', 'bank_transfer'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_id')->nullable();
            
            // Order Status
            $table->enum('status', ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending');
            
            // Delivery
            $table->string('delivery_partner')->nullable();
            $table->string('tracking_number')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('delivered_at')->nullable();
            
            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Invoice
            $table->string('invoice_number')->nullable();
            $table->timestamp('invoice_generated_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
