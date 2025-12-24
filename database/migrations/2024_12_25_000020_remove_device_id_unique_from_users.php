<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove unique constraint from device_id since multiple users might use the same device
     * (when user logs out and another user logs in on the same device)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique index on device_id if it exists
            // The device_id should not be unique because:
            // 1. When user A logs out and user B logs in on the same device, both might have same device_id
            // 2. The device_id is used to identify the device, not the user-device relationship
            $table->dropUnique(['device_id']);
        });
        
        // Add an index for better query performance (but not unique)
        Schema::table('users', function (Blueprint $table) {
            $table->index('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['device_id']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->unique('device_id');
        });
    }
};
