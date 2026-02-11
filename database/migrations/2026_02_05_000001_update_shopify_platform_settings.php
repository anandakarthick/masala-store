<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('selling_platforms')
            ->where('code', 'shopify')
            ->update([
                'website_url' => 'https://smartnestt.com',
                'seller_portal_url' => 'https://uu24yv-00.myshopify.com/admin',
                'settings' => json_encode([
                    'store_url' => 'uu24yv-00.myshopify.com',
                    'access_token' => 'c944329ea235f9d21b2071e8ce4394d5',
                    'api_key' => null,
                    'api_secret' => null,
                ]),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('selling_platforms')
            ->where('code', 'shopify')
            ->update([
                'website_url' => null,
                'seller_portal_url' => 'https://admin.shopify.com',
                'settings' => null,
                'updated_at' => now(),
            ]);
    }
};
