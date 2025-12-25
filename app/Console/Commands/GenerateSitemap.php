<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap for SEO';

    public function handle()
    {
        $this->info('Generating sitemap...');

        // Always use the canonical domain for sitemap
        $siteUrl = 'https://www.svproducts.store';
        
        // Temporarily set the app URL for route generation
        $originalUrl = config('app.url');
        config(['app.url' => $siteUrl]);
        \URL::forceRootUrl($siteUrl);
        \URL::forceScheme('https');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
        $xml .= 'xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;
        
        // Home page - highest priority
        $xml .= $this->addUrl($siteUrl, now()->format('Y-m-d'), 'daily', '1.0');
        
        // Main static pages
        $staticPages = [
            ['url' => route('products.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('products.offers'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('combo.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => route('about'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => route('contact'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => route('tracking.index'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];
        
        foreach ($staticPages as $page) {
            $xml .= $this->addUrl($page['url'], now()->format('Y-m-d'), $page['changefreq'], $page['priority']);
        }
        
        // Dynamic pages (Privacy Policy, Terms, etc.)
        $pages = Page::where('is_active', true)->get();
        foreach ($pages as $page) {
            $xml .= $this->addUrl(
                route('page.show', $page->slug),
                $page->updated_at->format('Y-m-d'),
                'monthly',
                '0.5'
            );
        }
        
        // Categories - high priority
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $images = [];
            if ($category->image_url) {
                $images[] = [
                    'loc' => $category->image_url,
                    'title' => $category->name,
                    'caption' => $category->description ?? 'Browse ' . $category->name . ' products',
                ];
            }
            
            $xml .= $this->addUrl(
                route('category.show', $category->slug),
                $category->updated_at->format('Y-m-d'),
                'daily',
                '0.8',
                $images
            );
        }
        
        // Products with images - highest priority after home
        $products = Product::where('is_active', true)
            ->with(['images', 'category'])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($products as $product) {
            $images = [];
            foreach ($product->images as $image) {
                $images[] = [
                    'loc' => $image->url,
                    'title' => $product->name,
                    'caption' => $product->short_description ?? $product->name . ' - ' . $product->category->name,
                ];
            }
            
            // Products updated recently get higher priority
            $daysSinceUpdate = now()->diffInDays($product->updated_at);
            $priority = $daysSinceUpdate < 7 ? '0.9' : ($daysSinceUpdate < 30 ? '0.8' : '0.7');
            
            $xml .= $this->addUrl(
                route('products.show', $product->slug),
                $product->updated_at->format('Y-m-d'),
                'weekly',
                $priority,
                $images
            );
        }
        
        $xml .= '</urlset>';
        
        // Save sitemap
        $path = public_path('sitemap.xml');
        File::put($path, $xml);
        
        // Generate sitemap index if needed (for large sites)
        $totalUrls = count($staticPages) + 1 + $pages->count() + $categories->count() + $products->count();
        
        $this->info('Sitemap generated successfully!');
        $this->info('Location: ' . $path);
        $this->info('Total URLs: ' . $totalUrls);
        $this->newLine();
        $this->table(
            ['Type', 'Count'],
            [
                ['Static Pages', count($staticPages) + 1],
                ['Dynamic Pages', $pages->count()],
                ['Categories', $categories->count()],
                ['Products', $products->count()],
                ['Total', $totalUrls],
            ]
        );
        
        // Restore original URL configuration
        config(['app.url' => $originalUrl]);
        
        return 0;
    }
    
    private function addUrl(string $loc, string $lastmod, string $changefreq, string $priority, array $images = []): string
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
        
        // Add images (max 1000 per URL according to Google)
        $imageCount = 0;
        foreach ($images as $image) {
            if ($imageCount >= 1000) break;
            if (empty($image['loc'])) continue;
            
            $xml .= '    <image:image>' . PHP_EOL;
            $xml .= '      <image:loc>' . htmlspecialchars($image['loc']) . '</image:loc>' . PHP_EOL;
            if (!empty($image['title'])) {
                $xml .= '      <image:title>' . htmlspecialchars($image['title']) . '</image:title>' . PHP_EOL;
            }
            if (!empty($image['caption'])) {
                $xml .= '      <image:caption>' . htmlspecialchars($image['caption']) . '</image:caption>' . PHP_EOL;
            }
            $xml .= '    </image:image>' . PHP_EOL;
            $imageCount++;
        }
        
        $xml .= '  </url>' . PHP_EOL;
        
        return $xml;
    }
}
