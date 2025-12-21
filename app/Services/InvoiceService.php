<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function generateInvoice(Order $order): string
    {
        // Generate invoice number if not exists
        if (!$order->invoice_number) {
            $order->update([
                'invoice_number' => $order->generateInvoiceNumber(),
                'invoice_generated_at' => now(),
            ]);
        }

        $order->load('items.product');

        // Get company details with logo as base64 for PDF
        $companyData = $this->getCompanyData();

        $pdf = PDF::loadView('pdf.invoice', [
            'order' => $order,
            'company' => $companyData,
        ]);

        $pdf->setPaper('A4', 'portrait');

        // Save to temp file
        $filename = 'invoice-' . $order->order_number . '-' . time() . '.pdf';
        $path = storage_path('app/temp/' . $filename);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }

    public function downloadInvoice(Order $order)
    {
        $order->load('items.product');

        // Get company details with logo as base64 for PDF
        $companyData = $this->getCompanyData();

        $pdf = PDF::loadView('pdf.invoice', [
            'order' => $order,
            'company' => $companyData,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Invoice-' . ($order->invoice_number ?? $order->order_number) . '.pdf');
    }

    /**
     * Get company data with logo converted to base64 for PDF
     */
    private function getCompanyData(): array
    {
        $logoUrl = Setting::logo();
        $logoBase64 = null;

        // Convert logo to base64 for PDF rendering
        if ($logoUrl) {
            try {
                // Check if it's a storage path
                $logoPath = Setting::get('business_logo');
                if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                    $logoContents = Storage::disk('public')->get($logoPath);
                    $mimeType = Storage::disk('public')->mimeType($logoPath);
                    $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoContents);
                } else {
                    // Try to fetch from URL
                    $logoContents = @file_get_contents($logoUrl);
                    if ($logoContents) {
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $finfo->buffer($logoContents);
                        $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoContents);
                    }
                }
            } catch (\Exception $e) {
                // Logo conversion failed, continue without logo
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
}
