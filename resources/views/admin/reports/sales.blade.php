@extends('layouts.admin')
@section('title', 'Sales Report')
@section('page_title', 'Sales Report')

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
        <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-download mr-2"></i>Export CSV
        </a>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">Total Orders</div>
        <div class="text-2xl font-bold text-gray-800">{{ $summary['total_orders'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">Total Revenue</div>
        <div class="text-2xl font-bold text-green-600">₹{{ number_format($summary['total_revenue'], 2) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">Total GST Collected</div>
        <div class="text-2xl font-bold text-blue-600">₹{{ number_format($summary['total_gst'], 2) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">Average Order Value</div>
        <div class="text-2xl font-bold text-orange-600">₹{{ number_format($summary['average_order_value'], 2) }}</div>
    </div>
</div>

<!-- Daily Sales Table -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Daily Sales Breakdown</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($dailySales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ \Carbon\Carbon::parse($sale->date)->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ $sale->orders_count }}</td>
                        <td class="px-6 py-4 text-green-600 font-medium">₹{{ number_format($sale->total_revenue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            No sales data for the selected period
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($dailySales->count() > 0)
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 font-bold">Total</td>
                    <td class="px-6 py-4 font-bold">{{ $dailySales->sum('orders_count') }}</td>
                    <td class="px-6 py-4 font-bold text-green-600">₹{{ number_format($dailySales->sum('total_revenue'), 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
