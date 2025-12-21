<?php

namespace App\Mail;

use App\Models\Estimate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class EstimateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Estimate $estimate;
    public ?string $customMessage;
    public array $company;
    protected string $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Estimate $estimate, ?string $customMessage = null)
    {
        $this->estimate = $estimate;
        $this->customMessage = $customMessage;
        $this->company = $this->getCompanyData();
        
        // Generate PDF content
        $this->pdfContent = $this->generatePdfContent();
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
     * Generate PDF content
     */
    private function generatePdfContent(): string
    {
        $this->estimate->load('items.product');

        $pdf = PDF::loadView('pdf.estimate', [
            'estimate' => $this->estimate,
            'company' => $this->company,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->output();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Estimate #' . $this->estimate->estimate_number . ' from ' . $this->company['name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.estimate',
            with: [
                'estimate' => $this->estimate,
                'company' => $this->company,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Estimate-' . $this->estimate->estimate_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
