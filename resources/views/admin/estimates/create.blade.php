@extends('layouts.admin')

@section('title', 'Create Estimate')

@section('content')
<div x-data="estimateForm()" class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Estimate</h1>
            <p class="text-gray-600">Create a new estimate for customer</p>
        </div>
        <a href="{{ route('admin.estimates.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Estimates
        </a>
    </div>

    <form action="{{ route('admin.estimates.store') }}" method="POST" @submit="prepareSubmit">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Customer & Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user text-green-600 mr-2"></i>Customer Details
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            @error('customer_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            @error('customer_phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            @error('customer_email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="customer_city" value="{{ old('customer_city') }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" name="customer_state" value="{{ old('customer_state', 'Tamil Nadu') }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                            <input type="text" name="customer_pincode" value="{{ old('customer_pincode') }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-box text-green-600 mr-2"></i>Items
                        </h2>
                    </div>

                    <!-- Add Product Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" x-model="productSearch" @input="filterProducts" @focus="showDropdown = true"
                                   placeholder="Search products to add..."
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 pl-10">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        
                        <!-- Product Dropdown -->
                        <div x-show="showDropdown && filteredProducts.length > 0" @click.away="showDropdown = false"
                             class="absolute z-10 mt-1 w-full max-w-2xl bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="product in filteredProducts" :key="product.id + '-' + (product.variant_id || 0)">
                                <div @click="addProduct(product)" 
                                     class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center">
                                    <div>
                                        <span x-text="product.name" class="font-medium"></span>
                                        <span x-text="'SKU: ' + product.sku" class="text-sm text-gray-500 ml-2"></span>
                                    </div>
                                    <span class="text-green-600 font-semibold">₹<span x-text="product.price.toFixed(2)"></span></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Price</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">GST%</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Total</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="px-3 py-2">
                                            <div class="font-medium text-gray-800" x-text="item.name"></div>
                                            <div class="text-xs text-gray-500" x-text="'SKU: ' + item.sku"></div>
                                            <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                                            <input type="hidden" :name="'items['+index+'][variant_id]'" :value="item.variant_id">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" x-model.number="item.quantity" min="1" @change="calculateTotals"
                                                   :name="'items['+index+'][quantity]'"
                                                   class="w-20 text-center border-gray-300 rounded focus:ring-green-500 focus:border-green-500">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" x-model.number="item.unit_price" step="0.01" min="0" @change="calculateTotals"
                                                   :name="'items['+index+'][unit_price]'"
                                                   class="w-28 text-right border-gray-300 rounded focus:ring-green-500 focus:border-green-500">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" x-model.number="item.gst_percent" step="0.01" min="0" @change="calculateTotals"
                                                   :name="'items['+index+'][gst_percent]'"
                                                   class="w-20 text-center border-gray-300 rounded focus:ring-green-500 focus:border-green-500">
                                        </td>
                                        <td class="px-3 py-2 text-right font-semibold text-gray-800">
                                            ₹<span x-text="item.total.toFixed(2)"></span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div x-show="items.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-2"></i>
                            <p>No items added. Search and add products above.</p>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-green-600 mr-2"></i>Notes & Terms
                    </h2>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Estimate for..."
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (visible to customer)</label>
                            <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terms & Conditions</label>
                            <textarea name="terms" rows="2" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">{{ old('terms', "1. This estimate is valid for 30 days.\n2. Prices are subject to change.\n3. GST extra as applicable.") }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="space-y-6">
                <!-- Estimate Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-calendar text-green-600 mr-2"></i>Estimate Info
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimate Date *</label>
                            <input type="date" name="estimate_date" value="{{ old('estimate_date', date('Y-m-d')) }}" required
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                            <input type="date" name="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Discount & Shipping -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-percent text-green-600 mr-2"></i>Discount & Shipping
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                            <select name="discount_type" x-model="discountType" @change="calculateTotals"
                                    class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                <option value="fixed">Fixed Amount (₹)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
                            <input type="number" name="discount_value" x-model.number="discountValue" @change="calculateTotals" step="0.01" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Charge (₹)</label>
                            <input type="number" name="shipping_charge" x-model.number="shippingCharge" @change="calculateTotals" step="0.01" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>
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
                            <span>₹<span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-gray-600" x-show="discountAmount > 0">
                            <span>Discount:</span>
                            <span class="text-red-600">-₹<span x-text="discountAmount.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-gray-600" x-show="gstAmount > 0">
                            <span>GST:</span>
                            <span>₹<span x-text="gstAmount.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-gray-600" x-show="shippingCharge > 0">
                            <span>Shipping:</span>
                            <span>₹<span x-text="shippingCharge.toFixed(2)"></span></span>
                        </div>
                        <div class="border-t pt-3 flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span class="text-green-600">₹<span x-text="grandTotal.toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                        <i class="fas fa-save mr-2"></i>Create Estimate
                    </button>
                    <a href="{{ route('admin.estimates.index') }}" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function estimateForm() {
    return {
        products: @json($productsJson),
        filteredProducts: [],
        productSearch: '',
        showDropdown: false,
        items: [],
        discountType: 'fixed',
        discountValue: 0,
        shippingCharge: 0,
        subtotal: 0,
        discountAmount: 0,
        gstAmount: 0,
        grandTotal: 0,

        filterProducts() {
            if (this.productSearch.length < 1) {
                this.filteredProducts = this.products.slice(0, 10);
                return;
            }
            const search = this.productSearch.toLowerCase();
            this.filteredProducts = this.products.filter(p => 
                p.name.toLowerCase().includes(search) || 
                p.sku.toLowerCase().includes(search)
            ).slice(0, 10);
        },

        addProduct(product) {
            // Check if already exists
            const exists = this.items.find(i => 
                i.product_id === product.id && i.variant_id === product.variant_id
            );
            
            if (exists) {
                exists.quantity++;
            } else {
                this.items.push({
                    product_id: product.id,
                    variant_id: product.variant_id,
                    name: product.name,
                    sku: product.sku,
                    quantity: 1,
                    unit_price: product.price,
                    gst_percent: product.gst_percent || 0,
                    total: product.price
                });
            }
            
            this.productSearch = '';
            this.showDropdown = false;
            this.calculateTotals();
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = 0;
            this.gstAmount = 0;

            this.items.forEach(item => {
                const baseTotal = item.quantity * item.unit_price;
                const gst = (baseTotal * item.gst_percent) / 100;
                item.total = baseTotal + gst;
                this.subtotal += baseTotal;
                this.gstAmount += gst;
            });

            if (this.discountType === 'percentage') {
                this.discountAmount = (this.subtotal * this.discountValue) / 100;
            } else {
                this.discountAmount = this.discountValue || 0;
            }

            this.grandTotal = this.subtotal - this.discountAmount + this.gstAmount + (this.shippingCharge || 0);
        },

        prepareSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the estimate.');
                return false;
            }
            return true;
        },

        init() {
            this.filteredProducts = this.products.slice(0, 10);
        }
    }
}
</script>
@endsection
