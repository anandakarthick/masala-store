<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendDeliveryUpdateNotification;
use App\Jobs\SendOrderCancellationNotification;
use App\Jobs\SendOrderEmails;
use App\Jobs\SendOrderStatusEmail;
use App\Jobs\SendReviewRequestEmail;
use App\Models\DeliveryPartner;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\InvoiceService;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product', 'reviews.product', 'reviews.orderItem', 'reviews.user');
        $deliveryPartners = DeliveryPartner::active()->get();

        // Mark order as seen by admin
        $order->markAsSeen();

        return view('admin.orders.show', compact('order', 'deliveryPartners'));
    }

    public function create()
    {
        $products = Product::active()->get();
        
        $productsJson = [];
        foreach ($products as $p) {
            if ($p->has_variants) {
                foreach ($p->activeVariants as $v) {
                    $productsJson[] = [
                        'id' => $p->id,
                        'variant_id' => $v->id,
                        'name' => $p->name . ' - ' . $v->name,
                        'sku' => $v->sku,
                        'price' => (float) $v->effective_price,
                        'stock' => (int) $v->stock_quantity,
                    ];
                }
            } else {
                if ($p->stock_quantity > 0) {
                    $productsJson[] = [
                        'id' => $p->id,
                        'variant_id' => null,
                        'name' => $p->name,
                        'sku' => $p->sku ?? '',
                        'price' => (float) $p->effective_price,
                        'stock' => (int) $p->stock_quantity,
                    ];
                }
            }
        }
        
        return view('admin.orders.create', compact('products', 'productsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_pincode' => 'required|string|max:10',
            'order_type' => 'required|in:retail,bulk,return_gift',
            'payment_method' => 'required|in:cod,upi,bank_transfer',
            'customer_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        $gstAmount = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $variant = isset($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
            $quantity = $item['quantity'];
            $unitPrice = $variant ? $variant->effective_price : $product->effective_price;
            $itemTotal = $unitPrice * $quantity;
            $itemGst = $product->calculateGst($itemTotal);

            $subtotal += $itemTotal;
            $gstAmount += $itemGst;

            $orderItems[] = [
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'product_name' => $product->name,
                'product_sku' => $variant ? $variant->sku : $product->sku,
                'variant_name' => $variant ? $variant->name : null,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'gst_amount' => $itemGst,
                'total_price' => $itemTotal + $itemGst,
            ];
        }

        $shippingCharge = $subtotal >= 500 ? 0 : 50;
        $totalAmount = $subtotal + $gstAmount + $shippingCharge;

        $order = Order::create([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'shipping_city' => $validated['shipping_city'],
            'shipping_state' => $validated['shipping_state'],
            'shipping_pincode' => $validated['shipping_pincode'],
            'order_type' => $validated['order_type'],
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'shipping_charge' => $shippingCharge,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'customer_notes' => $validated['customer_notes'],
            'status' => 'confirmed',
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);

            if ($item['variant_id']) {
                ProductVariant::find($item['variant_id'])->decrement('stock_quantity', $item['quantity']);
            } else {
                $product = Product::find($item['product_id']);
                StockMovement::recordMovement(
                    $product,
                    'out',
                    $item['quantity'],
                    "Order #{$order->order_number}",
                    'Stock reduced for order'
                );
            }
        }

        // Send order emails
        SendOrderEmails::dispatch($order->fresh()->load('items.product'));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,packed,shipped,delivered,cancelled,returned',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];
        
        $order->update(['status' => $newStatus]);

        // Handle cancelled order - restore stock
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    ProductVariant::find($item->variant_id)->increment('stock_quantity', $item->quantity);
                } else {
                    StockMovement::recordMovement(
                        $item->product,
                        'in',
                        $item->quantity,
                        "Order #{$order->order_number} cancelled",
                        'Stock restored due to order cancellation'
                    );
                }
            }
            
            // Refund wallet amount if used
            if ($order->wallet_amount_used > 0 && $order->user_id) {
                $order->user->addToWallet(
                    $order->wallet_amount_used,
                    'refund',
                    "Refund for cancelled Order #{$order->order_number}",
                    $order->id
                );
            }
            
            $order->update([
                'cancelled_at' => now(),
                'cancelled_by' => 'admin',
            ]);
            
            // Send cancellation notification
            SendOrderCancellationNotification::dispatch($order->fresh(), 'admin', null);
        }

        if ($newStatus === 'delivered') {
            $order->update(['delivered_at' => now()]);
            
            // Process referral reward when order is delivered
            $this->processReferralRewardOnDelivery($order);
            
            // Send review request email when order is delivered
            if ($order->customer_email && !$order->hasReviewBeenRequested()) {
                SendReviewRequestEmail::dispatch($order->fresh()->load('items.product'))
                    ->delay(now()->addHour());
            }
        }

        // Send status update email and push notification in background
        if ($oldStatus !== $newStatus && $newStatus !== 'cancelled') {
            SendOrderStatusEmail::dispatch($order->fresh()->load('items.product'), $oldStatus, $newStatus);
        }

        return back()->with('success', 'Order status updated.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'transaction_id' => 'nullable|string',
        ]);

        $order->update($validated);

        return back()->with('success', 'Payment status updated.');
    }

    public function updateDelivery(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_partner' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'expected_delivery_date' => 'nullable|date',
            'delivery_notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
            'remove_attachments' => 'nullable|boolean',
            'send_notification' => 'nullable|boolean',
        ]);

        // Handle attachment removal
        if ($request->boolean('remove_attachments') && $order->delivery_attachments) {
            foreach ($order->delivery_attachments as $attachment) {
                Storage::disk('public')->delete($attachment);
            }
            $validated['delivery_attachments'] = [];
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            $attachments = $order->delivery_attachments ?? [];
            
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('delivery-attachments/' . $order->id, 'public');
                $attachments[] = $path;
            }
            
            $validated['delivery_attachments'] = $attachments;
        }

        // Remove non-model fields
        unset($validated['attachments'], $validated['remove_attachments'], $validated['send_notification']);

        $order->update($validated);

        // Send notification if requested
        if ($request->boolean('send_notification')) {
            SendDeliveryUpdateNotification::dispatch($order->fresh());
        }

        return back()->with('success', 'Delivery information updated.' . ($request->boolean('send_notification') ? ' Notification sent to customer.' : ''));
    }

    public function generateInvoice(Order $order, InvoiceService $invoiceService)
    {
        return $invoiceService->downloadInvoice($order);
    }

    public function addNote(Request $request, Order $order)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $existingNotes = $order->admin_notes ?? '';
        $newNote = now()->format('Y-m-d H:i') . " - " . auth()->user()->name . ":\n" . $validated['admin_notes'];

        $order->update([
            'admin_notes' => $existingNotes . "\n\n" . $newNote,
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    /**
     * Process referral reward when order is delivered
     */
    protected function processReferralRewardOnDelivery(Order $order): void
    {
        try {
            if (!ReferralService::isEnabled()) {
                return;
            }

            $customer = $order->user;
            if (!$customer || !$customer->wasReferred()) {
                return;
            }

            $result = ReferralService::processOrderReferralReward($order);

            if ($result) {
                Log::info('Referral reward processed successfully on delivery', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process referral reward on delivery', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
