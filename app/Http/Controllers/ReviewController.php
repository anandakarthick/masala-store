<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Show the review form for an order (for logged-in users)
     */
    public function create(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to review this order.');
        }

        // Ensure order is delivered
        if (!$order->canBeReviewed()) {
            return redirect()->route('account.orders.show', $order)
                ->with('error', 'You can only review delivered orders.');
        }

        $order->load(['items.product.primaryImage', 'reviews']);
        
        // Get already reviewed item IDs
        $reviewedItemIds = $order->reviews()->pluck('order_item_id')->toArray();

        return view('frontend.account.review', compact('order', 'reviewedItemIds'));
    }

    /**
     * Show review form via token (for guest checkout orders)
     */
    public function createByToken(string $token)
    {
        $order = Order::where('review_token', $token)->firstOrFail();

        if (!$order->canBeReviewed()) {
            return redirect()->route('home')
                ->with('error', 'This order cannot be reviewed.');
        }

        $order->load(['items.product.primaryImage', 'reviews']);
        
        // Get already reviewed item IDs
        $reviewedItemIds = $order->reviews()->pluck('order_item_id')->toArray();

        return view('frontend.review.create', compact('order', 'reviewedItemIds', 'token'));
    }

    /**
     * Store a review (for logged-in users)
     */
    public function store(Request $request, Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to review this order.');
        }

        return $this->processReview($request, $order, auth()->id());
    }

    /**
     * Store a review via token (for guest orders)
     */
    public function storeByToken(Request $request, string $token)
    {
        $order = Order::where('review_token', $token)->firstOrFail();
        
        // Guest orders might not have a user_id, so we need to handle this
        $userId = $order->user_id;
        
        if (!$userId) {
            return redirect()->back()
                ->with('error', 'Only registered users can submit reviews. Please login first.');
        }

        return $this->processReview($request, $order, $userId);
    }

    /**
     * Process and store the review
     */
    protected function processReview(Request $request, Order $order, int $userId)
    {
        if (!$order->canBeReviewed()) {
            return redirect()->back()
                ->with('error', 'This order cannot be reviewed.');
        }

        $validated = $request->validate([
            'reviews' => 'required|array|min:1',
            'reviews.*.order_item_id' => 'required|exists:order_items,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.title' => 'nullable|string|max:255',
            'reviews.*.comment' => 'nullable|string|max:2000',
            'reviews.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $reviewsCreated = 0;

        foreach ($validated['reviews'] as $reviewData) {
            $orderItem = OrderItem::find($reviewData['order_item_id']);

            // Ensure this item belongs to this order
            if ($orderItem->order_id !== $order->id) {
                continue;
            }

            // Check if already reviewed
            $existingReview = Review::where('order_id', $order->id)
                ->where('order_item_id', $orderItem->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingReview) {
                continue; // Skip already reviewed items
            }

            // Handle image uploads
            $images = [];
            if ($request->hasFile("reviews.{$orderItem->id}.images")) {
                foreach ($request->file("reviews.{$orderItem->id}.images") as $image) {
                    $path = $image->store('reviews', 'public');
                    $images[] = $path;
                }
            }

            Review::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'product_id' => $orderItem->product_id,
                'order_item_id' => $orderItem->id,
                'rating' => $reviewData['rating'],
                'title' => $reviewData['title'] ?? null,
                'comment' => $reviewData['comment'] ?? null,
                'images' => !empty($images) ? $images : null,
                'is_verified_purchase' => true,
                'is_approved' => false, // Admin will approve
            ]);

            $reviewsCreated++;
        }

        if ($reviewsCreated > 0) {
            return redirect()->route('account.orders.show', $order)
                ->with('success', "Thank you! {$reviewsCreated} review(s) submitted successfully. They will be visible after approval.");
        }

        return redirect()->back()
            ->with('info', 'No new reviews were submitted.');
    }

    /**
     * Show single review (public)
     */
    public function show(Review $review)
    {
        if (!$review->is_approved) {
            abort(404);
        }

        return view('frontend.review.show', compact('review'));
    }
}
