@extends('layouts.admin')

@section('title', 'Categories')
@section('page_title', 'Categories')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">All Categories</h2>
            <p class="text-sm text-gray-500">Manage your product categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Add Category
        </a>
    </div>

    <!-- Filters -->
    <div class="p-4 border-b bg-gray-50">
        <form action="" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Search categories..." value="{{ request('search') }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg">Filter</button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($category->image)
                                    <img src="{{ $category->image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover mr-3">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                        <i class="fas fa-folder text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $category->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $category->parent?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $category->products_count }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-folder-open text-4xl mb-2"></i>
                            <p>No categories found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $categories->links() }}
    </div>
</div>
@endsection
