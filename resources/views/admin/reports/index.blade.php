@extends('layouts.admin')
@section('title', 'Reports')
@section('page_title', 'Reports Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <a href="{{ route('admin.reports.sales') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-green-200 transition">
            <i class="fas fa-chart-line text-green-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Sales Report</h3>
        <p class="text-sm text-gray-500 mt-1">View daily and monthly sales data</p>
    </a>

    <a href="{{ route('admin.reports.products') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-200 transition">
            <i class="fas fa-box text-blue-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Product Report</h3>
        <p class="text-sm text-gray-500 mt-1">View product performance & sales</p>
    </a>

    <a href="{{ route('admin.reports.categories') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-purple-200 transition">
            <i class="fas fa-folder text-purple-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Category Report</h3>
        <p class="text-sm text-gray-500 mt-1">View category-wise revenue</p>
    </a>

    <a href="{{ route('admin.reports.stock') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-yellow-200 transition">
            <i class="fas fa-warehouse text-yellow-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Stock Report</h3>
        <p class="text-sm text-gray-500 mt-1">View inventory & low stock alerts</p>
    </a>

    <a href="{{ route('admin.reports.customers') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-orange-200 transition">
            <i class="fas fa-users text-orange-600 text-xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800">Customer Report</h3>
        <p class="text-sm text-gray-500 mt-1">View top customers & spending</p>
    </a>
</div>
@endsection
