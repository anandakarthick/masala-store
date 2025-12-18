@extends('layouts.admin')
@section('title', 'Coupons')
@section('page_title', 'Coupons')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold">All Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Add Coupon
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Until</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-bold text-orange-600">{{ $coupon->code }}</td>
                        <td class="px-6 py-4">{{ $coupon->name }}</td>
                        <td class="px-6 py-4">
                            @if($coupon->type === 'percentage')
                                {{ $coupon->value }}%
                            @else
                                ₹{{ number_format($coupon->value, 2) }}
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $coupon->usage_count }}/{{ $coupon->usage_limit ?? '∞' }}</td>
                        <td class="px-6 py-4">{{ $coupon->end_date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $coupon->isValid() ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                {{ $coupon->isValid() ? 'Active' : 'Expired' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No coupons found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $coupons->links() }}</div>
</div>
@endsection
