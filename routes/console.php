<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Here you can define your scheduled commands. These will be run
| automatically by Laravel's scheduler when you set up a cron job.
|
| Add this to your server's crontab:
| * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Generate sitemap daily at 2 AM
Schedule::command('sitemap:generate')->dailyAt('02:00')->withoutOverlapping();
