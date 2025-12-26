<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueStatus extends Command
{
    protected $signature = 'queue:status';
    protected $description = 'Quick check of queue status';

    public function handle()
    {
        $pending = DB::table('jobs')->count();
        $failed = DB::table('failed_jobs')->count();

        $this->info("Queue Status:");
        $this->line("  Pending Jobs: {$pending}");
        $this->line("  Failed Jobs: {$failed}");

        if ($pending > 0) {
            $this->newLine();
            $this->warn("⚠️  You have {$pending} pending jobs waiting to be processed!");
            $this->line("   Run: php artisan queue:work");
        }

        if ($failed > 0) {
            $this->newLine();
            $this->error("❌ You have {$failed} failed jobs!");
            $this->line("   Run: php artisan queue:failed      (to see failed jobs)");
            $this->line("   Run: php artisan queue:retry all   (to retry all failed jobs)");
            
            // Show latest failed job reason
            $latest = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->first();
            if ($latest) {
                $payload = json_decode($latest->payload, true);
                $displayName = $payload['displayName'] ?? 'Unknown';
                $this->newLine();
                $this->line("Latest failed job: {$displayName}");
                $this->line("Failed at: {$latest->failed_at}");
                
                // Extract error message
                preg_match('/^([^\n]+)/', $latest->exception, $matches);
                if (!empty($matches[1])) {
                    $this->line("Error: " . $matches[1]);
                }
            }
        }

        if ($pending == 0 && $failed == 0) {
            $this->info("✓ Queue is healthy - no pending or failed jobs");
        }

        return 0;
    }
}
