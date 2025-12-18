<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::getCart();
        $cart->load('items.product.primaryImage');

        return view('frontend.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->is_active || $product->isOutOfStock()) {
            return back()->with('error', 'This product is not available.');
        }

        $quantity = $validated['quantity'] ?? 1;

        if ($quantity > $product->stock_quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cart = Cart::getCart();
        $cart->addItem($product, $quantity);

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
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($validated['quantity'] > $product->stock_quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cart = Cart::getCart();
        $cart->updateItemQuantity($validated['product_id'], $validated['quantity']);

        if ($request->ajax()) {
            $cart = $cart->fresh()->load('items.product');
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
        ]);

        $cart = Cart::getCart();
        $cart->removeItem($validated['product_id']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart.',
                'cart_count' => $cart->fresh()->total_items,
            ]);
        }

        return back()->with('success', 'Product removed from cart.');
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
