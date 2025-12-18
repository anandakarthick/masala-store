<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $product->load('variants');
        
        // Get all active attributes with their values
        $attributes = VariantAttribute::active()->with('activeValues')->get();
        
        // Get unique values used in this product's variants
        $usedSizes = $product->variants->pluck('size')->filter()->unique()->values();
        $usedColors = $product->variants->pluck('color')->filter()->unique()->values();
        
        return view('admin.products.variants', compact('product', 'attributes', 'usedSizes', 'usedColors'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|unique:product_variants,sku',
            'weight' => 'nullable|numeric|min:0',
            'unit' => 'required|in:g,kg,ml,L,piece,pair,set',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            // Clothing attributes
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'color_code' => 'nullable|string|max:10',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'style' => 'nullable|string|max:100',
            'pattern' => 'nullable|string|max:100',
            'fit' => 'nullable|string|max:50',
            'sleeve_type' => 'nullable|string|max:50',
            'neck_type' => 'nullable|string|max:50',
            'occasion' => 'nullable|string|max:100',
            'variant_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['product_id'] = $product->id;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $product->variants()->count();

        // Generate SKU if not provided
        if (empty($validated['sku'])) {
            $skuParts = [$product->sku];
            if (!empty($validated['size'])) $skuParts[] = $validated['size'];
            if (!empty($validated['color'])) $skuParts[] = substr($validated['color'], 0, 3);
            $validated['sku'] = strtoupper(implode('-', $skuParts));
            
            // Ensure unique SKU
            $counter = 1;
            $baseSku = $validated['sku'];
            while (ProductVariant::where('sku', $validated['sku'])->exists()) {
                $validated['sku'] = $baseSku . '-' . $counter++;
            }
        }

        // Handle variant image upload
        if ($request->hasFile('variant_image')) {
            $validated['variant_image'] = $request->file('variant_image')->store('products/variants', 'public');
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
            'unit' => 'required|in:g,kg,ml,L,piece,pair,set',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            // Clothing attributes
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'color_code' => 'nullable|string|max:10',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'style' => 'nullable|string|max:100',
            'pattern' => 'nullable|string|max:100',
            'fit' => 'nullable|string|max:50',
            'sleeve_type' => 'nullable|string|max:50',
            'neck_type' => 'nullable|string|max:50',
            'occasion' => 'nullable|string|max:100',
            'variant_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default');

        // Handle variant image upload
        if ($request->hasFile('variant_image')) {
            // Delete old image
            if ($variant->variant_image) {
                Storage::disk('public')->delete($variant->variant_image);
            }
            $validated['variant_image'] = $request->file('variant_image')->store('products/variants', 'public');
        }

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
        
        // Delete variant image
        if ($variant->variant_image) {
            Storage::disk('public')->delete($variant->variant_image);
        }
        
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

    /**
     * Bulk create variants (for size/color combinations)
     */
    public function bulkCreate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:50',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:50',
            'color_codes' => 'nullable|array',
            'base_price' => 'required|numeric|min:0',
            'base_stock' => 'required|integer|min:0',
            'unit' => 'required|in:g,kg,ml,L,piece,pair,set',
        ]);

        $sizes = $validated['sizes'] ?? [''];
        $colors = $validated['colors'] ?? [''];
        $colorCodes = $validated['color_codes'] ?? [];
        
        $created = 0;
        $existingCount = $product->variants()->count();

        foreach ($sizes as $size) {
            foreach ($colors as $index => $color) {
                // Skip if this combination already exists
                $exists = $product->variants()
                    ->where('size', $size ?: null)
                    ->where('color', $color ?: null)
                    ->exists();
                    
                if ($exists) continue;

                $nameParts = [];
                if ($size) $nameParts[] = $size;
                if ($color) $nameParts[] = $color;
                $name = implode(' / ', $nameParts) ?: 'Default';

                $skuParts = [$product->sku];
                if ($size) $skuParts[] = $size;
                if ($color) $skuParts[] = substr($color, 0, 3);
                $sku = strtoupper(implode('-', $skuParts));
                
                // Ensure unique SKU
                $counter = 1;
                $baseSku = $sku;
                while (ProductVariant::where('sku', $sku)->exists()) {
                    $sku = $baseSku . '-' . $counter++;
                }

                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $name,
                    'sku' => $sku,
                    'size' => $size ?: null,
                    'color' => $color ?: null,
                    'color_code' => $colorCodes[$index] ?? null,
                    'unit' => $validated['unit'],
                    'price' => $validated['base_price'],
                    'stock_quantity' => $validated['base_stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'is_default' => ($existingCount + $created) === 0,
                    'sort_order' => $existingCount + $created,
                ]);
                
                $created++;
            }
        }

        if ($created > 0) {
            $product->update(['has_variants' => true]);
        }

        return back()->with('success', "{$created} variant(s) created successfully.");
    }

    /**
     * Get attribute values for AJAX
     */
    public function getAttributeValues(Request $request)
    {
        $attribute = VariantAttribute::where('code', $request->code)->first();
        
        if (!$attribute) {
            return response()->json(['values' => []]);
        }

        $values = $attribute->activeValues->map(function ($value) {
            return [
                'value' => $value->value,
                'display' => $value->display_name,
                'color_code' => $value->color_code,
            ];
        });

        return response()->json(['values' => $values]);
    }
}
