<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create a separate table for FCM tokens to support multiple devices
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('fcm_token');
            $table->string('device_type')->default('android'); // android, ios, web
            $table->string('device_name')->nullable(); // e.g., "Samsung Galaxy S21"
            $table->string('device_id')->nullable(); // Unique device identifier
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
