@extends('layouts.admin')
@section('title', 'Customer Report')
@section('page_title', 'Customer Report')

@section('content')
<!-- Date Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg">
            Apply Filter
        </button>
    </form>
</div>

<!-- Top Customers Table -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Top Customers</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spent</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($topCustomers as $index => $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            @if($index < 3)
                                <span class="w-8 h-8 rounded-full inline-flex items-center justify-center 
                                    {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-600') }}">
                                    {{ $index + 1 }}
                                </span>
                            @else
                                <span class="text-gray-600">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $customer->customer_name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $customer->customer_phone }}</td>
                        <td class="px-6 py-4">{{ $customer->orders_count }}</td>
                        <td class="px-6 py-4 text-green-600 font-medium">₹{{ number_format($customer->total_spent, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No customer data for the selected period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
