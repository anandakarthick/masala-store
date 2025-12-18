<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Http\Request;

class VariantAttributeController extends Controller
{
    /**
     * List all attributes
     */
    public function index()
    {
        $attributes = VariantAttribute::with('values')->orderBy('sort_order')->get();
        return view('admin.variant-attributes.index', compact('attributes'));
    }

    /**
     * Store new attribute
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:variant_attributes,code|alpha_dash',
            'type' => 'required|in:select,color,text',
            'display_type' => 'required|in:dropdown,buttons,color_swatch',
        ]);

        $validated['sort_order'] = VariantAttribute::max('sort_order') + 1;
        $validated['is_active'] = true;

        VariantAttribute::create($validated);

        return back()->with('success', 'Attribute created successfully.');
    }

    /**
     * Update attribute
     */
    public function update(Request $request, VariantAttribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:select,color,text',
            'display_type' => 'required|in:dropdown,buttons,color_swatch',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $attribute->update($validated);

        return back()->with('success', 'Attribute updated successfully.');
    }

    /**
     * Delete attribute
     */
    public function destroy(VariantAttribute $attribute)
    {
        $attribute->delete();
        return back()->with('success', 'Attribute deleted successfully.');
    }

    /**
     * Store attribute value
     */
    public function storeValue(Request $request, VariantAttribute $attribute)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:100',
            'display_value' => 'nullable|string|max:100',
            'color_code' => 'nullable|string|max:10',
        ]);

        $validated['variant_attribute_id'] = $attribute->id;
        $validated['sort_order'] = $attribute->values()->max('sort_order') + 1;
        $validated['is_active'] = true;

        VariantAttributeValue::create($validated);

        return back()->with('success', 'Value added successfully.');
    }

    /**
     * Update attribute value
     */
    public function updateValue(Request $request, VariantAttribute $attribute, VariantAttributeValue $value)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:100',
            'display_value' => 'nullable|string|max:100',
            'color_code' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $value->update($validated);

        return back()->with('success', 'Value updated successfully.');
    }

    /**
     * Delete attribute value
     */
    public function destroyValue(VariantAttribute $attribute, VariantAttributeValue $value)
    {
        $value->delete();
        return back()->with('success', 'Value deleted successfully.');
    }

    /**
     * Get values for AJAX
     */
    public function getValues(Request $request)
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
