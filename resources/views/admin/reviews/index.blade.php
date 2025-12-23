@extends('layouts.admin')

@section('title', 'Reviews')
@section('page_title', 'Customer Reviews')

@section('content')
<div class="mb-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Reviews</p>
                    <p class="text-2xl font-bold">{{ $totalCount }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Approval</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Approved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $approvedCount }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search reviews..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div>
                <select name="rating" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'rating']))
                <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($reviews->count() > 0)
            <form id="bulkForm" action="" method="POST">
                @csrf
                <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                        <label for="selectAll" class="text-sm text-gray-600">Select All</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="bulkAction('approve')" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700 disabled:opacity-50" disabled id="bulkApproveBtn">
                            <i class="fas fa-check mr-1"></i> Approve Selected
                        </button>
                        <button type="button" onclick="bulkAction('delete')" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700 disabled:opacity-50" disabled id="bulkDeleteBtn">
                            <i class="fas fa-trash mr-1"></i> Delete Selected
                        </button>
                    </div>
                </div>

                <div class="divide-y">
                    @foreach($reviews as $review)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start gap-4">
                                <input type="checkbox" name="review_ids[]" value="{{ $review->id }}" class="review-checkbox mt-1 rounded border-gray-300">
                                
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="flex text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                
                                                @if($review->is_approved)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Approved</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">Pending</span>
                                                @endif
                                                
                                                @if($review->is_featured)
                                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full">
                                                        <i class="fas fa-star mr-1"></i>Featured
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($review->title)
                                                <h4 class="font-semibold text-gray-800">{{ $review->title }}</h4>
                                            @endif
                                        </div>
                                        
                                        <div class="text-sm text-gray-500">
                                            {{ $review->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    
                                    @if($review->comment)
                                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($review->comment, 200) }}</p>
                                    @endif
                                    
                                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-3">
                                        <span><i class="fas fa-user mr-1"></i>{{ $review->user->name ?? 'Unknown' }}</span>
                                        <span><i class="fas fa-box mr-1"></i>{{ Str::limit($review->product->name ?? 'Unknown', 30) }}</span>
                                        <span><i class="fas fa-receipt mr-1"></i>#{{ $review->order->order_number ?? 'N/A' }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.reviews.show', $review) }}" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        
                                        @if(!$review->is_approved)
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded hover:bg-green-200">
                                                    <i class="fas fa-check mr-1"></i> Approve
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm rounded hover:bg-yellow-200">
                                                    <i class="fas fa-times mr-1"></i> Unapprove
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.reviews.toggle-featured', $review) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 {{ $review->is_featured ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-700' }} text-sm rounded hover:opacity-80">
                                                <i class="fas fa-star mr-1"></i> {{ $review->is_featured ? 'Unfeature' : 'Feature' }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded hover:bg-red-200">
                                                <i class="fas fa-trash mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
            
            <div class="p-4 border-t">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-star text-4xl mb-4 text-gray-300"></i>
                <p>No reviews found.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    function updateBulkButtons() {
        const checked = document.querySelectorAll('.review-checkbox:checked');
        const hasChecked = checked.length > 0;
        bulkApproveBtn.disabled = !hasChecked;
        bulkDeleteBtn.disabled = !hasChecked;
    }
    
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkButtons();
    });
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButtons);
    });
});

function bulkAction(action) {
    const form = document.getElementById('bulkForm');
    if (action === 'approve') {
        form.action = '{{ route("admin.reviews.bulk-approve") }}';
    } else if (action === 'delete') {
        if (!confirm('Are you sure you want to delete the selected reviews?')) return;
        form.action = '{{ route("admin.reviews.bulk-delete") }}';
    }
    form.submit();
}
</script>
@endpush
@endsection
