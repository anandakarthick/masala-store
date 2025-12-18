<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->position('home_slider')->orderBy('sort_order')->get();
        
        $categories = Category::active()
            ->parentCategories()
            ->withCount('activeProducts')
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::active()
            ->featured()
            ->with('category', 'primaryImage')
            ->take(8)
            ->get();

        $newArrivals = Product::active()
            ->with('category', 'primaryImage')
            ->latest()
            ->take(8)
            ->get();

        $bestSellers = Product::active()
            ->withCount('orderItems')
            ->with('category', 'primaryImage')
            ->orderByDesc('order_items_count')
            ->take(8)
            ->get();

        return view('frontend.home', compact(
            'banners',
            'categories',
            'featuredProducts',
            'newArrivals',
            'bestSellers'
        ));
    }

    public function about()
    {
        return view('frontend.about');
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Here you can send email or save to database
        // Mail::to(Setting::businessEmail())->send(new ContactFormMail($validated));

        return back()->with('success', 'Thank you for your message. We will get back to you soon!');
    }
}
