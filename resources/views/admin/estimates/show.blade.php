@extends('layouts.admin')

@section('title', 'Estimate ' . $estimate->estimate_number)

@section('content')
<div class="space-y-6" x-data="estimateShare()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $estimate->estimate_number }}</h1>
                {!! $estimate->status_badge !!}
            </div>
            <p class="text-gray-600">Created on {{ $estimate->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.estimates.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            @if($estimate->canBeEdited())
                <a href="{{ route('admin.estimates.edit', $estimate) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('admin.estimates.download', $estimate) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i>Download PDF
            </a>
            <a href="{{ route('admin.estimates.duplicate', $estimate) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-copy mr-2"></i>Duplicate
            </a>
        </div>
    </div>

    <!-- Share Options -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
        <h2 class="text-lg font-semibold mb-4">
            <i class="fas fa-share-alt mr-2"></i>Share Estimate with Customer
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Email -->
            <button @click="showEmailModal = true" 
                    class="flex items-center justify-center gap-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg p-4 transition">
                <i class="fas fa-envelope text-2xl"></i>
                <div class="text-left">
                    <div class="font-semibold">Send via Email</div>
                    <div class="text-sm text-white/80">{{ $estimate->customer_email ?: 'No email' }}</div>
                </div>
            </button>
            
            <!-- WhatsApp -->
            <button @click="shareWhatsApp()" 
                    class="flex items-center justify-center gap-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg p-4 transition">
                <i class="fab fa-whatsapp text-2xl"></i>
                <div class="text-left">
                    <div class="font-semibold">Send via WhatsApp</div>
                    <div class="text-sm text-white/80">{{ $estimate->customer_phone }}</div>
                </div>
            </button>
            
            <!-- Copy Link -->
            <button @click="copyLink()" 
                    class="flex items-center justify-center gap-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-lg p-4 transition">
                <i class="fas fa-link text-2xl"></i>
                <div class="text-left">
                    <div class="font-semibold">Copy PDF Link</div>
                    <div class="text-sm text-white/80">Share anywhere</div>
                </div>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user text-green-600 mr-2"></i>Customer Details
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Name</label>
                        <p class="font-medium">{{ $estimate->customer_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Phone</label>
                        <p class="font-medium">{{ $estimate->customer_phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="font-medium">{{ $estimate->customer_email ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">City</label>
                        <p class="font-medium">{{ $estimate->customer_city ?: '-' }}</p>
                    </div>
                    @if($estimate->customer_address)
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-500">Address</label>
                            <p class="font-medium">{{ $estimate->customer_address }}, {{ $estimate->customer_city }}, {{ $estimate->customer_state }} - {{ $estimate->customer_pincode }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-box text-green-600 mr-2"></i>Items
                    </h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">GST</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($estimate->items as $index => $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $item->product_name }}</div>
                                    @if($item->variant_name)
                                        <div class="text-sm text-green-600">{{ $item->variant_name }}</div>
                                    @endif
                                    @if($item->product_sku)
                                        <div class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-500">
                                    @if($item->gst_percent > 0)
                                        {{ $item->gst_percent }}%<br>
                                        <span class="text-xs">₹{{ number_format($item->gst_amount, 2) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Notes & Terms -->
            @if($estimate->subject || $estimate->notes || $estimate->terms)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-green-600 mr-2"></i>Notes & Terms
                    </h2>
                    @if($estimate->subject)
                        <div class="mb-3">
                            <label class="text-sm text-gray-500">Subject</label>
                            <p class="font-medium">{{ $estimate->subject }}</p>
                        </div>
                    @endif
                    @if($estimate->notes)
                        <div class="mb-3">
                            <label class="text-sm text-gray-500">Notes</label>
                            <p class="text-gray-700 whitespace-pre-line">{{ $estimate->notes }}</p>
                        </div>
                    @endif
                    @if($estimate->terms)
                        <div>
                            <label class="text-sm text-gray-500">Terms & Conditions</label>
                            <p class="text-gray-700 whitespace-pre-line">{{ $estimate->terms }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Estimate Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>Estimate Info
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Estimate Date:</span>
                        <span class="font-medium">{{ $estimate->estimate_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Valid Until:</span>
                        <span class="font-medium {{ $estimate->isExpired() ? 'text-red-600' : '' }}">
                            {{ $estimate->valid_until ? $estimate->valid_until->format('d M Y') : '-' }}
                            @if($estimate->isExpired())
                                <span class="text-xs">(Expired)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created By:</span>
                        <span class="font-medium">{{ $estimate->createdBy?->name ?? 'System' }}</span>
                    </div>
                    @if($estimate->sent_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Sent At:</span>
                            <span class="font-medium">{{ $estimate->sent_at->format('d M Y, h:i A') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-calculator text-green-600 mr-2"></i>Summary
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span>₹{{ number_format($estimate->subtotal, 2) }}</span>
                    </div>
                    @if($estimate->discount_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Discount:</span>
                            <span class="text-red-600">-₹{{ number_format($estimate->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($estimate->gst_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>GST:</span>
                            <span>₹{{ number_format($estimate->gst_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($estimate->shipping_charge > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping:</span>
                            <span>₹{{ number_format($estimate->shipping_charge, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t pt-3 flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span class="text-green-600">₹{{ number_format($estimate->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Update -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-tasks text-green-600 mr-2"></i>Update Status
                </h2>
                <form action="{{ route('admin.estimates.update-status', $estimate) }}" method="POST">
                    @csrf
                    <select name="status" onchange="this.form.submit()"
                            class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="draft" {{ $estimate->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ $estimate->status === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="viewed" {{ $estimate->status === 'viewed' ? 'selected' : '' }}>Viewed</option>
                        <option value="accepted" {{ $estimate->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ $estimate->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="expired" {{ $estimate->status === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </form>
            </div>

            <!-- Delete -->
            @if($estimate->status !== 'converted')
                <form action="{{ route('admin.estimates.destroy', $estimate) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this estimate?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-lg font-semibold">
                        <i class="fas fa-trash mr-2"></i>Delete Estimate
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Email Modal -->
    <div x-show="showEmailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showEmailModal = false"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-envelope text-green-600 mr-2"></i>Send Estimate via Email
                </h3>
                <form action="{{ route('admin.estimates.send-email', $estimate) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Send To</label>
                            <input type="email" value="{{ $estimate->customer_email }}" disabled
                                   class="w-full border-gray-300 rounded-lg bg-gray-100">
                            @if(!$estimate->customer_email)
                                <p class="text-red-500 text-sm mt-1">Customer email is not available</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Custom Message (Optional)</label>
                            <textarea name="message" rows="3" placeholder="Add a personal message..."
                                      class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showEmailModal = false" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" {{ !$estimate->customer_email ? 'disabled' : '' }}
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane mr-2"></i>Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition
         class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <span x-text="toastMessage"></span>
    </div>
</div>

<script>
function estimateShare() {
    return {
        showEmailModal: false,
        showToast: false,
        toastMessage: '',

        async shareWhatsApp() {
            try {
                const response = await fetch('{{ route("admin.estimates.whatsapp", $estimate) }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                if (data.success) {
                    window.open(data.url, '_blank');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },

        async copyLink() {
            const pdfUrl = '{{ route("admin.estimates.download", $estimate) }}';
            try {
                await navigator.clipboard.writeText(pdfUrl);
                this.showNotification('PDF link copied to clipboard!');
            } catch (error) {
                // Fallback
                const input = document.createElement('input');
                input.value = pdfUrl;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                this.showNotification('PDF link copied to clipboard!');
            }
        },

        showNotification(message) {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }
}
</script>
@endsection
