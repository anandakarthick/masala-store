<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Jobs\SendOrderEmails;

class DebugQueue extends Command
{
    protected $signature = 'queue:debug {--dispatch : Dispatch a test job}';
    protected $description = 'Debug queue configuration and status';

    public function handle()
    {
        $this->info('=== Queue Debug Info ===');
        $this->newLine();

        // 1. Queue connection
        $queueConnection = config('queue.default');
        $this->line("QUEUE_CONNECTION: <info>{$queueConnection}</info>");

        if ($queueConnection === 'sync') {
            $this->error('⚠️  Queue is set to SYNC mode - jobs run immediately, not queued!');
            $this->line('   Set QUEUE_CONNECTION=database in .env file');
            return;
        }

        // 2. Check jobs table exists
        $this->newLine();
        if (Schema::hasTable('jobs')) {
            $this->info('✓ Jobs table exists');
            
            $pendingJobs = DB::table('jobs')->count();
            $this->line("  Pending jobs: {$pendingJobs}");
            
            if ($pendingJobs > 0) {
                $this->warn("  ⚠️  There are {$pendingJobs} jobs waiting to be processed!");
                
                // Show job details
                $jobs = DB::table('jobs')->limit(5)->get();
                foreach ($jobs as $job) {
                    $payload = json_decode($job->payload, true);
                    $displayName = $payload['displayName'] ?? 'Unknown';
                    $this->line("    - {$displayName} (attempts: {$job->attempts})");
                }
            }
        } else {
            $this->error('✗ Jobs table does NOT exist!');
            $this->line('   Run: php artisan queue:table && php artisan migrate');
            return;
        }

        // 3. Check failed_jobs table
        $this->newLine();
        if (Schema::hasTable('failed_jobs')) {
            $this->info('✓ Failed jobs table exists');
            
            $failedJobs = DB::table('failed_jobs')->count();
            $this->line("  Failed jobs: {$failedJobs}");
            
            if ($failedJobs > 0) {
                $this->error("  ⚠️  There are {$failedJobs} failed jobs!");
                
                // Show latest failure
                $latest = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->first();
                if ($latest) {
                    $payload = json_decode($latest->payload, true);
                    $displayName = $payload['displayName'] ?? 'Unknown';
                    $this->line("  Latest failure: {$displayName}");
                    $this->line("  Failed at: {$latest->failed_at}");
                    
                    // Extract first line of exception
                    preg_match('/^([^\n]+)/', $latest->exception, $matches);
                    if (!empty($matches[1])) {
                        $this->line("  Error: " . substr($matches[1], 0, 100));
                    }
                }
            }
        } else {
            $this->warn('✗ Failed jobs table does not exist');
        }

        // 4. Check database connection for queue
        $this->newLine();
        $queueConfig = config('queue.connections.database');
        $this->line("Queue database connection: " . ($queueConfig['connection'] ?? 'default'));
        $this->line("Queue table: " . ($queueConfig['table'] ?? 'jobs'));

        // 5. Test job dispatch if requested
        if ($this->option('dispatch')) {
            $this->newLine();
            $this->info('--- Testing Job Dispatch ---');
            
            $order = Order::latest()->first();
            
            if (!$order) {
                $this->error('No orders found to test with');
                return;
            }

            $this->line("Dispatching SendOrderEmails job for order: {$order->order_number}");
            
            $beforeCount = DB::table('jobs')->count();
            
            SendOrderEmails::dispatch($order);
            
            $afterCount = DB::table('jobs')->count();
            
            if ($afterCount > $beforeCount) {
                $this->info("✓ Job dispatched successfully! Jobs in queue: {$afterCount}");
            } else {
                $this->error("✗ Job was NOT added to queue!");
                $this->line("  This means jobs might be running synchronously");
                $this->line("  Check QUEUE_CONNECTION in .env");
            }
        }

        $this->newLine();
        $this->info('=== Debug Complete ===');
        $this->newLine();
        $this->line('To process jobs, run: php artisan queue:work --verbose');
        
        return 0;
    }
}
