<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\DeliveryPartner;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'nullable|email',
            'business_phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:20',
            'currency' => 'nullable|string|max:5',
            'min_order_amount' => 'nullable|numeric|min:0',
            'free_shipping_amount' => 'nullable|numeric|min:0',
            'default_shipping_charge' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|max:2048',
            // First-time customer discount settings
            'first_time_discount_enabled' => 'nullable|boolean',
            'first_time_discount_max_customers' => 'nullable|integer|min:1|max:10000',
            'first_time_discount_percentage' => 'nullable|numeric|min:1|max:100',
            'first_time_discount_min_order' => 'nullable|numeric|min:0',
            'first_time_discount_max_amount' => 'nullable|numeric|min:0',
        ]);

        // Handle checkbox for first_time_discount_enabled
        $validated['first_time_discount_enabled'] = $request->boolean('first_time_discount_enabled');

        foreach ($validated as $key => $value) {
            if ($key === 'logo') {
                if ($request->hasFile('logo')) {
                    $oldLogo = Setting::get('logo');
                    if ($oldLogo) {
                        Storage::disk('public')->delete($oldLogo);
                    }
                    $value = $request->file('logo')->store('settings', 'public');
                    Setting::set('logo', $value, 'image', 'general');
                }
            } else {
                // Determine group based on key
                $group = str_starts_with($key, 'first_time_discount') ? 'promotions' : 'general';
                Setting::set($key, $value, 'text', $group);
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    // Banners
    public function banners()
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->get();
        return view('admin.settings.banners', compact('banners'));
    }

    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'position' => 'required|in:home_slider,home_banner,category_banner,popup',
            'sort_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $validated['image'] = $request->file('image')->store('banners', 'public');
        $validated['is_active'] = $request->boolean('is_active', true);

        Banner::create($validated);

        return back()->with('success', 'Banner created successfully.');
    }

    public function updateBanner(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'position' => 'required|in:home_slider,home_banner,category_banner,popup',
            'sort_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $banner->update($validated);

        return back()->with('success', 'Banner updated successfully.');
    }

    public function destroyBanner(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();

        return back()->with('success', 'Banner deleted successfully.');
    }

    // Delivery Partners
    public function deliveryPartners()
    {
        $partners = DeliveryPartner::orderBy('name')->get();
        return view('admin.settings.delivery_partners', compact('partners'));
    }

    public function storeDeliveryPartner(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'tracking_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        DeliveryPartner::create($validated);

        return back()->with('success', 'Delivery partner added successfully.');
    }

    public function updateDeliveryPartner(Request $request, DeliveryPartner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'tracking_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $partner->update($validated);

        return back()->with('success', 'Delivery partner updated successfully.');
    }

    public function destroyDeliveryPartner(DeliveryPartner $partner)
    {
        $partner->delete();
        return back()->with('success', 'Delivery partner deleted successfully.');
    }
}
