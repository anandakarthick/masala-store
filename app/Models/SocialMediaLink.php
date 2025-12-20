<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    protected $fillable = [
        'platform',
        'name',
        'url',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // Predefined platforms with their icons and colors
    public static function getPlatforms(): array
    {
        return [
            'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'color' => '#1877F2'],
            'instagram' => ['name' => 'Instagram', 'icon' => 'fab fa-instagram', 'color' => '#E4405F'],
            'twitter' => ['name' => 'Twitter/X', 'icon' => 'fab fa-x-twitter', 'color' => '#000000'],
            'youtube' => ['name' => 'YouTube', 'icon' => 'fab fa-youtube', 'color' => '#FF0000'],
            'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
            'telegram' => ['name' => 'Telegram', 'icon' => 'fab fa-telegram', 'color' => '#0088CC'],
            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'color' => '#0A66C2'],
            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest-p', 'color' => '#E60023'],
            'tiktok' => ['name' => 'TikTok', 'icon' => 'fab fa-tiktok', 'color' => '#000000'],
            'snapchat' => ['name' => 'Snapchat', 'icon' => 'fab fa-snapchat', 'color' => '#FFFC00'],
            'other' => ['name' => 'Other', 'icon' => 'fas fa-link', 'color' => '#6B7280'],
        ];
    }
}
