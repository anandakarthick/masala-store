<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add referral fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->unique()->nullable()->after('provider');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            $table->decimal('wallet_balance', 10, 2)->default(0)->after('referred_by');
            $table->timestamp('referred_at')->nullable()->after('wallet_balance');
        });

        // Create wallet transactions table
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('source')->default('referral'); // referral, order, admin, refund
            $table->string('description');
            $table->foreignId('reference_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('reference_user_id')->nullable()->constrained('users')->nullOnDelete(); // referred user
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['source', 'created_at']);
        });

        // Create referrals tracking table
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade'); // Customer A
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade'); // Customer B
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');
            $table->decimal('reward_amount', 10, 2)->default(0);
            $table->integer('orders_rewarded')->default(0); // Track how many orders have been rewarded
            $table->foreignId('first_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['referrer_id', 'referred_id']);
            $table->index(['referrer_id', 'status']);
            $table->index(['referred_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('wallet_transactions');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by', 'wallet_balance', 'referred_at']);
        });
    }
};
