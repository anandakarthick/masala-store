<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class ClearSettingsCache extends Command
{
    protected $signature = 'settings:clear-cache';
    protected $description = 'Clear all settings cache';

    public function handle(): int
    {
        Setting::clearCache();
        $this->info('Settings cache cleared successfully!');
        
        // Show current shipping settings
        $this->info('');
        $this->info('Current Shipping Settings (from database):');
        $this->table(
            ['Setting', 'Value'],
            [
                ['free_shipping_amount', Setting::getFresh('free_shipping_amount', 'NOT SET')],
                ['default_shipping_charge', Setting::getFresh('default_shipping_charge', 'NOT SET')],
            ]
        );
        
        return 0;
    }
}
