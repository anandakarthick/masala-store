<?php

namespace App\Jobs;

use App\Mail\EstimateMail;
use App\Models\Estimate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEstimateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    protected int $estimateId;
    protected ?string $customMessage;

    /**
     * Create a new job instance.
     */
    public function __construct(int $estimateId, ?string $customMessage = null)
    {
        $this->estimateId = $estimateId;
        $this->customMessage = $customMessage;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Load estimate fresh from database
            $estimate = Estimate::with('items.product')->findOrFail($this->estimateId);

            if (empty($estimate->customer_email)) {
                Log::warning('Estimate email job failed: No customer email', [
                    'estimate_id' => $estimate->id,
                    'estimate_number' => $estimate->estimate_number,
                ]);
                return;
            }

            // Send the email
            Mail::to($estimate->customer_email, $estimate->customer_name)
                ->send(new EstimateMail($this->estimateId, $this->customMessage));

            // Update estimate status
            $estimate->update([
                'status' => $estimate->status === 'draft' ? 'sent' : $estimate->status,
                'sent_at' => now(),
            ]);

            Log::info('Estimate email sent successfully', [
                'estimate_id' => $estimate->id,
                'estimate_number' => $estimate->estimate_number,
                'customer_email' => $estimate->customer_email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send estimate email', [
                'estimate_id' => $this->estimateId,
                'error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Estimate email job failed permanently', [
            'estimate_id' => $this->estimateId,
            'error' => $exception->getMessage(),
        ]);
    }
}
