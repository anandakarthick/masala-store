@extends('layouts.admin')
@section('title', 'Pages')
@section('page_title', 'Manage Pages')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <p class="text-gray-600 text-sm">Create and manage legal pages like Privacy Policy, Terms & Conditions, etc.</p>
    <a href="{{ route('admin.pages.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <i class="fas fa-plus"></i> Create New Page
    </a>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold flex items-center">
            <i class="fas fa-file-alt text-blue-500 mr-2"></i>
            All Pages ({{ $pages->count() }})
        </h3>
    </div>
    <div class="p-6">
        @if($pages->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Page Title</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Slug</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Status</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Show in Footer</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Order</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800">{{ $page->title }}</div>
                                    <div class="text-xs text-gray-500">Updated {{ $page->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">/page/{{ $page->slug }}</code>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $page->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($page->show_in_footer)
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    @else
                                        <i class="fas fa-times-circle text-gray-400"></i>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-gray-600">{{ $page->sort_order }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('page.show', $page->slug) }}" target="_blank"
                                           class="text-gray-500 hover:text-gray-700" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pages.edit', $page) }}" 
                                           class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Delete this page?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-alt text-3xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-600 mb-2">No Pages Yet</h4>
                <p class="text-gray-500 text-sm mb-4">Create your first page like Privacy Policy or Terms & Conditions</p>
                <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-plus"></i> Create Page
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Quick Templates -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
        <i class="fas fa-lightbulb mr-2"></i> Quick Start Templates
    </h4>
    <p class="text-sm text-blue-600 mb-3">Create common pages with pre-filled templates:</p>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.pages.create', ['template' => 'privacy']) }}" 
           class="bg-white border border-blue-300 text-blue-700 px-3 py-1.5 rounded text-sm hover:bg-blue-100">
            <i class="fas fa-shield-alt mr-1"></i> Privacy Policy
        </a>
        <a href="{{ route('admin.pages.create', ['template' => 'terms']) }}" 
           class="bg-white border border-blue-300 text-blue-700 px-3 py-1.5 rounded text-sm hover:bg-blue-100">
            <i class="fas fa-file-contract mr-1"></i> Terms & Conditions
        </a>
        <a href="{{ route('admin.pages.create', ['template' => 'refund']) }}" 
           class="bg-white border border-blue-300 text-blue-700 px-3 py-1.5 rounded text-sm hover:bg-blue-100">
            <i class="fas fa-undo mr-1"></i> Refund Policy
        </a>
        <a href="{{ route('admin.pages.create', ['template' => 'shipping']) }}" 
           class="bg-white border border-blue-300 text-blue-700 px-3 py-1.5 rounded text-sm hover:bg-blue-100">
            <i class="fas fa-truck mr-1"></i> Shipping Policy
        </a>
    </div>
</div>
@endsection
