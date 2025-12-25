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
            $table->json('delivery_attachments')->nullable()->after('tracking_number');
            $table->text('delivery_notes')->nullable()->after('delivery_attachments');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            $table->string('cancelled_by')->nullable()->after('cancelled_at'); // 'customer' or 'admin'
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_attachments', 'delivery_notes', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });
    }
};
