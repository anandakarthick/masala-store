<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'instructions' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_order_amount' => 'nullable|numeric|min:0',
            'extra_charge' => 'nullable|numeric|min:0',
            'extra_charge_type' => 'in:fixed,percentage',
            'sort_order' => 'integer|min:0',
            'qr_code_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle Razorpay specific settings
        if ($paymentMethod->code === 'razorpay') {
            $settings = [
                'key_id' => $request->input('razorpay_key_id'),
                'key_secret' => $request->input('razorpay_key_secret'),
                'webhook_secret' => $request->input('razorpay_webhook_secret'),
            ];
            $validated['settings'] = $settings;
        }

        // Handle UPI settings
        if ($paymentMethod->code === 'upi') {
            $settings = $paymentMethod->settings ?? [];
            $settings['upi_id'] = $request->input('upi_id');
            $settings['upi_name'] = $request->input('upi_name');
            
            // Handle QR code image upload
            if ($request->hasFile('qr_code_image')) {
                // Delete old QR code if exists
                if (!empty($settings['qr_code']) && Storage::disk('public')->exists($settings['qr_code'])) {
                    Storage::disk('public')->delete($settings['qr_code']);
                }
                
                // Store new QR code
                $path = $request->file('qr_code_image')->store('payment/qr-codes', 'public');
                $settings['qr_code'] = $path;
            }
            
            // Handle QR code removal
            if ($request->boolean('remove_qr_code') && !empty($settings['qr_code'])) {
                if (Storage::disk('public')->exists($settings['qr_code'])) {
                    Storage::disk('public')->delete($settings['qr_code']);
                }
                $settings['qr_code'] = null;
            }
            
            $validated['settings'] = $settings;
        }

        // Handle Bank Transfer settings
        if ($paymentMethod->code === 'bank_transfer') {
            $settings = [
                'account_name' => $request->input('account_name'),
                'account_number' => $request->input('account_number'),
                'bank_name' => $request->input('bank_name'),
                'ifsc_code' => $request->input('ifsc_code'),
                'branch' => $request->input('branch'),
            ];
            $validated['settings'] = $settings;
        }

        $validated['is_active'] = $request->boolean('is_active');

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $paymentMethod->is_active,
            'message' => $paymentMethod->is_active ? 'Payment method enabled' : 'Payment method disabled',
        ]);
    }
}
