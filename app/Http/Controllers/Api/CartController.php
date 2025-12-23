<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get cart
     */
    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        $cart->load('items.product.images', 'items.variant', 'customCombos.items.product', 'customCombos.comboSetting');

        return response()->json([
            'success' => true,
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available.',
            ], 400);
        }

        // Check variant requirement
        if ($product->has_variants && empty($validated['variant_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a variant.',
            ], 400);
        }

        // Check stock
        $stockQty = $product->stock_quantity;
        if (!empty($validated['variant_id'])) {
            $variant = ProductVariant::find($validated['variant_id']);
            if (!$variant || !$variant->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected variant is not available.',
                ], 400);
            }
            $stockQty = $variant->stock_quantity;
        }

        if ($stockQty < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.',
            ], 400);
        }

        $cart = $this->getCart($request);
        $cart->addItem($product, $validated['quantity'], $validated['variant_id'] ?? null);
        $cart->load('items.product.images', 'items.variant', 'customCombos.items.product', 'customCombos.comboSetting');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $this->getCart($request);

        // Check stock
        if ($validated['quantity'] > 0) {
            $product = Product::find($validated['product_id']);
            $stockQty = $product->stock_quantity;
            
            if (!empty($validated['variant_id'])) {
                $variant = ProductVariant::find($validated['variant_id']);
                $stockQty = $variant ? $variant->stock_quantity : 0;
            }

            if ($stockQty < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available.',
                ], 400);
            }
        }

        $cart->updateItemQuantity(
            $validated['product_id'],
            $validated['quantity'],
            $validated['variant_id'] ?? null
        );

        $cart->load('items.product.images', 'items.variant', 'customCombos.items.product', 'customCombos.comboSetting');

        return response()->json([
            'success' => true,
            'message' => $validated['quantity'] > 0 ? 'Cart updated' : 'Item removed',
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $cart = $this->getCart($request);
        $cart->removeItem($validated['product_id'], $validated['variant_id'] ?? null);
        $cart->load('items.product.images', 'items.variant', 'customCombos.items.product', 'customCombos.comboSetting');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request)
    {
        $cart = $this->getCart($request);
        $cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * Get cart helper
     */
    private function getCart(Request $request): Cart
    {
        if ($request->user()) {
            return Cart::firstOrCreate(['user_id' => $request->user()->id]);
        }

        $sessionId = $request->header('X-Session-Id', session()->getId());
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Format cart for response
     */
    private function formatCart(Cart $cart): array
    {
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'image' => $item->product->primary_image_url,
                ],
                'variant' => $item->variant ? [
                    'id' => $item->variant->id,
                    'name' => $item->variant->name,
                    'display_name' => $item->variant->display_name,
                ] : null,
                'item_name' => $item->item_name,
                'unit_price' => (float) $item->unit_price,
                'total' => (float) $item->total,
                'stock_quantity' => $item->stock_quantity,
                'in_stock' => $item->stock_quantity >= $item->quantity,
            ];
        });

        return [
            'id' => $cart->id,
            'items' => $items,
            'item_count' => $cart->total_items,
            'total_quantity' => $cart->total_quantity,
            'subtotal' => (float) $cart->subtotal,
            'gst_amount' => (float) $cart->gst_amount,
        ];
    }
}
