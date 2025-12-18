<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_seen_by_admin')->default(false)->after('admin_notes');
            $table->timestamp('seen_at')->nullable()->after('is_seen_by_admin');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_seen_by_admin', 'seen_at']);
        });
    }
};
