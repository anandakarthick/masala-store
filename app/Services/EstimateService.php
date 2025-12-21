<?php

namespace App\Services;

use App\Models\Estimate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EstimateService
{
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
     * Generate estimate PDF
     */
    public function generatePdf(Estimate $estimate): \Barryvdh\DomPDF\PDF
    {
        $estimate->load('items.product');

        $pdf = PDF::loadView('pdf.estimate', [
            'estimate' => $estimate,
            'company' => $this->getCompanyData(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Download estimate PDF
     */
    public function downloadPdf(Estimate $estimate)
    {
        $pdf = $this->generatePdf($estimate);
        return $pdf->download('Estimate-' . $estimate->estimate_number . '.pdf');
    }

    /**
     * Save estimate PDF to storage
     */
    public function savePdf(Estimate $estimate): string
    {
        $pdf = $this->generatePdf($estimate);

        $filename = 'estimate-' . $estimate->estimate_number . '.pdf';
        $path = 'estimates/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Get public URL for estimate PDF
     */
    public function getPdfUrl(Estimate $estimate): string
    {
        $path = $this->savePdf($estimate);
        return asset('storage/' . $path);
    }

    /**
     * Send estimate via email
     */
    public function sendEmail(Estimate $estimate, ?string $customMessage = null): bool
    {
        if (empty($estimate->customer_email)) {
            return false;
        }

        try {
            $estimate->load('items.product');
            $pdf = $this->generatePdf($estimate);
            $pdfContent = $pdf->output();

            $company = $this->getCompanyData();

            Mail::send('emails.estimate', [
                'estimate' => $estimate,
                'company' => $company,
                'customMessage' => $customMessage,
            ], function ($message) use ($estimate, $pdfContent, $company) {
                $message->to($estimate->customer_email, $estimate->customer_name)
                    ->subject('Estimate #' . $estimate->estimate_number . ' from ' . $company['name'])
                    ->attachData($pdfContent, 'Estimate-' . $estimate->estimate_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });

            // Update estimate status
            $estimate->update([
                'status' => $estimate->status === 'draft' ? 'sent' : $estimate->status,
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send estimate email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get WhatsApp share URL
     */
    public function getWhatsAppUrl(Estimate $estimate): string
    {
        $pdfUrl = $this->getPdfUrl($estimate);
        $company = $this->getCompanyData();

        $message = "Hello {$estimate->customer_name},\n\n";
        $message .= "Please find your estimate from {$company['name']}:\n\n";
        $message .= "Estimate No: {$estimate->estimate_number}\n";
        $message .= "Date: {$estimate->estimate_date->format('d/m/Y')}\n";
        if ($estimate->valid_until) {
            $message .= "Valid Until: {$estimate->valid_until->format('d/m/Y')}\n";
        }
        $message .= "Total Amount: ₹" . number_format($estimate->total_amount, 2) . "\n\n";
        $message .= "View/Download Estimate: {$pdfUrl}\n\n";
        $message .= "Thank you for your interest!\n";
        $message .= $company['name'];

        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $estimate->customer_phone);
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    /**
     * Get Web Share data
     */
    public function getWebShareData(Estimate $estimate): array
    {
        $pdfUrl = $this->getPdfUrl($estimate);
        $company = $this->getCompanyData();

        return [
            'title' => 'Estimate #' . $estimate->estimate_number,
            'text' => "Estimate from {$company['name']} - Total: ₹" . number_format($estimate->total_amount, 2),
            'url' => $pdfUrl,
        ];
    }
}
