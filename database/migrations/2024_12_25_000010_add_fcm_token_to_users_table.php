<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('fcm_token')->nullable()->after('provider');
            $table->string('device_type')->nullable()->after('fcm_token'); // android, ios, web
            $table->timestamp('fcm_token_updated_at')->nullable()->after('device_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fcm_token', 'device_type', 'fcm_token_updated_at']);
        });
    }
};
