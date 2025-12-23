<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category', 'primaryImage', 'activeVariants', 'defaultVariant');

        // Category filter
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $categoryIds = [$category->id];
                // Include children categories
                $childIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
                $categoryIds = array_merge($categoryIds, $childIds);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderByDesc('order_items_count');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(25);
        $categories = Category::active()->whereNull('parent_id')->withCount('activeProducts')->get();

        $currentCategory = $request->filled('category') 
            ? Category::where('slug', $request->category)->first() 
            : null;

        return view('frontend.products.index', compact('products', 'categories', 'currentCategory'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load('category', 'images', 'activeVariants', 'defaultVariant', 'comboItems.includedProduct');

        // Load approved reviews with user info
        $reviews = $product->reviews()
            ->where('is_approved', true)
            ->with('user', 'orderItem')
            ->latest()
            ->paginate(10);

        // Calculate review statistics
        $reviewStats = [
            'average' => $product->average_rating,
            'total' => $product->review_count,
            'distribution' => [],
        ];
        
        // Get rating distribution
        for ($i = 5; $i >= 1; $i--) {
            $count = $product->approvedReviews()->where('rating', $i)->count();
            $reviewStats['distribution'][$i] = [
                'count' => $count,
                'percentage' => $reviewStats['total'] > 0 ? round(($count / $reviewStats['total']) * 100) : 0
            ];
        }

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('primaryImage', 'activeVariants', 'defaultVariant')
            ->take(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts', 'reviews', 'reviewStats'));
    }

    public function category(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        // Get products from this category and its children
        $categoryIds = [$category->id];
        $childIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
        $categoryIds = array_merge($categoryIds, $childIds);

        $products = Product::active()
            ->whereIn('category_id', $categoryIds)
            ->with('primaryImage', 'category', 'activeVariants', 'defaultVariant')
            ->paginate(25);

        $categories = Category::active()->whereNull('parent_id')->withCount('activeProducts')->get();

        // Get child categories for display
        $childCategories = Category::active()->where('parent_id', $category->id)->get();

        return view('frontend.products.index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
            'childCategories' => $childCategories,
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $products = Product::active()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->with('primaryImage', 'activeVariants', 'defaultVariant')
            ->paginate(25);

        return view('frontend.products.search', compact('products', 'search'));
    }

    public function offers(Request $request)
    {
        $query = Product::active()
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->whereColumn('discount_price', '<', 'price')
            ->with('primaryImage', 'category', 'activeVariants', 'defaultVariant');

        // Category filter for offers
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Minimum discount filter
        if ($request->filled('min_discount')) {
            $minDiscount = (float) $request->min_discount;
            $query->whereRaw('((price - discount_price) / price * 100) >= ?', [$minDiscount]);
        }

        // Sorting
        $sort = $request->get('sort', 'discount');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('discount_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('discount_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
                $query->latest();
                break;
            case 'discount':
            default:
                // Order by discount percentage (highest first)
                $query->orderByRaw('((price - discount_price) / price * 100) DESC');
        }

        $products = $query->paginate(25);
        $categories = Category::active()->whereNull('parent_id')->withCount('activeProducts')->get();

        return view('frontend.products.offers', compact('products', 'categories'));
    }
}
