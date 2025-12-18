@extends('layouts.admin')
@section('title', 'Create Order')
@section('page_title', 'Create New Order')

@section('content')
<form action="{{ route('admin.orders.store') }}" method="POST" x-data="orderForm()" x-init="init()">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Customer Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                        <input type="tel" name="customer_phone" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="customer_email"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                        <textarea name="shipping_address" rows="2" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                            <input type="text" name="shipping_city" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                            <input type="text" name="shipping_state" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pincode *</label>
                            <input type="text" name="shipping_pincode" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Order Items</h3>
                
                <!-- Add Product Search -->
                <div class="mb-4 relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search & Add Product</label>
                    <div class="relative">
                        <input type="text" 
                               x-model="search"
                               @focus="searchOpen = true"
                               @click.away="setTimeout(function() { searchOpen = false }, 200)"
                               @keydown.escape="searchOpen = false"
                               placeholder="Type to search products..."
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-orange-500 focus:border-orange-500">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="searchOpen && search.length >= 2" 
                         x-cloak
                         class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                        <template x-for="product in getFilteredProducts()" :key="product.id + '-' + (product.variant_id || 0)">
                            <div @click="addProduct(product)"
                                 class="px-4 py-3 hover:bg-orange-50 cursor-pointer border-b last:border-b-0">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-800" x-text="product.name"></p>
                                        <p class="text-sm text-gray-500">
                                            SKU: <span x-text="product.sku"></span> | 
                                            Stock: <span x-text="product.stock" :class="product.stock <= 0 ? 'text-red-600' : 'text-green-600'"></span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-orange-600">₹<span x-text="formatPrice(product.price)"></span></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="getFilteredProducts().length === 0" class="px-4 py-3 text-gray-500 text-center">
                            No products found
                        </div>
                    </div>
                </div>

                <!-- Selected Items Table -->
                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Price</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Total</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-3">
                                        <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                                        <input type="hidden" :name="'items[' + index + '][variant_id]'" :value="item.variant_id" x-show="item.variant_id">
                                        <p class="font-medium text-gray-800" x-text="item.name"></p>
                                        <p class="text-sm text-gray-500" x-text="'SKU: ' + item.sku"></p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" 
                                               x-model.number="item.quantity" 
                                               :name="'items[' + index + '][quantity]'"
                                               min="1" 
                                               :max="item.stock"
                                               class="w-20 text-center border border-gray-300 rounded px-2 py-1 focus:ring-orange-500 focus:border-orange-500">
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600">
                                        ₹<span x-text="formatPrice(item.price)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                                        ₹<span x-text="formatPrice(item.price * item.quantity)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-box-open text-3xl mb-2"></i>
                                    <p>No items added. Search and add products above.</p>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot x-show="items.length > 0" class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-medium">Subtotal:</td>
                                <td class="px-4 py-3 text-right font-bold text-lg text-orange-600">₹<span x-text="formatPrice(getSubtotal())"></span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Order Notes</h3>
                <textarea name="customer_notes" rows="3" placeholder="Any special instructions..."
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Type -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Order Type</h3>
                <select name="order_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="retail">Retail</option>
                    <option value="bulk">Bulk</option>
                    <option value="return_gift">Return Gift</option>
                </select>
            </div>

            <!-- Payment -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Payment Method</h3>
                <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="cod">Cash on Delivery</option>
                    <option value="upi">UPI</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Items</span>
                        <span x-text="items.length + ' products'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Quantity</span>
                        <span x-text="getTotalQuantity() + ' units'"></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t font-bold text-lg">
                        <span>Total</span>
                        <span class="text-orange-600">₹<span x-text="formatPrice(getSubtotal())"></span></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <button type="submit" 
                        :disabled="items.length === 0"
                        :class="items.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-orange-600 hover:bg-orange-700'"
                        class="w-full text-white py-3 rounded-lg font-semibold">
                    Create Order
                </button>
                <a href="{{ route('admin.orders.index') }}" class="block w-full text-center bg-gray-200 text-gray-700 py-3 rounded-lg mt-2">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<script>
    window.productsData = {!! json_encode($productsJson) !!};
</script>

<script>
function orderForm() {
    return {
        items: [],
        products: [],
        search: '',
        searchOpen: false,
        
        init() {
            this.products = window.productsData || [];
        },
        
        formatPrice(value) {
            return parseFloat(value).toFixed(2);
        },
        
        getSubtotal() {
            var sum = 0;
            for (var i = 0; i < this.items.length; i++) {
                sum += this.items[i].price * this.items[i].quantity;
            }
            return sum;
        },
        
        getTotalQuantity() {
            var sum = 0;
            for (var i = 0; i < this.items.length; i++) {
                sum += this.items[i].quantity;
            }
            return sum;
        },
        
        getFilteredProducts() {
            if (!this.search || this.search.length < 2) return [];
            var searchLower = this.search.toLowerCase();
            var results = [];
            for (var i = 0; i < this.products.length; i++) {
                var p = this.products[i];
                if (p.name.toLowerCase().indexOf(searchLower) !== -1 || 
                    (p.sku && p.sku.toLowerCase().indexOf(searchLower) !== -1)) {
                    results.push(p);
                    if (results.length >= 10) break;
                }
            }
            return results;
        },
        
        addProduct(product) {
            var itemKey = product.id + '-' + (product.variant_id || 0);
            
            for (var i = 0; i < this.items.length; i++) {
                var existingKey = this.items[i].product_id + '-' + (this.items[i].variant_id || 0);
                if (existingKey === itemKey) {
                    if (this.items[i].quantity < product.stock) {
                        this.items[i].quantity++;
                    }
                    this.search = '';
                    this.searchOpen = false;
                    return;
                }
            }
            
            if (product.stock <= 0) {
                alert('This product is out of stock');
                return;
            }
            
            this.items.push({
                product_id: product.id,
                variant_id: product.variant_id || null,
                name: product.name,
                sku: product.sku || '',
                price: product.price,
                quantity: 1,
                stock: product.stock
            });
            
            this.search = '';
            this.searchOpen = false;
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
        }
    }
}
</script>
@endsection
