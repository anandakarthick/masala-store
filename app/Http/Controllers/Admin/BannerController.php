<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    protected $bannerSizes = [
        'whatsapp_status' => ['width' => 1080, 'height' => 1920, 'label' => 'WhatsApp Status', 'icon' => 'fab fa-whatsapp'],
        'instagram_story' => ['width' => 1080, 'height' => 1920, 'label' => 'Instagram Story', 'icon' => 'fab fa-instagram'],
        'instagram_post' => ['width' => 1080, 'height' => 1080, 'label' => 'Instagram Post', 'icon' => 'fab fa-instagram'],
        'facebook_post' => ['width' => 1200, 'height' => 630, 'label' => 'Facebook Post', 'icon' => 'fab fa-facebook'],
        'facebook_story' => ['width' => 1080, 'height' => 1920, 'label' => 'Facebook Story', 'icon' => 'fab fa-facebook'],
        'website_banner' => ['width' => 1920, 'height' => 600, 'label' => 'Website Banner', 'icon' => 'fas fa-globe'],
        'website_mobile' => ['width' => 800, 'height' => 400, 'label' => 'Website Mobile', 'icon' => 'fas fa-mobile-alt'],
        'google_display_square' => ['width' => 300, 'height' => 250, 'label' => 'Google Ads Square', 'icon' => 'fab fa-google'],
        'google_display_rect' => ['width' => 336, 'height' => 280, 'label' => 'Google Ads Rectangle', 'icon' => 'fab fa-google'],
        'google_display_leaderboard' => ['width' => 728, 'height' => 90, 'label' => 'Google Ads Leaderboard', 'icon' => 'fab fa-google'],
        'meta_feed' => ['width' => 1080, 'height' => 1080, 'label' => 'Meta Feed Ad', 'icon' => 'fab fa-facebook'],
        'meta_story' => ['width' => 1080, 'height' => 1920, 'label' => 'Meta Story Ad', 'icon' => 'fab fa-facebook'],
        'youtube_thumbnail' => ['width' => 1280, 'height' => 720, 'label' => 'YouTube Thumbnail', 'icon' => 'fab fa-youtube'],
    ];

    protected $templates = [
        'product_showcase' => ['label' => 'Product Showcase', 'description' => 'Highlight a single product'],
        'discount_offer' => ['label' => 'Discount Offer', 'description' => 'Promote discounts and sales'],
        'new_arrival' => ['label' => 'New Arrival', 'description' => 'Announce new products'],
        'combo_deal' => ['label' => 'Combo Deal', 'description' => 'Promote combo offers'],
        'festive_offer' => ['label' => 'Festive Offer', 'description' => 'Festival special promotions'],
        'free_shipping' => ['label' => 'Free Shipping', 'description' => 'Free delivery promotion'],
        'brand_awareness' => ['label' => 'Brand Awareness', 'description' => 'Company branding'],
        'custom' => ['label' => 'Custom Banner', 'description' => 'Create your own design'],
    ];

    protected $colorThemes = [
        'green_fresh' => ['primary' => '#16a34a', 'secondary' => '#22c55e', 'accent' => '#f0fdf4', 'text' => '#ffffff', 'label' => 'Fresh Green'],
        'orange_spicy' => ['primary' => '#ea580c', 'secondary' => '#f97316', 'accent' => '#fff7ed', 'text' => '#ffffff', 'label' => 'Spicy Orange'],
        'red_hot' => ['primary' => '#dc2626', 'secondary' => '#ef4444', 'accent' => '#fef2f2', 'text' => '#ffffff', 'label' => 'Hot Red'],
        'gold_premium' => ['primary' => '#ca8a04', 'secondary' => '#eab308', 'accent' => '#fefce8', 'text' => '#ffffff', 'label' => 'Premium Gold'],
        'purple_royal' => ['primary' => '#7c3aed', 'secondary' => '#8b5cf6', 'accent' => '#f5f3ff', 'text' => '#ffffff', 'label' => 'Royal Purple'],
        'brown_natural' => ['primary' => '#78350f', 'secondary' => '#92400e', 'accent' => '#fef3c7', 'text' => '#ffffff', 'label' => 'Natural Brown'],
        'pink_festive' => ['primary' => '#db2777', 'secondary' => '#ec4899', 'accent' => '#fdf2f8', 'text' => '#ffffff', 'label' => 'Festive Pink'],
        'teal_herbal' => ['primary' => '#0d9488', 'secondary' => '#14b8a6', 'accent' => '#f0fdfa', 'text' => '#ffffff', 'label' => 'Herbal Teal'],
        'dark_elegant' => ['primary' => '#1f2937', 'secondary' => '#374151', 'accent' => '#f9fafb', 'text' => '#ffffff', 'label' => 'Elegant Dark'],
    ];

    public function index()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.banner-generator.index', [
            'products' => $products,
            'categories' => $categories,
            'bannerSizes' => $this->bannerSizes,
            'templates' => $this->templates,
            'colorThemes' => $this->colorThemes,
            'businessName' => Setting::get('business_name', 'SV Masala & Herbal Products'),
            'businessPhone' => Setting::get('business_phone', ''),
            'businessTagline' => Setting::get('business_tagline', 'Premium Homemade Masala & Herbal Products'),
            'logoUrl' => Setting::logo(),
        ]);
    }

    public function getProductDetails(Request $request)
    {
        $product = Product::with(['category', 'images', 'activeVariants'])->find($request->product_id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->short_description ?? substr(strip_tags($product->description ?? ''), 0, 150),
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'discount_percentage' => $product->discount_percentage,
            'price_display' => $product->price_display,
            'image_url' => $product->primary_image_url,
            'category_name' => $product->category->name ?? '',
            'weight_display' => $product->weight_display,
        ]);
    }

    public function getCategoryDetails(Request $request)
    {
        $category = Category::withCount(['products' => function($q) {
            $q->where('is_active', true);
        }])->find($request->category_id);
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'products_count' => $category->products_count,
        ]);
    }

    /**
     * Save generated banner to store banners
     */
    public function saveToStore(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // Base64 image data
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'position' => 'required|in:home_slider,home_banner,category_banner,popup',
            'is_active' => 'boolean',
        ]);

        // Decode base64 image
        $imageData = $request->image;
        
        // Remove data URL prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
            $extension = $matches[1];
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        } else {
            $extension = 'png';
        }
        
        $imageData = base64_decode($imageData);
        
        if ($imageData === false) {
            return response()->json(['success' => false, 'message' => 'Invalid image data'], 400);
        }

        // Generate unique filename
        $filename = 'banners/generated_' . time() . '_' . uniqid() . '.' . $extension;
        
        // Store the image
        Storage::disk('public')->put($filename, $imageData);

        // Create banner record
        $banner = Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $filename,
            'link' => $request->link,
            'button_text' => $request->button_text ?? 'Shop Now',
            'position' => $request->position,
            'sort_order' => Banner::where('position', $request->position)->max('sort_order') + 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banner saved to store successfully!',
            'banner' => [
                'id' => $banner->id,
                'title' => $banner->title,
                'image_url' => $banner->image_url,
            ]
        ]);
    }

    /**
     * Get all store banners
     */
    public function getStoreBanners()
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->get();
        
        return response()->json([
            'success' => true,
            'banners' => $banners->map(function($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'image_url' => $banner->image_url,
                    'position' => $banner->position,
                    'is_active' => $banner->is_active,
                ];
            })
        ]);
    }
}
