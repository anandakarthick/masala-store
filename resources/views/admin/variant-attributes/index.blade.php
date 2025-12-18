@extends('layouts.admin')

@section('title', 'Variant Attributes')
@section('page_title', 'Variant Attributes')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add New Attribute -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-plus text-green-600 mr-2"></i>Add New Attribute
        </h3>
        
        <form action="{{ route('admin.variant-attributes.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attribute Name *</label>
                    <input type="text" name="name" required placeholder="e.g., Size, Color, Brand"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                    <input type="text" name="code" required placeholder="e.g., size, color, brand"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Lowercase, no spaces. Used internally.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        <option value="select">Select (Dropdown/Buttons)</option>
                        <option value="color">Color</option>
                        <option value="text">Text Input</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Type *</label>
                    <select name="display_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        <option value="dropdown">Dropdown</option>
                        <option value="buttons">Buttons</option>
                        <option value="color_swatch">Color Swatch</option>
                    </select>
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold">
                    <i class="fas fa-plus mr-1"></i> Add Attribute
                </button>
            </div>
        </form>
    </div>
    
    <!-- Existing Attributes -->
    <div class="lg:col-span-2 space-y-4">
        @forelse($attributes as $attribute)
        <div class="bg-white rounded-lg shadow-md p-6" x-data="{ expanded: false }">
            <!-- Attribute Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $attribute->type === 'color' ? 'bg-pink-100' : 'bg-blue-100' }}">
                        <i class="fas {{ $attribute->type === 'color' ? 'fa-palette text-pink-600' : 'fa-tags text-blue-600' }}"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg">{{ $attribute->name }}</h4>
                        <p class="text-sm text-gray-500">
                            Code: {{ $attribute->code }} | 
                            Type: {{ ucfirst($attribute->type) }} |
                            Values: {{ $attribute->values->count() }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs rounded-full {{ $attribute->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ $attribute->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <button @click="expanded = !expanded" class="text-gray-500 hover:text-gray-700">
                        <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>
            </div>
            
            <!-- Expanded Content -->
            <div x-show="expanded" x-cloak class="mt-4 pt-4 border-t">
                <!-- Edit Attribute Form -->
                <form action="{{ route('admin.variant-attributes.update', $attribute) }}" method="POST" class="mb-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Name</label>
                            <input type="text" name="name" value="{{ $attribute->name }}" required
                                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Type</label>
                            <select name="type" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="select" {{ $attribute->type === 'select' ? 'selected' : '' }}>Select</option>
                                <option value="color" {{ $attribute->type === 'color' ? 'selected' : '' }}>Color</option>
                                <option value="text" {{ $attribute->type === 'text' ? 'selected' : '' }}>Text</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Display</label>
                            <select name="display_type" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="dropdown" {{ $attribute->display_type === 'dropdown' ? 'selected' : '' }}>Dropdown</option>
                                <option value="buttons" {{ $attribute->display_type === 'buttons' ? 'selected' : '' }}>Buttons</option>
                                <option value="color_swatch" {{ $attribute->display_type === 'color_swatch' ? 'selected' : '' }}>Color Swatch</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $attribute->is_active ? 'checked' : '' }}
                                       class="text-green-600 focus:ring-green-500 rounded">
                                <span class="ml-2 text-sm">Active</span>
                            </label>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Values List -->
                <div class="mb-4">
                    <h5 class="font-medium text-sm mb-2">Values:</h5>
                    <div class="flex flex-wrap gap-2">
                        @foreach($attribute->values as $value)
                        <div class="flex items-center gap-1 px-3 py-1 bg-gray-100 rounded-full text-sm group">
                            @if($value->color_code)
                                <span class="w-4 h-4 rounded-full border" style="background-color: {{ $value->color_code }}"></span>
                            @endif
                            <span>{{ $value->value }}</span>
                            @if($value->display_value && $value->display_value !== $value->value)
                                <span class="text-gray-400">({{ $value->display_value }})</span>
                            @endif
                            <form action="{{ route('admin.variant-attributes.values.destroy', [$attribute, $value]) }}" 
                                  method="POST" class="inline opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 ml-1" 
                                        onclick="return confirm('Delete this value?')">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Add Value Form -->
                <form action="{{ route('admin.variant-attributes.values.store', $attribute) }}" method="POST">
                    @csrf
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-600 mb-1">New Value</label>
                            <input type="text" name="value" required placeholder="e.g., XL, Red, Cotton"
                                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-600 mb-1">Display Name</label>
                            <input type="text" name="display_value" placeholder="Optional"
                                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        @if($attribute->type === 'color')
                        <div class="w-24">
                            <label class="block text-xs text-gray-600 mb-1">Color</label>
                            <input type="color" name="color_code" value="#000000"
                                   class="w-full h-8 border border-gray-300 rounded cursor-pointer">
                        </div>
                        @endif
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-plus mr-1"></i> Add
                        </button>
                    </div>
                </form>
                
                <!-- Delete Attribute -->
                <div class="mt-4 pt-4 border-t">
                    <form action="{{ route('admin.variant-attributes.destroy', $attribute) }}" method="POST" 
                          onsubmit="return confirm('Delete this attribute and all its values?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                            <i class="fas fa-trash mr-1"></i> Delete Attribute
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-8 text-center text-gray-500">
            <i class="fas fa-tags text-4xl mb-2"></i>
            <p>No attributes defined yet.</p>
            <p class="text-sm">Add attributes like Size, Color, Material to use with product variants.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
