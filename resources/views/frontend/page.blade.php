@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description ?? Str::limit(strip_tags($page->content), 160))

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('home') }}" class="hover:text-green-600">Home</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800 font-medium">{{ $page->title }}</li>
        </ol>
    </nav>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ $page->title }}</h1>
            
            <div class="prose prose-green max-w-none text-gray-600">
                {!! $page->content !!}
            </div>
            
            <div class="mt-8 pt-6 border-t text-sm text-gray-500">
                <p>Last updated: {{ $page->updated_at->format('F d, Y') }}</p>
            </div>
        </div>
    </div>
</div>

<style>
.prose h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #1f2937; }
.prose h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #374151; }
.prose p { margin-bottom: 1rem; line-height: 1.7; }
.prose ul, .prose ol { margin-bottom: 1rem; padding-left: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }
.prose ul { list-style-type: disc; }
.prose ol { list-style-type: decimal; }
.prose strong { font-weight: 600; color: #1f2937; }
.prose a { color: #16a34a; text-decoration: underline; }
.prose a:hover { color: #15803d; }
</style>
@endsection
