@extends('layouts.admin')
@section('title', 'Category Report')
@section('page_title', 'Category Report')

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

<!-- Categories Table -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Category Performance</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="font-medium text-gray-800 hover:text-orange-600">
                                {{ $category->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $category->products_count }}</td>
                        <td class="px-6 py-4 text-green-600 font-medium">
                            ₹{{ number_format($category->total_revenue ?? 0, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            No category data found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
