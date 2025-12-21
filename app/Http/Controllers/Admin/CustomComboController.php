<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomComboSetting;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomComboController extends Controller
{
    /**
     * Display a listing of combo settings
     */
    public function index()
    {
        $combos = CustomComboSetting::orderBy('sort_order')->paginate(20);
        return view('admin.combos.index', compact('combos'));
    }

    /**
     * Show the form for creating a new combo
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        return view('admin.combos.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created combo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'min_products' => 'required|integer|min:1|max:50',
            'max_products' => 'required|integer|min:1|max:50|gte:min_products',
            'discount_type' => 'required|in:percentage,fixed,per_item',
            'discount_value' => 'required|numeric|min:0',
            'combo_price' => 'nullable|numeric|min:0',
            'allowed_categories' => 'nullable|array',
            'allowed_categories.*' => 'exists:categories,id',
            'allowed_products' => 'nullable|array',
            'allowed_products.*' => 'exists:products,id',
            'excluded_products' => 'nullable|array',
            'excluded_products.*' => 'exists:products,id',
            'allow_same_product' => 'boolean',
            'allow_variants' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['allow_same_product'] = $request->boolean('allow_same_product');
        $validated['allow_variants'] = $request->boolean('allow_variants', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('combos', 'public');
        }

        // Clean up arrays
        $validated['allowed_categories'] = array_filter($validated['allowed_categories'] ?? []);
        $validated['allowed_products'] = array_filter($validated['allowed_products'] ?? []);
        $validated['excluded_products'] = array_filter($validated['excluded_products'] ?? []);

        CustomComboSetting::create($validated);

        return redirect()->route('admin.combos.index')->with('success', 'Combo created successfully!');
    }

    /**
     * Show the form for editing a combo
     */
    public function edit(CustomComboSetting $combo)
    {
        $categories = Category::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        return view('admin.combos.edit', compact('combo', 'categories', 'products'));
    }

    /**
     * Update the specified combo
     */
    public function update(Request $request, CustomComboSetting $combo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'min_products' => 'required|integer|min:1|max:50',
            'max_products' => 'required|integer|min:1|max:50|gte:min_products',
            'discount_type' => 'required|in:percentage,fixed,per_item',
            'discount_value' => 'required|numeric|min:0',
            'combo_price' => 'nullable|numeric|min:0',
            'allowed_categories' => 'nullable|array',
            'allowed_categories.*' => 'exists:categories,id',
            'allowed_products' => 'nullable|array',
            'allowed_products.*' => 'exists:products,id',
            'excluded_products' => 'nullable|array',
            'excluded_products.*' => 'exists:products,id',
            'allow_same_product' => 'boolean',
            'allow_variants' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['allow_same_product'] = $request->boolean('allow_same_product');
        $validated['allow_variants'] = $request->boolean('allow_variants', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($combo->image) {
                Storage::disk('public')->delete($combo->image);
            }
            $validated['image'] = $request->file('image')->store('combos', 'public');
        }

        // Clean up arrays
        $validated['allowed_categories'] = array_filter($validated['allowed_categories'] ?? []);
        $validated['allowed_products'] = array_filter($validated['allowed_products'] ?? []);
        $validated['excluded_products'] = array_filter($validated['excluded_products'] ?? []);

        $combo->update($validated);

        return redirect()->route('admin.combos.index')->with('success', 'Combo updated successfully!');
    }

    /**
     * Remove the specified combo
     */
    public function destroy(CustomComboSetting $combo)
    {
        if ($combo->image) {
            Storage::disk('public')->delete($combo->image);
        }

        $combo->delete();

        return redirect()->route('admin.combos.index')->with('success', 'Combo deleted successfully!');
    }

    /**
     * Toggle combo status
     */
    public function toggleStatus(CustomComboSetting $combo)
    {
        $combo->update(['is_active' => !$combo->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $combo->is_active,
            'message' => $combo->is_active ? 'Combo activated' : 'Combo deactivated',
        ]);
    }
}
