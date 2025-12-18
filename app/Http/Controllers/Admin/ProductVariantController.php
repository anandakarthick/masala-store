<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $product->load('variants');
        return view('admin.products.variants', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|unique:product_variants,sku',
            'weight' => 'nullable|numeric|min:0',
            'unit' => 'required|in:g,kg,ml,L,piece',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['product_id'] = $product->id;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $product->variants()->count();

        // Generate SKU if not provided
        if (empty($validated['sku'])) {
            $validated['sku'] = $product->sku . '-' . strtoupper(str_replace(' ', '', $validated['name']));
        }

        // If this is set as default, unset other defaults
        if ($validated['is_default']) {
            $product->variants()->update(['is_default' => false]);
        }

        // If this is the first variant, make it default
        if ($product->variants()->count() === 0) {
            $validated['is_default'] = true;
        }

        ProductVariant::create($validated);

        // Mark product as having variants
        $product->update(['has_variants' => true]);

        return back()->with('success', 'Variant added successfully.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|unique:product_variants,sku,' . $variant->id,
            'weight' => 'nullable|numeric|min:0',
            'unit' => 'required|in:g,kg,ml,L,piece',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default');

        // If this is set as default, unset other defaults
        if ($validated['is_default']) {
            $product->variants()->where('id', '!=', $variant->id)->update(['is_default' => false]);
        }

        $variant->update($validated);

        return back()->with('success', 'Variant updated successfully.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $wasDefault = $variant->is_default;
        $variant->delete();

        // If we deleted the default, make another one default
        if ($wasDefault && $product->variants()->count() > 0) {
            $product->variants()->first()->update(['is_default' => true]);
        }

        // If no variants left, mark product as not having variants
        if ($product->variants()->count() === 0) {
            $product->update(['has_variants' => false]);
        }

        return back()->with('success', 'Variant deleted successfully.');
    }
}
