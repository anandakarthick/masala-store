<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get wishlist
     */
    public function index(Request $request)
    {
        $wishlists = Wishlist::where('user_id', $request->user()->id)
            ->with('product.images', 'product.category', 'product.activeVariants')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlists->map(function ($wishlist) {
                $product = $wishlist->product;
                return [
                    'id' => $wishlist->id,
                    'product_id' => $product->id,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => (float) $product->price,
                        'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
                        'effective_price' => (float) $product->effective_price,
                        'discount_percentage' => $product->discount_percentage,
                        'image' => $product->primary_image_url,
                        'in_stock' => !$product->isOutOfStock(),
                        'has_variants' => $product->has_variants,
                    ],
                    'added_at' => $wishlist->created_at->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * Add to wishlist
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist.',
            ], 400);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist.',
        ]);
    }

    /**
     * Remove from wishlist
     */
    public function remove(Request $request, $productId)
    {
        $deleted = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Product not in wishlist.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist.',
        ]);
    }

    /**
     * Toggle wishlist
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist.',
                'data' => ['in_wishlist' => false],
            ]);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist.',
            'data' => ['in_wishlist' => true],
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request, $productId)
    {
        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => ['in_wishlist' => $exists],
        ]);
    }
}
