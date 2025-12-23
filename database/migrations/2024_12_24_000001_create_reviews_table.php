<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->json('images')->nullable(); // Store review images if any
            $table->boolean('is_verified_purchase')->default(true);
            $table->boolean('is_approved')->default(false); // Admin approval
            $table->boolean('is_featured')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Ensure one review per order item per user
            $table->unique(['order_id', 'order_item_id', 'user_id']);
        });

        // Add review_requested_at and review_token to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('review_requested_at')->nullable()->after('invoice_generated_at');
            $table->string('review_token', 64)->nullable()->unique()->after('review_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['review_requested_at', 'review_token']);
        });
        
        Schema::dropIfExists('reviews');
    }
};
