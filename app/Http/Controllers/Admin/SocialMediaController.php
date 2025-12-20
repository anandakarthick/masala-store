<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use App\Models\SocialMediaLink;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index()
    {
        $socialLinks = SocialMediaLink::orderBy('sort_order')->get();
        $platforms = SocialMediaLink::getPlatforms();
        $whatsappNumber = Setting::get('whatsapp_number', '');
        $whatsappEnabled = Setting::get('whatsapp_enabled', '1');
        $whatsappMessage = Setting::get('whatsapp_default_message', 'Hello! I would like to place an order.');
        
        // Marquee/Announcement Bar Settings
        $marqueeEnabled = Setting::get('marquee_enabled', '1');
        $marqueeText = Setting::get('marquee_text', '🎉 Free Shipping on Orders Above ₹500 | 100% Pure & Natural Products | Order Now! 🌿');
        $marqueeSpeed = Setting::get('marquee_speed', '30');
        $marqueeBgColor = Setting::get('marquee_bg_color', '#15803d');
        
        return view('admin.settings.social-media', compact(
            'socialLinks', 
            'platforms', 
            'whatsappNumber', 
            'whatsappEnabled', 
            'whatsappMessage',
            'marqueeEnabled',
            'marqueeText',
            'marqueeSpeed',
            'marqueeBgColor'
        ));
    }

    public function storeLink(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'url' => 'required|url|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $platforms = SocialMediaLink::getPlatforms();
        if (isset($platforms[$validated['platform']])) {
            $validated['icon'] = $validated['icon'] ?? $platforms[$validated['platform']]['icon'];
            $validated['color'] = $validated['color'] ?? $platforms[$validated['platform']]['color'];
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? SocialMediaLink::max('sort_order') + 1;

        SocialMediaLink::create($validated);

        return back()->with('success', 'Social media link added successfully.');
    }

    public function updateLink(Request $request, SocialMediaLink $link)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'url' => 'required|url|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $link->update($validated);

        return back()->with('success', 'Social media link updated successfully.');
    }

    public function destroyLink(SocialMediaLink $link)
    {
        $link->delete();
        return back()->with('success', 'Social media link deleted successfully.');
    }

    public function updateWhatsApp(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|max:20',
            'whatsapp_default_message' => 'nullable|string|max:500',
        ]);

        Setting::set('whatsapp_number', $request->whatsapp_number, 'text', 'contact');
        Setting::set('whatsapp_enabled', $request->boolean('whatsapp_enabled') ? '1' : '0', 'boolean', 'contact');
        Setting::set('whatsapp_default_message', $request->whatsapp_default_message ?? 'Hello! I would like to place an order.', 'text', 'contact');

        return back()->with('success', 'WhatsApp settings updated successfully.');
    }

    public function updateMarquee(Request $request)
    {
        $request->validate([
            'marquee_text' => 'required|string|max:1000',
            'marquee_speed' => 'nullable|integer|min:10|max:120',
            'marquee_bg_color' => 'nullable|string|max:20',
        ]);

        Setting::set('marquee_enabled', $request->boolean('marquee_enabled') ? '1' : '0', 'boolean', 'appearance');
        Setting::set('marquee_text', $request->marquee_text, 'text', 'appearance');
        Setting::set('marquee_speed', $request->marquee_speed ?? '30', 'text', 'appearance');
        Setting::set('marquee_bg_color', $request->marquee_bg_color ?? '#15803d', 'text', 'appearance');

        return back()->with('success', 'Announcement bar settings updated successfully.');
    }
}
