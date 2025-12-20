@extends('layouts.admin')
@section('title', 'Edit Page')
@section('page_title', 'Edit Page')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-edit text-blue-500 mr-2"></i>
                    Edit: {{ $page->title }}
                </h2>
                <div class="flex items-center gap-3">
                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" 
                       class="text-gray-600 hover:text-gray-800 text-sm">
                        <i class="fas fa-eye mr-1"></i> View Page
                    </a>
                    <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Pages
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page Title *</label>
                        <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                /page/
                            </span>
                            <input type="text" name="slug" value="{{ old('slug', $page->slug) }}"
                                   class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Content *</label>
                    <textarea name="content" rows="15" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 font-mono text-sm">{{ old('content', $page->content) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">HTML tags are supported for formatting</p>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">SEO Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <input type="text" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Display Settings</h3>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $page->is_active ? 'checked' : '' }}
                                   class="w-5 h-5 text-green-600 focus:ring-green-500 rounded">
                            <span class="ml-2">
                                <span class="font-medium">Active</span>
                                <span class="text-sm text-gray-500 block">Page is visible on website</span>
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="show_in_footer" value="1" {{ $page->show_in_footer ? 'checked' : '' }}
                                   class="w-5 h-5 text-green-600 focus:ring-green-500 rounded">
                            <span class="ml-2">
                                <span class="font-medium">Show in Footer</span>
                                <span class="text-sm text-gray-500 block">Display link in website footer</span>
                            </span>
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order) }}"
                                   class="w-20 border border-gray-300 rounded-lg px-3 py-2">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this page?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash mr-1"></i> Delete Page
                    </button>
                </form>
                
                <div class="flex gap-3">
                    <a href="{{ route('admin.pages.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-save"></i> Update Page
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
