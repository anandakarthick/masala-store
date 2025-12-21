<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\CustomComboCart;
use App\Models\CustomComboSetting;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomComboController extends Controller
{
    /**
     * Display available combo options
     */
    public function index()
    {
        $comboSettings = CustomComboSetting::active()
            ->orderBy('sort_order')
            ->get();

        $categories = Category::active()
            ->whereNull('parent_id')
            ->withCount('activeProducts')
            ->get();

        return view('frontend.combo.index', compact('comboSettings', 'categories'));
    }

    /**
     * Show the combo builder page
     */
    public function builder(CustomComboSetting $combo)
    {
        if (!$combo->is_active) {
            abort(404);
        }

        $cart = Cart::getCart();
        
        // Get or create combo cart for this setting
        $comboCart = $cart->customCombos()
            ->where('combo_setting_id', $combo->id)
            ->whereHas('items', null, '<', $combo->min_products) // Only get incomplete combos
            ->first();

        // Get eligible products
        $products = $combo->getEligibleProducts();

        // Group by category
        $categories = $products->groupBy('category_id');
        $categoryNames = Category::whereIn('id', $products->pluck('category_id')->unique())
            ->pluck('name', 'id');

        return view('frontend.combo.builder', compact('combo', 'products', 'comboCart', 'categories', 'categoryNames'));
    }

    /**
     * Start a new combo
     */
    public function startCombo(Request $request, CustomComboSetting $combo)
    {
        if (!$combo->is_active) {
            return response()->json(['success' => false, 'message' => 'Combo not available'], 404);
        }

        $cart = Cart::getCart();

        // Create new combo cart
        $comboCart = $cart->customCombos()->create([
            'combo_setting_id' => $combo->id,
            'combo_name' => $combo->name,
            'quantity' => 1,
            'calculated_price' => 0,
            'discount_amount' => 0,
            'final_price' => 0,
        ]);

        return response()->json([
            'success' => true,
            'combo_cart_id' => $comboCart->id,
            'message' => 'Combo started! Add products to your combo.',
        ]);
    }

    /**
     * Add product to combo
     */
    public function addProduct(Request $request)
    {
        $request->validate([
            'combo_cart_id' => 'required|exists:custom_combo_cart,id',
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'integer|min:1|max:10',
        ]);

        $cart = Cart::getCart();
        $comboCart = $cart->customCombos()->find($request->combo_cart_id);

        if (!$comboCart) {
            return response()->json(['success' => false, 'message' => 'Combo not found'], 404);
        }

        $setting = $comboCart->comboSetting;
        $product = Product::find($request->product_id);

        // Check if product is eligible
        if (!$setting->isProductEligible($product)) {
            return response()->json(['success' => false, 'message' => 'Product not eligible for this combo'], 400);
        }

        // Check max capacity
        $quantity = $request->quantity ?? 1;
        if ($comboCart->total_items_count + $quantity > $setting->max_products) {
            return response()->json([
                'success' => false, 
                'message' => 'Maximum ' . $setting->max_products . ' products allowed in this combo'
            ], 400);
        }

        // Add product
        $comboCart->addProduct($product, $quantity, $request->variant_id);
        $comboCart->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Product added to combo',
            'combo' => $this->getComboData($comboCart),
        ]);
    }

    /**
     * Remove product from combo
     */
    public function removeProduct(Request $request)
    {
        $request->validate([
            'combo_cart_id' => 'required|exists:custom_combo_cart,id',
            'item_id' => 'required|exists:custom_combo_cart_items,id',
        ]);

        $cart = Cart::getCart();
        $comboCart = $cart->customCombos()->find($request->combo_cart_id);

        if (!$comboCart) {
            return response()->json(['success' => false, 'message' => 'Combo not found'], 404);
        }

        $comboCart->removeProduct($request->item_id);
        $comboCart->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from combo',
            'combo' => $this->getComboData($comboCart),
        ]);
    }

    /**
     * Update product quantity in combo
     */
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'combo_cart_id' => 'required|exists:custom_combo_cart,id',
            'item_id' => 'required|exists:custom_combo_cart_items,id',
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $cart = Cart::getCart();
        $comboCart = $cart->customCombos()->find($request->combo_cart_id);

        if (!$comboCart) {
            return response()->json(['success' => false, 'message' => 'Combo not found'], 404);
        }

        // Check max capacity before increasing
        if ($request->quantity > 0) {
            $currentItem = $comboCart->items()->find($request->item_id);
            $difference = $request->quantity - ($currentItem->quantity ?? 0);
            
            if ($difference > 0 && $comboCart->total_items_count + $difference > $comboCart->comboSetting->max_products) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum ' . $comboCart->comboSetting->max_products . ' products allowed'
                ], 400);
            }
        }

        $comboCart->updateProductQuantity($request->item_id, $request->quantity);
        $comboCart->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated',
            'combo' => $this->getComboData($comboCart),
        ]);
    }

    /**
     * Add combo to cart (finalize)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'combo_cart_id' => 'required|exists:custom_combo_cart,id',
        ]);

        $cart = Cart::getCart();
        $comboCart = $cart->customCombos()->find($request->combo_cart_id);

        if (!$comboCart) {
            return response()->json(['success' => false, 'message' => 'Combo not found'], 404);
        }

        // Check minimum requirement
        if (!$comboCart->meetsMinimumRequirement()) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least ' . $comboCart->comboSetting->min_products . ' products to this combo'
            ], 400);
        }

        // Combo is already in cart, just return success
        return response()->json([
            'success' => true,
            'message' => 'Combo added to cart!',
            'redirect' => route('cart.index'),
            'cart_count' => $cart->total_items,
        ]);
    }

    /**
     * Get combo status
     */
    public function getStatus(Request $request)
    {
        $request->validate([
            'combo_cart_id' => 'required|exists:custom_combo_cart,id',
        ]);

        $cart = Cart::getCart();
        $comboCart = $cart->customCombos()->with(['items.product.primaryImage', 'items.variant'])->find($request->combo_cart_id);

        if (!$comboCart) {
            return response()->json(['success' => false, 'message' => 'Combo not found'], 404);
        }

        return response()->json([
            'success' => true,
            'combo' => $this->getComboData($comboCart),
        ]);
    }

    /**
     * Delete incomplete combo
     */
    public function deleteCombo(Request $request)
    {
        $request->validate([
            'combo_cart_id' => 'required|exists:custom_combo_cart,id',
        ]);

        $cart = Cart::getCart();
        $deleted = $cart->removeCombo($request->combo_cart_id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Combo deleted' : 'Could not delete combo',
        ]);
    }

    /**
     * Get combo data for JSON response
     */
    private function getComboData(CustomComboCart $comboCart): array
    {
        $comboCart->load(['items.product.primaryImage', 'items.variant', 'comboSetting']);

        return [
            'id' => $comboCart->id,
            'name' => $comboCart->combo_name,
            'total_items' => $comboCart->total_items_count,
            'min_items' => $comboCart->comboSetting->min_products,
            'max_items' => $comboCart->comboSetting->max_products,
            'slots_needed' => $comboCart->slots_needed,
            'remaining_slots' => $comboCart->getRemainingSlots(),
            'meets_minimum' => $comboCart->meetsMinimumRequirement(),
            'is_at_max' => $comboCart->isAtMaxCapacity(),
            'original_price' => (float) $comboCart->calculated_price,
            'discount_amount' => (float) $comboCart->discount_amount,
            'final_price' => (float) $comboCart->final_price,
            'discount_display' => $comboCart->comboSetting->discount_display,
            'items' => $comboCart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total' => (float) $item->total,
                    'image' => $item->image_url,
                ];
            }),
        ];
    }
}
