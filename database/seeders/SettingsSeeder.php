<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'business_name', 'value' => 'Masala Store', 'type' => 'text', 'group' => 'general'],
            ['key' => 'business_email', 'value' => 'info@masalastore.com', 'type' => 'text', 'group' => 'general'],
            ['key' => 'business_phone', 'value' => '+91 98765 43210', 'type' => 'text', 'group' => 'general'],
            ['key' => 'business_address', 'value' => '123 Spice Market, Chennai, Tamil Nadu 600001', 'type' => 'textarea', 'group' => 'general'],
            ['key' => 'gst_number', 'value' => '33AABCT1234F1ZH', 'type' => 'text', 'group' => 'general'],
            
            // Order Settings
            ['key' => 'currency', 'value' => '₹', 'type' => 'text', 'group' => 'order'],
            ['key' => 'min_order_amount', 'value' => '100', 'type' => 'text', 'group' => 'order'],
            ['key' => 'free_shipping_amount', 'value' => '500', 'type' => 'text', 'group' => 'order'],
            ['key' => 'default_shipping_charge', 'value' => '50', 'type' => 'text', 'group' => 'order'],
            
            // Notification Settings
            ['key' => 'sms_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'whatsapp_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'email_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'notification'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
