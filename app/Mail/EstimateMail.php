<?php

namespace App\Mail;

use App\Models\Estimate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class EstimateMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $estimateId;
    public ?string $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(int $estimateId, ?string $customMessage = null)
    {
        $this->estimateId = $estimateId;
        $this->customMessage = $customMessage;
    }

    /**
     * Get company data with logo for PDF
     */
    private function getCompanyData(): array
    {
        $logoUrl = Setting::logo();
        $logoBase64 = null;

        if ($logoUrl) {
            try {
                $logoPath = Setting::get('business_logo');
                if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                    $logoContents = Storage::disk('public')->get($logoPath);
                    $mimeType = Storage::disk('public')->mimeType($logoPath);
                    $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoContents);
                } else {
                    $logoContents = @file_get_contents($logoUrl);
                    if ($logoContents) {
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer($logoContents);
                        $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoContents);
                    }
                }
            } catch (\Exception $e) {
                $logoBase64 = null;
            }
        }

        return [
            'name' => Setting::get('business_name', 'Masala Store'),
            'email' => Setting::get('business_email', ''),
            'phone' => Setting::get('business_phone', ''),
            'address' => Setting::get('business_address', ''),
            'gst' => Setting::get('gst_number', ''),
            'logo' => $logoBase64,
        ];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $estimate = Estimate::with('items.product')->findOrFail($this->estimateId);
        $company = $this->getCompanyData();

        // Generate PDF
        $pdf = PDF::loadView('pdf.estimate', [
            'estimate' => $estimate,
            'company' => $company,
        ]);
        $pdf->setPaper('A4', 'portrait');
        $pdfContent = $pdf->output();

        return $this->subject('Estimate #' . $estimate->estimate_number . ' from ' . $company['name'])
            ->view('emails.estimate', [
                'estimate' => $estimate,
                'company' => $company,
                'customMessage' => $this->customMessage,
            ])
            ->attachData($pdfContent, 'Estimate-' . $estimate->estimate_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
