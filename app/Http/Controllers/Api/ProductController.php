<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Setting;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get home page data
     */
    public function home()
    {
        // Get banners
        $banners = Banner::where('is_active', true)
            ->where('position', 'home_slider')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('sort_order')
            ->get()
            ->map(fn($banner) => $this->formatBanner($banner));

        // Get categories
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($cat) => $this->formatCategory($cat));

        // Get featured products
        $featuredProducts = Product::with(['images', 'category', 'activeVariants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        // Get new arrivals
        $newArrivals = Product::with(['images', 'category', 'activeVariants'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        // Get offer products
        $offerProducts = Product::with(['images', 'category', 'activeVariants'])
            ->where('is_active', true)
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        // Get settings
        $settings = [
            'business_name' => Setting::get('business_name', 'SV Products'),
            'business_phone' => Setting::get('business_phone', ''),
            'business_email' => Setting::get('business_email', ''),
            'whatsapp_number' => Setting::get('whatsapp_number', ''),
            'whatsapp_enabled' => Setting::get('whatsapp_enabled', '0') === '1',
            'free_shipping_amount' => (float) Setting::get('free_shipping_amount', 500),
            'min_order_amount' => (float) Setting::get('min_order_amount', 0),
            'currency' => Setting::get('currency', '₹'),
            'logo' => Setting::logo(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners,
                'categories' => $categories,
                'featured_products' => $featuredProducts,
                'new_arrivals' => $newArrivals,
                'offer_products' => $offerProducts,
                'settings' => $settings,
            ]
        ]);
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->withCount(['activeProducts'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($cat) => $this->formatCategory($cat, true));

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get products list
     */
    public function products(Request $request)
    {
        $query = Product::with(['images', 'category', 'activeVariants'])
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('category_slug')) {
            $category = Category::where('slug', $request->category_slug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Filter by offers
        if ($request->boolean('offers')) {
            $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('discount_price', '>=', $request->min_price)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('discount_price')->where('price', '>=', $request->min_price);
                    });
            });
        }

        if ($request->has('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('discount_price', '<=', $request->max_price)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('discount_price')->where('price', '<=', $request->max_price);
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'price_low':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'popular':
                $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $products->getCollection()->map(fn($p) => $this->formatProduct($p)),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get single product
     */
    public function show($idOrSlug)
    {
        $product = Product::with(['images', 'category', 'activeVariants', 'approvedReviews.user'])
            ->where('is_active', true)
            ->where(function ($q) use ($idOrSlug) {
                $q->where('id', $idOrSlug)->orWhere('slug', $idOrSlug);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // Get related products
        $relatedProducts = Product::with(['images', 'category', 'activeVariants'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->limit(6)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $this->formatProduct($product, true),
                'related_products' => $relatedProducts,
            ]
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Format banner for response
     */
    private function formatBanner(Banner $banner): array
    {
        return [
            'id' => $banner->id,
            'title' => $banner->title,
            'subtitle' => $banner->subtitle,
            'image' => $banner->image ? asset('storage/' . $banner->image) : null,
            'link' => $banner->link,
            'button_text' => $banner->button_text,
        ];
    }

    /**
     * Format category for response
     */
    private function formatCategory(Category $category, bool $withChildren = false): array
    {
        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image' => $category->image_url,
            'product_count' => $category->active_products_count ?? $category->activeProducts()->count(),
        ];

        if ($withChildren && $category->children) {
            $data['children'] = $category->children->map(fn($c) => $this->formatCategory($c));
        }

        return $data;
    }

    /**
     * Format product for response
     */
    private function formatProduct(Product $product, bool $detailed = false): array
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'short_description' => $product->short_description,
            'price' => (float) $product->price,
            'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
            'effective_price' => (float) $product->effective_price,
            'discount_percentage' => $product->discount_percentage,
            'image' => $product->primary_image_url,
            'images' => $product->images->map(fn($img) => [
                'id' => $img->id,
                'url' => asset('storage/' . $img->image_path),
                'is_primary' => $img->is_primary,
            ]),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'is_featured' => $product->is_featured,
            'has_variants' => $product->has_variants,
            'in_stock' => !$product->isOutOfStock(),
            'stock_quantity' => $product->has_variants ? $product->total_stock : $product->stock_quantity,
            'rating' => $product->average_rating,
            'review_count' => $product->review_count,
            'weight' => $product->weight,
            'unit' => $product->unit,
            'weight_display' => $product->weight_display,
        ];

        if ($product->has_variants) {
            $data['variants'] = $product->activeVariants->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'display_name' => $v->display_name,
                'sku' => $v->sku,
                'price' => (float) $v->price,
                'discount_price' => $v->discount_price ? (float) $v->discount_price : null,
                'effective_price' => (float) $v->effective_price,
                'discount_percentage' => $v->discount_percentage,
                'stock_quantity' => $v->stock_quantity,
                'in_stock' => !$v->isOutOfStock(),
                'is_default' => $v->is_default,
                'weight' => $v->weight,
                'unit' => $v->unit,
                'weight_display' => $v->weight_display,
                'size' => $v->size,
                'color' => $v->color,
                'color_code' => $v->color_code,
                'image' => $v->variant_image_url,
            ]);

            $data['price_range'] = $product->price_range;
        }

        if ($detailed) {
            $data['description'] = $product->description;
            $data['meta_title'] = $product->meta_title;
            $data['meta_description'] = $product->meta_description;
            $data['hsn_code'] = $product->hsn_code;
            $data['gst_percentage'] = (float) $product->gst_percentage;

            // Include reviews
            $data['reviews'] = $product->approvedReviews->map(fn($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'title' => $r->title,
                'comment' => $r->comment,
                'user_name' => $r->user?->name ?? 'Anonymous',
                'is_verified' => $r->is_verified_purchase,
                'created_at' => $r->created_at->diffForHumans(),
            ]);
        }

        return $data;
    }
}
