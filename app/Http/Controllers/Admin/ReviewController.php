<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'order']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->latest()->paginate(20);

        // Get counts for tabs
        $pendingCount = Review::where('is_approved', false)->count();
        $approvedCount = Review::where('is_approved', true)->count();
        $totalCount = Review::count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount', 'approvedCount', 'totalCount'));
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review)
    {
        $review->load(['user', 'product.primaryImage', 'order', 'orderItem']);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review.
     */
    public function approve(Review $review)
    {
        $review->approve();

        return back()->with('success', 'Review approved successfully.');
    }

    /**
     * Reject/Unapprove a review.
     */
    public function reject(Review $review)
    {
        $review->reject();

        return back()->with('success', 'Review rejected.');
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Review $review)
    {
        $review->toggleFeatured();

        $message = $review->is_featured ? 'Review marked as featured.' : 'Review removed from featured.';
        return back()->with('success', $message);
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        // Delete associated images
        if ($review->images) {
            foreach ($review->images as $image) {
                \Storage::disk('public')->delete($image);
            }
        }

        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    /**
     * Bulk approve reviews.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        Review::whereIn('id', $validated['review_ids'])->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return back()->with('success', count($validated['review_ids']) . ' reviews approved successfully.');
    }

    /**
     * Bulk delete reviews.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        $reviews = Review::whereIn('id', $validated['review_ids'])->get();

        foreach ($reviews as $review) {
            if ($review->images) {
                foreach ($review->images as $image) {
                    \Storage::disk('public')->delete($image);
                }
            }
            $review->delete();
        }

        return back()->with('success', count($validated['review_ids']) . ' reviews deleted successfully.');
    }
}
