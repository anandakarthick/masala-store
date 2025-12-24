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
        // Add device_id and is_guest columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('device_id')->nullable()->unique()->after('id');
            $table->boolean('is_guest')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['device_id', 'is_guest']);
        });
    }
};
