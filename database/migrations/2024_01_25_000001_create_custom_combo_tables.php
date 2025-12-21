<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop tables if they exist (both singular and plural versions)
        Schema::dropIfExists('order_custom_combos');
        Schema::dropIfExists('custom_combo_cart_items');
        Schema::dropIfExists('custom_combo_carts');
        Schema::dropIfExists('custom_combo_cart');
        Schema::dropIfExists('custom_combo_settings');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Custom Combo Settings - Admin configures combo rules
        Schema::create('custom_combo_settings', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('min_products')->default(2);
            $table->integer('max_products')->default(10);
            $table->enum('discount_type', ['percentage', 'fixed', 'per_item'])->default('percentage');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('combo_price', 10, 2)->nullable();
            $table->json('allowed_categories')->nullable();
            $table->json('allowed_products')->nullable();
            $table->json('excluded_products')->nullable();
            $table->boolean('allow_same_product')->default(false);
            $table->boolean('allow_variants')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Custom Combo Carts (plural to match Laravel convention)
        Schema::create('custom_combo_carts', function ($table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('combo_setting_id')->constrained('custom_combo_settings')->onDelete('cascade');
            $table->string('combo_name')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('calculated_price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
            $table->timestamps();
        });

        // Custom Combo Cart Items
        Schema::create('custom_combo_cart_items', function ($table) {
            $table->id();
            $table->foreignId('custom_combo_cart_id')->constrained('custom_combo_carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });

        // Order Custom Combos
        Schema::create('order_custom_combos', function ($table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('combo_setting_id')->nullable()->constrained('custom_combo_settings')->onDelete('set null');
            $table->string('combo_name');
            $table->integer('quantity')->default(1);
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->json('items_snapshot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('order_custom_combos');
        Schema::dropIfExists('custom_combo_cart_items');
        Schema::dropIfExists('custom_combo_carts');
        Schema::dropIfExists('custom_combo_settings');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
