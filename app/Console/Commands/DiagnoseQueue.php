<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\FCMService;
use App\Models\User;

class DiagnoseQueue extends Command
{
    protected $signature = 'queue:diagnose {--test-email : Send a test email} {--test-push : Send a test push notification}';
    protected $description = 'Diagnose queue issues and test email/push notifications';

    public function handle()
    {
        $this->info('=== Queue Diagnostics ===');
        $this->newLine();

        // 1. Check queue connection
        $queueConnection = config('queue.default');
        $this->info("Queue Connection: {$queueConnection}");

        // 2. Check jobs table
        $this->checkJobsTable();

        // 3. Check failed jobs
        $this->checkFailedJobs();

        // 4. Check mail configuration
        $this->checkMailConfig();

        // 5. Check FCM configuration
        $this->checkFCMConfig();

        // 6. Test email if requested
        if ($this->option('test-email')) {
            $this->testEmail();
        }

        // 7. Test push notification if requested
        if ($this->option('test-push')) {
            $this->testPush();
        }

        $this->newLine();
        $this->info('=== Diagnostics Complete ===');
    }

    private function checkJobsTable()
    {
        $this->newLine();
        $this->info('--- Jobs Table ---');

        try {
            $pendingJobs = DB::table('jobs')->count();
            $this->line("Pending Jobs: {$pendingJobs}");

            if ($pendingJobs > 0) {
                $jobs = DB::table('jobs')->take(5)->get();
                $this->table(
                    ['ID', 'Queue', 'Attempts', 'Created At'],
                    $jobs->map(function ($job) {
                        $payload = json_decode($job->payload, true);
                        $displayName = $payload['displayName'] ?? 'Unknown';
                        return [
                            $job->id,
                            $job->queue . ' (' . $displayName . ')',
                            $job->attempts,
                            date('Y-m-d H:i:s', $job->created_at)
                        ];
                    })->toArray()
                );

                $this->warn('⚠️  There are pending jobs! Make sure queue:work is running.');
            } else {
                $this->info('✓ No pending jobs in queue');
            }
        } catch (\Exception $e) {
            $this->error('Error checking jobs table: ' . $e->getMessage());
        }
    }

    private function checkFailedJobs()
    {
        $this->newLine();
        $this->info('--- Failed Jobs ---');

        try {
            $failedJobs = DB::table('failed_jobs')->count();
            $this->line("Failed Jobs: {$failedJobs}");

            if ($failedJobs > 0) {
                $recent = DB::table('failed_jobs')
                    ->orderBy('failed_at', 'desc')
                    ->take(5)
                    ->get();

                foreach ($recent as $job) {
                    $payload = json_decode($job->payload, true);
                    $displayName = $payload['displayName'] ?? 'Unknown';
                    $this->newLine();
                    $this->error("Job: {$displayName}");
                    $this->line("  Queue: {$job->queue}");
                    $this->line("  Failed At: {$job->failed_at}");
                    
                    // Show first 500 chars of exception
                    $exception = substr($job->exception, 0, 500);
                    $this->line("  Exception: {$exception}...");
                }

                $this->newLine();
                $this->warn('⚠️  There are failed jobs! Run: php artisan queue:failed to see all');
                $this->warn('    To retry all: php artisan queue:retry all');
                $this->warn('    To flush all: php artisan queue:flush');
            } else {
                $this->info('✓ No failed jobs');
            }
        } catch (\Exception $e) {
            $this->error('Error checking failed jobs: ' . $e->getMessage());
        }
    }

    private function checkMailConfig()
    {
        $this->newLine();
        $this->info('--- Mail Configuration ---');

        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $from = config('mail.from.address');

        $this->line("Mailer: {$mailer}");
        $this->line("Host: {$host}");
        $this->line("Port: {$port}");
        $this->line("Username: {$username}");
        $this->line("From: {$from}");

        if (empty($username) || empty($host)) {
            $this->error('✗ Mail configuration is incomplete!');
        } else {
            $this->info('✓ Mail configuration looks OK');
        }
    }

    private function checkFCMConfig()
    {
        $this->newLine();
        $this->info('--- FCM Configuration ---');

        $serviceAccountPath = storage_path('app/firebase-service-account.json');

        if (file_exists($serviceAccountPath)) {
            $this->info('✓ Service account file exists');
            
            $content = file_get_contents($serviceAccountPath);
            $json = json_decode($content, true);
            
            if (isset($json['project_id'])) {
                $this->line("Project ID: {$json['project_id']}");
            }
            if (isset($json['client_email'])) {
                $this->line("Client Email: {$json['client_email']}");
            }

            // Test OAuth token
            try {
                $fcmService = app(FCMService::class);
                $this->info('✓ FCM Service initialized successfully');
            } catch (\Exception $e) {
                $this->error('✗ FCM Service error: ' . $e->getMessage());
            }
        } else {
            $this->error("✗ Service account file not found at: {$serviceAccountPath}");
        }
    }

    private function testEmail()
    {
        $this->newLine();
        $this->info('--- Testing Email ---');

        $testEmail = $this->ask('Enter email address to send test email to');

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address');
            return;
        }

        try {
            Mail::raw('This is a test email from SV Products queue diagnostic tool. If you receive this, email is working!', function ($message) use ($testEmail) {
                $message->to($testEmail)
                    ->subject('SV Products - Test Email');
            });

            $this->info('✓ Test email sent to ' . $testEmail);
            $this->line('  Check your inbox (and spam folder)');
        } catch (\Exception $e) {
            $this->error('✗ Failed to send email: ' . $e->getMessage());
            Log::error('Test email failed', ['error' => $e->getMessage()]);
        }
    }

    private function testPush()
    {
        $this->newLine();
        $this->info('--- Testing Push Notification ---');

        $userId = $this->ask('Enter user ID to send test push to');
        $user = User::find($userId);

        if (!$user) {
            $this->error('User not found');
            return;
        }

        $this->line("User: {$user->name} ({$user->email})");
        $this->line("FCM Token: " . ($user->fcm_token ? substr($user->fcm_token, 0, 30) . '...' : 'NOT SET'));

        if (!$user->fcm_token) {
            $this->error('✗ User does not have FCM token');
            return;
        }

        try {
            $fcmService = app(FCMService::class);
            $result = $fcmService->sendCustomNotification(
                $user->id,
                'Test Notification 🔔',
                'This is a test notification from SV Products',
                ['type' => 'test']
            );

            if ($result) {
                $this->info('✓ Push notification sent successfully');
            } else {
                $this->error('✗ Push notification failed (check logs)');
            }
        } catch (\Exception $e) {
            $this->error('✗ Push notification error: ' . $e->getMessage());
            Log::error('Test push failed', ['error' => $e->getMessage()]);
        }
    }
}
