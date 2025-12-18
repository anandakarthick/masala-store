@extends('layouts.admin')
@section('title', 'Customers')
@section('page_title', 'Customers')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">All Customers</h2>
        <a href="{{ route('admin.customers.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Add Customer
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $customer->name }}</p>
                            <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $customer->orders_count }}</td>
                        <td class="px-6 py-4 font-medium">₹{{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No customers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $customers->links() }}</div>
</div>
@endsection
