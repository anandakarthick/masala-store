<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Get a setting value with caching
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Get a setting value without caching (fresh from database)
     */
    public static function getFresh(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'text', string $group = 'general'): self
    {
        Cache::forget("setting.{$key}");
        
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $keys = self::pluck('key')->toArray();
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    // Predefined settings helpers
    public static function businessName(): string
    {
        return self::get('business_name', 'Masala Store');
    }

    public static function businessEmail(): string
    {
        return self::get('business_email', 'info@masalastore.com');
    }

    public static function businessPhone(): string
    {
        return self::get('business_phone', '');
    }

    public static function businessAddress(): string
    {
        return self::get('business_address', '');
    }

    public static function gstNumber(): string
    {
        return self::get('gst_number', '');
    }

    public static function logo(): ?string
    {
        $logo = self::get('logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    public static function favicon(): ?string
    {
        $favicon = self::get('favicon');
        if ($favicon) {
            return asset('storage/' . $favicon);
        }
        $logo = self::get('logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    public static function currency(): string
    {
        return self::get('currency', '₹');
    }

    public static function minOrderAmount(): float
    {
        return (float) self::get('min_order_amount', 0);
    }

    public static function freeShippingAmount(): float
    {
        return (float) self::get('free_shipping_amount', 0);
    }

    public static function defaultShippingCharge(): float
    {
        return (float) self::get('default_shipping_charge', 0);
    }
}
