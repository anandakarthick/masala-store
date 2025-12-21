@extends('layouts.admin')

@section('title', 'Create Estimate')

@push('styles')
<style>
    .form-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        background-color: #fff;
    }
    .form-input:focus {
        outline: none;
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background-color: white;
    }
    .form-select:focus {
        outline: none;
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    .form-textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        resize: vertical;
        background-color: #fff;
    }
    .form-textarea:focus {
        outline: none;
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    .product-dropdown {
        position: absolute;
        z-index: 50;
        margin-top: 0.25rem;
        width: 100%;
        background-color: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
    }
    .product-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #f3f4f6;
    }
    .product-item:hover {
        background-color: #f0fdf4;
    }
    .product-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
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

    <form action="{{ route('admin.estimates.store') }}" method="POST" id="estimateForm">
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
                                   class="form-input" placeholder="Enter customer name">
                            @error('customer_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required
                                   class="form-input" placeholder="Enter phone number">
                            @error('customer_phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                   class="form-input" placeholder="Enter email address">
                            @error('customer_email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="customer_city" value="{{ old('customer_city') }}"
                                   class="form-input" placeholder="Enter city">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                                   class="form-input" placeholder="Enter full address">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" name="customer_state" value="{{ old('customer_state', 'Tamil Nadu') }}"
                                   class="form-input" placeholder="Enter state">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                            <input type="text" name="customer_pincode" value="{{ old('customer_pincode') }}"
                                   class="form-input" placeholder="Enter pincode">
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-lg shadow p-6" x-data="itemsManager()">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-box text-green-600 mr-2"></i>Items
                        </h2>
                        <span class="text-sm text-gray-500">
                            <span x-text="items.length"></span> item(s) added
                        </span>
                    </div>

                    <!-- Add Product Search -->
                    <div class="mb-4 relative">
                        <div class="relative">
                            <input type="text" 
                                   x-model="productSearch" 
                                   @input="filterProducts()" 
                                   @focus="showDropdown = true"
                                   @keydown.escape="showDropdown = false"
                                   placeholder="Type to search products..."
                                   class="form-input pl-10">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        
                        <!-- Product Dropdown -->
                        <div x-show="showDropdown && filteredProducts.length > 0" 
                             x-transition
                             @click.away="showDropdown = false"
                             class="product-dropdown">
                            <template x-for="product in filteredProducts" :key="getProductKey(product)">
                                <div @click="addProduct(product)" class="product-item">
                                    <div>
                                        <div class="font-medium text-gray-800" x-text="product.name"></div>
                                        <div class="text-xs text-gray-500">SKU: <span x-text="product.sku || 'N/A'"></span></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-green-600 font-semibold">₹<span x-text="product.price.toFixed(2)"></span></div>
                                        <div class="text-xs text-gray-400">Stock: <span x-text="product.stock"></span></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full" x-show="items.length > 0">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase" style="width: 80px;">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase" style="width: 110px;">Price</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase" style="width: 80px;">GST%</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase" style="width: 110px;">Total</th>
                                    <th class="px-3 py-2" style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3">
                                            <div class="font-medium text-gray-800" x-text="item.name"></div>
                                            <div class="text-xs text-gray-500">SKU: <span x-text="item.sku || 'N/A'"></span></div>
                                            <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                                            <input type="hidden" :name="'items['+index+'][variant_id]'" :value="item.variant_id">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" 
                                                   x-model.number="item.quantity" 
                                                   @input="calculateItemTotal(index); calculateTotals()"
                                                   min="1" 
                                                   :name="'items['+index+'][quantity]'"
                                                   class="form-input text-center" 
                                                   style="width: 70px;">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" 
                                                   x-model.number="item.unit_price" 
                                                   @input="calculateItemTotal(index); calculateTotals()"
                                                   step="0.01" 
                                                   min="0" 
                                                   :name="'items['+index+'][unit_price]'"
                                                   class="form-input text-right" 
                                                   style="width: 100px;">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" 
                                                   x-model.number="item.gst_percent" 
                                                   @input="calculateItemTotal(index); calculateTotals()"
                                                   step="0.01" 
                                                   min="0" 
                                                   :name="'items['+index+'][gst_percent]'"
                                                   class="form-input text-center" 
                                                   style="width: 70px;">
                                        </td>
                                        <td class="px-3 py-3 text-right font-semibold text-gray-800">
                                            ₹<span x-text="item.total.toFixed(2)"></span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div x-show="items.length === 0" class="text-center py-12 text-gray-500">
                            <i class="fas fa-box-open text-5xl mb-3 text-gray-300"></i>
                            <p class="text-lg">No items added yet</p>
                            <p class="text-sm">Search and add products using the search box above</p>
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
                                   class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (visible to customer)</label>
                            <textarea name="notes" rows="2" class="form-textarea" placeholder="Add any notes...">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Terms & Conditions</label>
                            <textarea name="terms" rows="3" class="form-textarea">{{ old('terms', "1. This estimate is valid for 30 days.\n2. Prices are subject to change.\n3. GST extra as applicable.") }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="space-y-6" x-data="summaryManager()">
                <!-- Estimate Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-calendar text-green-600 mr-2"></i>Estimate Info
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimate Date *</label>
                            <input type="date" name="estimate_date" value="{{ old('estimate_date', date('Y-m-d')) }}" required
                                   class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until</label>
                            <input type="date" name="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}"
                                   class="form-input">
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
                            <select name="discount_type" x-model="discountType" @change="window.dispatchEvent(new CustomEvent('recalculate'))"
                                    class="form-select">
                                <option value="fixed">Fixed Amount (₹)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
                            <input type="number" name="discount_value" x-model.number="discountValue" 
                                   @input="window.dispatchEvent(new CustomEvent('recalculate'))"
                                   step="0.01" min="0" class="form-input" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Charge (₹)</label>
                            <input type="number" name="shipping_charge" x-model.number="shippingCharge" 
                                   @input="window.dispatchEvent(new CustomEvent('recalculate'))"
                                   step="0.01" min="0" class="form-input" placeholder="0">
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
// Products data
const allProducts = @json($productsJson);

// Shared items array
window.estimateItems = [];

function itemsManager() {
    return {
        products: allProducts,
        filteredProducts: [],
        productSearch: '',
        showDropdown: false,
        items: window.estimateItems,

        init() {
            this.filteredProducts = this.products.slice(0, 15);
        },

        getProductKey(product) {
            return product.id + '-' + product.variant_id;
        },

        filterProducts() {
            if (this.productSearch.length < 1) {
                this.filteredProducts = this.products.slice(0, 15);
                this.showDropdown = true;
                return;
            }
            const search = this.productSearch.toLowerCase();
            this.filteredProducts = this.products.filter(p => 
                p.name.toLowerCase().includes(search) || 
                (p.sku && p.sku.toLowerCase().includes(search))
            ).slice(0, 15);
            this.showDropdown = true;
        },

        addProduct(product) {
            // Check if already exists
            const existingIndex = this.items.findIndex(i => 
                i.product_id === product.id && i.variant_id === product.variant_id
            );
            
            if (existingIndex > -1) {
                this.items[existingIndex].quantity++;
                this.calculateItemTotal(existingIndex);
            } else {
                const newItem = {
                    product_id: product.id,
                    variant_id: product.variant_id,
                    name: product.name,
                    sku: product.sku || '',
                    quantity: 1,
                    unit_price: product.price,
                    gst_percent: product.gst_percent || 0,
                    total: product.price
                };
                this.items.push(newItem);
                this.calculateItemTotal(this.items.length - 1);
            }
            
            this.productSearch = '';
            this.showDropdown = false;
            this.calculateTotals();
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        calculateItemTotal(index) {
            const item = this.items[index];
            if (item) {
                const baseTotal = item.quantity * item.unit_price;
                const gst = (baseTotal * item.gst_percent) / 100;
                item.total = baseTotal + gst;
            }
        },

        calculateTotals() {
            window.dispatchEvent(new CustomEvent('recalculate'));
        }
    }
}

function summaryManager() {
    return {
        discountType: 'fixed',
        discountValue: 0,
        shippingCharge: 0,
        subtotal: 0,
        discountAmount: 0,
        gstAmount: 0,
        grandTotal: 0,

        init() {
            this.calculate();
            window.addEventListener('recalculate', () => this.calculate());
        },

        calculate() {
            const items = window.estimateItems;
            
            this.subtotal = 0;
            this.gstAmount = 0;

            items.forEach(item => {
                const baseTotal = item.quantity * item.unit_price;
                const gst = (baseTotal * (item.gst_percent || 0)) / 100;
                this.subtotal += baseTotal;
                this.gstAmount += gst;
            });

            if (this.discountType === 'percentage') {
                this.discountAmount = (this.subtotal * (this.discountValue || 0)) / 100;
            } else {
                this.discountAmount = this.discountValue || 0;
            }

            this.grandTotal = this.subtotal - this.discountAmount + this.gstAmount + (this.shippingCharge || 0);
        }
    }
}

// Form validation
document.getElementById('estimateForm').addEventListener('submit', function(e) {
    if (window.estimateItems.length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the estimate.');
        return false;
    }
    return true;
});
</script>
@endsection
