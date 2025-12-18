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

        // Get company details
        $companyData = [
            'name' => Setting::get('business_name', 'Masala Store'),
            'email' => Setting::get('business_email', ''),
            'phone' => Setting::get('business_phone', ''),
            'address' => Setting::get('business_address', ''),
            'gst' => Setting::get('gst_number', ''),
            'logo' => Setting::logo(),
        ];

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

        $companyData = [
            'name' => Setting::get('business_name', 'Masala Store'),
            'email' => Setting::get('business_email', ''),
            'phone' => Setting::get('business_phone', ''),
            'address' => Setting::get('business_address', ''),
            'gst' => Setting::get('gst_number', ''),
            'logo' => Setting::logo(),
        ];

        $pdf = PDF::loadView('pdf.invoice', [
            'order' => $order,
            'company' => $companyData,
        ]);

        return $pdf->download('Invoice-' . $order->invoice_number . '.pdf');
    }
}
