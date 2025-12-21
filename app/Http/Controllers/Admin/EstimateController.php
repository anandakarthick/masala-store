<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\EstimateService;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    protected EstimateService $estimateService;

    public function __construct(EstimateService $estimateService)
    {
        $this->estimateService = $estimateService;
    }

    public function index(Request $request)
    {
        $query = Estimate::with('items', 'createdBy');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('estimate_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('estimate_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('estimate_date', '<=', $request->date_to);
        }

        $estimates = $query->latest()->paginate(15);

        return view('admin.estimates.index', compact('estimates'));
    }

    public function create()
    {
        $products = Product::active()->with('activeVariants')->get();
        $customers = User::whereHas('role', function($q) {
            $q->where('slug', 'customer');
        })->get();

        $productsJson = [];
        foreach ($products as $p) {
            if ($p->has_variants && $p->activeVariants->count() > 0) {
                foreach ($p->activeVariants as $v) {
                    $productsJson[] = [
                        'id' => (int) $p->id,
                        'variant_id' => (int) $v->id,
                        'name' => $p->name . ' - ' . $v->name,
                        'sku' => $v->sku ?? '',
                        'price' => (float) ($v->discount_price ?? $v->price ?? $p->price),
                        'gst_percent' => (float) ($p->gst_percentage ?? 0),
                        'stock' => (int) ($v->stock_quantity ?? 0),
                    ];
                }
            } else {
                $productsJson[] = [
                    'id' => (int) $p->id,
                    'variant_id' => 0,
                    'name' => $p->name,
                    'sku' => $p->sku ?? '',
                    'price' => (float) ($p->discount_price ?? $p->price),
                    'gst_percent' => (float) ($p->gst_percentage ?? 0),
                    'stock' => (int) ($p->stock_quantity ?? 0),
                ];
            }
        }

        return view('admin.estimates.create', compact('products', 'productsJson', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string|max:15',
            'customer_address' => 'nullable|string',
            'customer_city' => 'nullable|string',
            'customer_state' => 'nullable|string',
            'customer_pincode' => 'nullable|string|max:10',
            'estimate_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:estimate_date',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'shipping_charge' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);

        $estimate = Estimate::create([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_city' => $validated['customer_city'] ?? null,
            'customer_state' => $validated['customer_state'] ?? null,
            'customer_pincode' => $validated['customer_pincode'] ?? null,
            'estimate_date' => $validated['estimate_date'],
            'valid_until' => $validated['valid_until'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'] ?? 0,
            'shipping_charge' => $validated['shipping_charge'] ?? 0,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $variantId = !empty($item['variant_id']) && $item['variant_id'] != '0' ? $item['variant_id'] : null;
            $variant = $variantId ? ProductVariant::find($variantId) : null;

            $estimate->items()->create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'product_name' => $product->name,
                'product_sku' => $variant ? $variant->sku : $product->sku,
                'variant_name' => $variant?->name,
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'gst_percent' => $item['gst_percent'] ?? 0,
            ]);
        }

        $estimate->calculateTotals();

        return redirect()->route('admin.estimates.show', $estimate)
            ->with('success', 'Estimate created successfully.');
    }

    public function show(Estimate $estimate)
    {
        $estimate->load('items.product', 'createdBy', 'convertedOrder');

        return view('admin.estimates.show', compact('estimate'));
    }

    public function edit(Estimate $estimate)
    {
        if (!$estimate->canBeEdited()) {
            return back()->with('error', 'This estimate cannot be edited.');
        }

        $estimate->load('items');
        $products = Product::active()->with('activeVariants')->get();
        $customers = User::whereHas('role', function($q) {
            $q->where('slug', 'customer');
        })->get();

        $productsJson = [];
        foreach ($products as $p) {
            if ($p->has_variants && $p->activeVariants->count() > 0) {
                foreach ($p->activeVariants as $v) {
                    $productsJson[] = [
                        'id' => (int) $p->id,
                        'variant_id' => (int) $v->id,
                        'name' => $p->name . ' - ' . $v->name,
                        'sku' => $v->sku ?? '',
                        'price' => (float) ($v->discount_price ?? $v->price ?? $p->price),
                        'gst_percent' => (float) ($p->gst_percentage ?? 0),
                        'stock' => (int) ($v->stock_quantity ?? 0),
                    ];
                }
            } else {
                $productsJson[] = [
                    'id' => (int) $p->id,
                    'variant_id' => 0,
                    'name' => $p->name,
                    'sku' => $p->sku ?? '',
                    'price' => (float) ($p->discount_price ?? $p->price),
                    'gst_percent' => (float) ($p->gst_percentage ?? 0),
                    'stock' => (int) ($p->stock_quantity ?? 0),
                ];
            }
        }

        return view('admin.estimates.edit', compact('estimate', 'products', 'productsJson', 'customers'));
    }

    public function update(Request $request, Estimate $estimate)
    {
        if (!$estimate->canBeEdited()) {
            return back()->with('error', 'This estimate cannot be edited.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string|max:15',
            'customer_address' => 'nullable|string',
            'customer_city' => 'nullable|string',
            'customer_state' => 'nullable|string',
            'customer_pincode' => 'nullable|string|max:10',
            'estimate_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:estimate_date',
            'subject' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'shipping_charge' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);

        $estimate->update([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_city' => $validated['customer_city'] ?? null,
            'customer_state' => $validated['customer_state'] ?? null,
            'customer_pincode' => $validated['customer_pincode'] ?? null,
            'estimate_date' => $validated['estimate_date'],
            'valid_until' => $validated['valid_until'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'] ?? 0,
            'shipping_charge' => $validated['shipping_charge'] ?? 0,
        ]);

        // Remove old items
        $estimate->items()->delete();

        // Add new items
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $variantId = !empty($item['variant_id']) && $item['variant_id'] != '0' ? $item['variant_id'] : null;
            $variant = $variantId ? ProductVariant::find($variantId) : null;

            $estimate->items()->create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'product_name' => $product->name,
                'product_sku' => $variant ? $variant->sku : $product->sku,
                'variant_name' => $variant?->name,
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'gst_percent' => $item['gst_percent'] ?? 0,
            ]);
        }

        $estimate->calculateTotals();

        return redirect()->route('admin.estimates.show', $estimate)
            ->with('success', 'Estimate updated successfully.');
    }

    public function destroy(Estimate $estimate)
    {
        if ($estimate->status === 'converted') {
            return back()->with('error', 'Cannot delete a converted estimate.');
        }

        $estimate->delete();

        return redirect()->route('admin.estimates.index')
            ->with('success', 'Estimate deleted successfully.');
    }

    public function downloadPdf(Estimate $estimate)
    {
        return $this->estimateService->downloadPdf($estimate);
    }

    public function sendEmail(Request $request, Estimate $estimate)
    {
        if (empty($estimate->customer_email)) {
            return back()->with('error', 'Customer email is not available.');
        }

        $customMessage = $request->input('message');

        $sent = $this->estimateService->sendEmail($estimate, $customMessage);

        if ($sent) {
            return back()->with('success', 'Estimate sent successfully via email.');
        }

        return back()->with('error', 'Failed to send estimate. Please try again.');
    }

    public function getWhatsAppUrl(Estimate $estimate)
    {
        $url = $this->estimateService->getWhatsAppUrl($estimate);

        // Update status if draft
        if ($estimate->status === 'draft') {
            $estimate->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'url' => $url,
        ]);
    }

    public function updateStatus(Request $request, Estimate $estimate)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,viewed,accepted,rejected,expired',
        ]);

        $updateData = ['status' => $validated['status']];

        switch ($validated['status']) {
            case 'sent':
                $updateData['sent_at'] = now();
                break;
            case 'viewed':
                $updateData['viewed_at'] = now();
                break;
            case 'accepted':
                $updateData['accepted_at'] = now();
                break;
            case 'rejected':
                $updateData['rejected_at'] = now();
                break;
        }

        $estimate->update($updateData);

        return back()->with('success', 'Estimate status updated.');
    }

    public function duplicate(Estimate $estimate)
    {
        $newEstimate = $estimate->replicate();
        $newEstimate->estimate_number = Estimate::generateEstimateNumber();
        $newEstimate->estimate_date = now();
        $newEstimate->valid_until = now()->addDays(30);
        $newEstimate->status = 'draft';
        $newEstimate->sent_at = null;
        $newEstimate->viewed_at = null;
        $newEstimate->accepted_at = null;
        $newEstimate->rejected_at = null;
        $newEstimate->converted_order_id = null;
        $newEstimate->created_by = auth()->id();
        $newEstimate->save();

        foreach ($estimate->items as $item) {
            $newItem = $item->replicate();
            $newItem->estimate_id = $newEstimate->id;
            $newItem->save();
        }

        $newEstimate->calculateTotals();

        return redirect()->route('admin.estimates.edit', $newEstimate)
            ->with('success', 'Estimate duplicated successfully.');
    }
}
