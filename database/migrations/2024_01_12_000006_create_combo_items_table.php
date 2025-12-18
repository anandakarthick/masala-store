<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table to store combo/pack items (what products are included in a combo)
        Schema::create('combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_product_id')->constrained('products')->onDelete('cascade'); // The combo product
            $table->foreignId('included_product_id')->nullable()->constrained('products')->onDelete('set null'); // Linked product (optional)
            $table->string('item_name'); // Name of included item (e.g., "Turmeric Powder")
            $table->string('item_quantity')->nullable(); // e.g., "100g", "50ml"
            $table->text('item_description')->nullable(); // Brief description
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('combo_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_items');
    }
};
