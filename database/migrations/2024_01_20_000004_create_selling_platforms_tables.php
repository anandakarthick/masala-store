<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Selling Platforms table
        Schema::create('selling_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // amazon, flipkart, myntra, etc.
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->string('seller_portal_url')->nullable();
            $table->text('description')->nullable();
            $table->string('platform_type')->default('marketplace'); // marketplace, b2b, social_commerce, own_store
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable(); // API keys, seller IDs, etc.
            $table->timestamps();
        });

        // Product Platform Listings - which products are listed on which platforms
        Schema::create('product_platform_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('selling_platform_id')->constrained()->onDelete('cascade');
            $table->string('platform_product_id')->nullable(); // Product ID on that platform
            $table->string('platform_sku')->nullable();
            $table->string('listing_url')->nullable();
            $table->decimal('platform_price', 10, 2)->nullable(); // Price on that platform
            $table->decimal('platform_mrp', 10, 2)->nullable();
            $table->string('status')->default('draft'); // draft, pending, active, inactive, rejected
            $table->integer('platform_stock')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('listed_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('platform_data')->nullable(); // Platform-specific data
            $table->timestamps();
            
            $table->unique(['product_id', 'selling_platform_id']);
        });

        // Platform Orders - orders from different platforms
        Schema::create('platform_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selling_platform_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('platform_order_id');
            $table->string('platform_order_status')->nullable();
            $table->decimal('platform_order_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('settlement_amount', 10, 2)->default(0);
            $table->string('customer_name')->nullable();
            $table->text('shipping_address')->nullable();
            $table->json('order_data')->nullable();
            $table->timestamp('platform_order_date')->nullable();
            $table->timestamps();
            
            $table->unique(['selling_platform_id', 'platform_order_id']);
        });

        // Insert default platforms
        DB::table('selling_platforms')->insert([
            [
                'name' => 'Amazon India',
                'code' => 'amazon',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg',
                'website_url' => 'https://www.amazon.in',
                'seller_portal_url' => 'https://sellercentral.amazon.in',
                'description' => 'Largest e-commerce marketplace in India',
                'platform_type' => 'marketplace',
                'commission_percentage' => 15.00,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flipkart',
                'code' => 'flipkart',
                'logo' => 'https://static-assets-web.flixcart.com/fk-p-linchpin-web/fk-cp-zion/img/flipkart-plus_8d85f4.png',
                'website_url' => 'https://www.flipkart.com',
                'seller_portal_url' => 'https://seller.flipkart.com',
                'description' => 'Leading Indian e-commerce marketplace',
                'platform_type' => 'marketplace',
                'commission_percentage' => 12.00,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Myntra',
                'code' => 'myntra',
                'logo' => 'https://constant.myntassets.com/web/assets/img/logo_2021.png',
                'website_url' => 'https://www.myntra.com',
                'seller_portal_url' => 'https://partners.myntra.com',
                'description' => 'Fashion and lifestyle e-commerce platform',
                'platform_type' => 'marketplace',
                'commission_percentage' => 20.00,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meesho',
                'code' => 'meesho',
                'logo' => 'https://images.meesho.com/images/marketing/1678691430498_512.webp',
                'website_url' => 'https://www.meesho.com',
                'seller_portal_url' => 'https://supplier.meesho.com',
                'description' => 'Social commerce platform for resellers',
                'platform_type' => 'social_commerce',
                'commission_percentage' => 0.00,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IndiaMART',
                'code' => 'indiamart',
                'logo' => 'https://www.indiamart.com/images/logo.png',
                'website_url' => 'https://www.indiamart.com',
                'seller_portal_url' => 'https://seller.indiamart.com',
                'description' => 'B2B marketplace for wholesale and bulk orders',
                'platform_type' => 'b2b',
                'commission_percentage' => 0.00,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Etsy',
                'code' => 'etsy',
                'logo' => 'https://www.etsy.com/images/logo.svg',
                'website_url' => 'https://www.etsy.com',
                'seller_portal_url' => 'https://www.etsy.com/your/shops/me/dashboard',
                'description' => 'Global marketplace for handcrafted and unique items',
                'platform_type' => 'marketplace',
                'commission_percentage' => 6.50,
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shopify Store',
                'code' => 'shopify',
                'logo' => 'https://cdn.shopify.com/s/files/1/0070/7032/files/shopify_logo.png',
                'website_url' => null,
                'seller_portal_url' => 'https://admin.shopify.com',
                'description' => 'Your own branded online store',
                'platform_type' => 'own_store',
                'commission_percentage' => 2.00,
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'JioMart',
                'code' => 'jiomart',
                'logo' => 'https://www.jiomart.com/images/cms/aw_rbslider/slides/1590702100_jiomart-logo.png',
                'website_url' => 'https://www.jiomart.com',
                'seller_portal_url' => 'https://seller.jiomart.com',
                'description' => 'Reliance retail marketplace',
                'platform_type' => 'marketplace',
                'commission_percentage' => 10.00,
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_orders');
        Schema::dropIfExists('product_platform_listings');
        Schema::dropIfExists('selling_platforms');
    }
};
