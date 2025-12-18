@extends('layouts.admin')
@section('title', 'Banners')
@section('page_title', 'Banner Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Banner Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Add New Banner</h3>
            <form action="{{ route('admin.settings.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <input type="text" name="subtitle"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image *</label>
                        <input type="file" name="image" accept="image/*" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                        <input type="url" name="link"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" name="button_text" placeholder="Shop Now"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position *</label>
                        <select name="position" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="home_slider">Home Slider</option>
                            <option value="home_banner">Home Banner</option>
                            <option value="category_banner">Category Banner</option>
                            <option value="popup">Popup</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked class="text-orange-600 focus:ring-orange-500 rounded">
                            <span class="ml-2 text-sm">Active</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg">
                        Add Banner
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Banners List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">All Banners</h3>
            </div>
            <div class="p-6">
                @if($banners->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($banners as $banner)
                            <div class="border rounded-lg overflow-hidden">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-32 object-cover">
                                <div class="p-4">
                                    <h4 class="font-semibold">{{ $banner->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $banner->subtitle }}</p>
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $banner->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                            {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <form action="{{ route('admin.settings.banners.destroy', $banner) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Delete this banner?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p>No banners added yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
