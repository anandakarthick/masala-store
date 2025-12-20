@extends('layouts.admin')
@section('title', 'Banners')
@section('page_title', 'Banner Management')

@section('content')
<div class="mb-6 flex flex-wrap justify-between items-center gap-4">
    <div>
        <p class="text-gray-600 text-sm">Manage your website banners - home slider, promotional banners & popups</p>
    </div>
    <a href="{{ route('admin.banner-generator.index') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-5 py-2 rounded-lg text-sm flex items-center gap-2 shadow-lg">
        <i class="fas fa-magic"></i> Create with Banner Generator
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Banner Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                Add New Banner
            </h3>
            <form action="{{ route('admin.settings.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="e.g., Summer Sale 50% Off">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <input type="text" name="subtitle"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="e.g., Limited time offer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image *</label>
                        <input type="file" name="image" accept="image/*" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Recommended: 1920×600px for desktop, 800×400px for mobile</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                        <input type="url" name="link"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="https://yourstore.com/products">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" name="button_text" placeholder="Shop Now"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position *</label>
                        <select name="position" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="home_slider">🖼️ Home Slider (Main)</option>
                            <option value="home_banner">📢 Home Banner (Secondary)</option>
                            <option value="category_banner">📁 Category Banner</option>
                            <option value="popup">💬 Popup Banner</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
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
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="text-green-600 focus:ring-green-500 rounded">
                            <span class="ml-2 text-sm">Active (Show on website)</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Add Banner
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Banners List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-images text-green-600 mr-2"></i>
                    All Banners ({{ $banners->count() }})
                </h3>
            </div>
            <div class="p-6">
                @if($banners->count() > 0)
                    <!-- Group by Position -->
                    @php
                        $grouped = $banners->groupBy('position');
                        $positionLabels = [
                            'home_slider' => ['label' => 'Home Slider', 'icon' => 'fa-images', 'color' => 'purple'],
                            'home_banner' => ['label' => 'Home Banner', 'icon' => 'fa-bullhorn', 'color' => 'blue'],
                            'category_banner' => ['label' => 'Category Banner', 'icon' => 'fa-folder', 'color' => 'green'],
                            'popup' => ['label' => 'Popup Banner', 'icon' => 'fa-comment-alt', 'color' => 'orange'],
                        ];
                    @endphp

                    @foreach($positionLabels as $position => $info)
                        @if($grouped->has($position))
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-600 mb-3 flex items-center">
                                    <i class="fas {{ $info['icon'] }} text-{{ $info['color'] }}-500 mr-2"></i>
                                    {{ $info['label'] }} ({{ $grouped[$position]->count() }})
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($grouped[$position] as $banner)
                                        <div class="border rounded-lg overflow-hidden group hover:shadow-lg transition" x-data="{ editing: false }">
                                            <!-- Image -->
                                            <div class="relative h-36">
                                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                                
                                                <!-- Status Badge -->
                                                <div class="absolute top-2 left-2">
                                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $banner->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Sort Order -->
                                                <div class="absolute top-2 right-2 bg-white/90 text-gray-700 px-2 py-1 text-xs rounded-full font-medium">
                                                    #{{ $banner->sort_order }}
                                                </div>
                                                
                                                <!-- Title on Image -->
                                                <div class="absolute bottom-2 left-2 right-2">
                                                    <h5 class="font-semibold text-white text-sm truncate">{{ $banner->title }}</h5>
                                                    @if($banner->subtitle)
                                                        <p class="text-xs text-white/80 truncate">{{ $banner->subtitle }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="p-3 bg-gray-50 flex items-center justify-between">
                                                <div class="text-xs text-gray-500">
                                                    @if($banner->start_date || $banner->end_date)
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        {{ $banner->start_date?->format('M d') ?? 'Start' }} - {{ $banner->end_date?->format('M d') ?? 'End' }}
                                                    @else
                                                        <i class="fas fa-infinity mr-1"></i> Always
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button @click="editing = !editing" class="text-blue-600 hover:text-blue-800 text-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('admin.settings.banners.destroy', $banner) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Delete this banner?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-red-600 hover:text-red-800 text-sm" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <!-- Edit Form (Collapsible) -->
                                            <div x-show="editing" x-collapse class="border-t p-4 bg-white">
                                                <form action="{{ route('admin.settings.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="space-y-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                                                            <input type="text" name="title" value="{{ $banner->title }}" required
                                                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-green-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">Subtitle</label>
                                                            <input type="text" name="subtitle" value="{{ $banner->subtitle }}"
                                                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-green-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">New Image</label>
                                                            <input type="file" name="image" accept="image/*"
                                                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">Link URL</label>
                                                            <input type="url" name="link" value="{{ $banner->link }}"
                                                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-green-500">
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">Button Text</label>
                                                                <input type="text" name="button_text" value="{{ $banner->button_text }}"
                                                                       class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">Position</label>
                                                                <select name="position" class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                                                    <option value="home_slider" {{ $banner->position === 'home_slider' ? 'selected' : '' }}>Home Slider</option>
                                                                    <option value="home_banner" {{ $banner->position === 'home_banner' ? 'selected' : '' }}>Home Banner</option>
                                                                    <option value="category_banner" {{ $banner->position === 'category_banner' ? 'selected' : '' }}>Category Banner</option>
                                                                    <option value="popup" {{ $banner->position === 'popup' ? 'selected' : '' }}>Popup</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-3 gap-2">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">Sort</label>
                                                                <input type="number" name="sort_order" value="{{ $banner->sort_order }}"
                                                                       class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">Start</label>
                                                                <input type="date" name="start_date" value="{{ $banner->start_date?->format('Y-m-d') }}"
                                                                       class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">End</label>
                                                                <input type="date" name="end_date" value="{{ $banner->end_date?->format('Y-m-d') }}"
                                                                       class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="flex items-center text-sm">
                                                                <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}
                                                                       class="text-green-600 rounded">
                                                                <span class="ml-2">Active</span>
                                                            </label>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-1.5 rounded text-sm">
                                                                Save Changes
                                                            </button>
                                                            <button type="button" @click="editing = false" class="px-4 bg-gray-200 hover:bg-gray-300 text-gray-700 py-1.5 rounded text-sm">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-image text-3xl text-gray-400"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-600 mb-2">No Banners Yet</h4>
                        <p class="text-gray-500 text-sm mb-4">Create your first banner to display on your store</p>
                        <a href="{{ route('admin.banner-generator.index') }}" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg text-sm">
                            <i class="fas fa-magic"></i> Create with Banner Generator
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
