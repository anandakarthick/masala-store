<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // $table->string('transaction_id', 100)->nullable()->after('payment_status');
            $table->timestamp('payment_confirmed_at')->nullable()->after('transaction_id');
            $table->string('payment_confirmed_via', 50)->nullable()->after('payment_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'payment_confirmed_at', 'payment_confirmed_via']);
        });
    }
};
