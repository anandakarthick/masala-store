@extends('layouts.admin')

@section('title', 'Custom Combos - Build Your Own')
@section('page-title', 'Custom Combos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Build Your Own Combo</h1>
            <p class="text-gray-600">Manage custom combo settings for customers</p>
        </div>
        <a href="{{ route('admin.combos.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
            <i class="fas fa-plus"></i> Add New Combo
        </a>
    </div>

    <!-- Combos List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Combo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($combos as $combo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-purple-100">
                                    @if($combo->image_url)
                                        <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center">
                                            <i class="fas fa-boxes text-purple-400 text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $combo->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($combo->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $combo->min_products }} - {{ $combo->max_products }} products</div>
                            <div class="text-xs text-gray-500">
                                @if($combo->allow_same_product)
                                    <i class="fas fa-check text-green-500"></i> Same product allowed
                                @else
                                    <i class="fas fa-times text-red-500"></i> Unique products only
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                {{ $combo->discount_display }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="toggleStatus({{ $combo->id }})" 
                                    id="status-btn-{{ $combo->id }}"
                                    class="{{ $combo->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 text-xs font-medium rounded-full">
                                {{ $combo->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.combos.edit', $combo) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.combos.destroy', $combo) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this combo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-boxes text-4xl text-gray-300 mb-3"></i>
                            <p class="text-lg font-medium">No combos created yet</p>
                            <p class="text-sm">Create your first combo to let customers build their own bundles</p>
                            <a href="{{ route('admin.combos.create') }}" class="mt-4 inline-block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-plus mr-2"></i> Create Combo
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($combos->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $combos->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(id) {
    fetch(`/admin/combos/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById(`status-btn-${id}`);
            btn.textContent = data.is_active ? 'Active' : 'Inactive';
            btn.className = data.is_active 
                ? 'bg-green-100 text-green-800 px-2 py-1 text-xs font-medium rounded-full'
                : 'bg-red-100 text-red-800 px-2 py-1 text-xs font-medium rounded-full';
        }
    });
}
</script>
@endpush
@endsection
