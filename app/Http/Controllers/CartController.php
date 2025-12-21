<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::getCart();
        $cart->load([
            'items.product.primaryImage', 
            'items.variant',
            'customCombos.items.product.primaryImage',
            'customCombos.items.variant',
            'customCombos.comboSetting'
        ]);

        return view('frontend.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $variantId = $validated['variant_id'] ?? null;
        $quantity = $validated['quantity'] ?? 1;

        // Check if product requires variant selection
        if ($product->has_variants && !$variantId) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a size/variant.',
                ], 400);
            }
            return back()->with('error', 'Please select a size/variant.');
        }

        // Validate variant belongs to product
        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->first();

            if (!$variant) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid variant selected.',
                    ], 400);
                }
                return back()->with('error', 'Invalid variant selected.');
            }

            if ($variant->isOutOfStock()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This variant is out of stock.',
                    ], 400);
                }
                return back()->with('error', 'This variant is out of stock.');
            }

            if ($quantity > $variant->stock_quantity) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available.',
                    ], 400);
                }
                return back()->with('error', 'Not enough stock available.');
            }
        } else {
            if (!$product->is_active || $product->isOutOfStock()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This product is not available.',
                    ], 400);
                }
                return back()->with('error', 'This product is not available.');
            }

            if ($quantity > $product->stock_quantity) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available.',
                    ], 400);
                }
                return back()->with('error', 'Not enough stock available.');
            }
        }

        $cart = Cart::getCart();
        $cart->addItem($product, $quantity, $variantId);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart.',
                'cart_count' => $cart->fresh()->total_items,
            ]);
        }

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $variantId = $validated['variant_id'] ?? null;

        // Check stock
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant && $validated['quantity'] > $variant->stock_quantity) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available.',
                    ], 400);
                }
                return back()->with('error', 'Not enough stock available.');
            }
        } else {
            if ($validated['quantity'] > $product->stock_quantity) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available.',
                    ], 400);
                }
                return back()->with('error', 'Not enough stock available.');
            }
        }

        $cart = Cart::getCart();
        $cart->updateItemQuantity($validated['product_id'], $validated['quantity'], $variantId);

        if ($request->ajax()) {
            $cart = $cart->fresh()->load('items.product', 'items.variant');
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'cart_count' => $cart->total_items,
                'subtotal' => number_format($cart->subtotal, 2),
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $cart = Cart::getCart();
        $cart->removeItem($validated['product_id'], $validated['variant_id'] ?? null);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart.',
                'cart_count' => $cart->fresh()->total_items,
            ]);
        }

        return back()->with('success', 'Product removed from cart.');
    }

    /**
     * Remove a custom combo from cart
     */
    public function removeCombo(Request $request)
    {
        $validated = $request->validate([
            'combo_id' => 'required|exists:custom_combo_carts,id',
        ]);

        $cart = Cart::getCart();
        $cart->removeCombo($validated['combo_id']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Combo removed from cart.',
                'cart_count' => $cart->fresh()->total_items,
            ]);
        }

        return back()->with('success', 'Combo removed from cart.');
    }

    /**
     * Update combo quantity in cart
     */
    public function updateCombo(Request $request)
    {
        $validated = $request->validate([
            'combo_id' => 'required|exists:custom_combo_carts,id',
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $cart = Cart::getCart();
        $cart->updateComboQuantity($validated['combo_id'], $validated['quantity']);

        if ($request->ajax()) {
            $cart = $cart->fresh()->load('customCombos.comboSetting');
            return response()->json([
                'success' => true,
                'message' => 'Combo updated.',
                'cart_count' => $cart->total_items,
                'subtotal' => number_format($cart->subtotal, 2),
            ]);
        }

        return back()->with('success', 'Combo updated.');
    }

    public function clear()
    {
        $cart = Cart::getCart();
        $cart->clear();

        return back()->with('success', 'Cart cleared.');
    }

    public function count()
    {
        $cart = Cart::getCart();
        return response()->json(['count' => $cart->total_items]);
    }
}
