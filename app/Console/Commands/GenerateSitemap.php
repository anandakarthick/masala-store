<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap for SEO';

    public function handle()
    {
        $siteUrl = 'https://www.svproducts.store';
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

        // Homepage - Highest priority
        $xml .= $this->addUrl($siteUrl, now()->toW3cString(), 'daily', '1.0');

        // Main pages
        $xml .= $this->addUrl($siteUrl . '/products', now()->toW3cString(), 'daily', '0.9');
        $xml .= $this->addUrl($siteUrl . '/products/offers', now()->toW3cString(), 'daily', '0.9');
        $xml .= $this->addUrl($siteUrl . '/about', now()->subDays(7)->toW3cString(), 'monthly', '0.6');
        $xml .= $this->addUrl($siteUrl . '/contact', now()->subDays(7)->toW3cString(), 'monthly', '0.6');
        $xml .= $this->addUrl($siteUrl . '/combo', now()->toW3cString(), 'weekly', '0.7');
        $xml .= $this->addUrl($siteUrl . '/track', now()->subDays(7)->toW3cString(), 'monthly', '0.4');

        // Categories - High priority
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $xml .= $this->addUrl(
                $siteUrl . '/category/' . $category->slug,
                $category->updated_at->toW3cString(),
                'weekly',
                '0.8'
            );
        }

        // Products - High priority with images
        $products = Product::where('is_active', true)
            ->with('images')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($products as $product) {
            $images = [];
            foreach ($product->images as $image) {
                $images[] = [
                    'loc' => asset('storage/' . $image->image_path),
                    'title' => $product->name,
                    'caption' => $product->short_description ?? $product->name,
                ];
            }
            
            $xml .= $this->addUrl(
                $siteUrl . '/products/' . $product->slug,
                $product->updated_at->toW3cString(),
                'weekly',
                $product->is_featured ? '0.9' : '0.8',
                $images
            );
        }

        // Static pages
        $pages = Page::where('is_active', true)->get();
        foreach ($pages as $page) {
            $xml .= $this->addUrl(
                $siteUrl . '/page/' . $page->slug,
                $page->updated_at->toW3cString(),
                'monthly',
                '0.5'
            );
        }

        $xml .= '</urlset>';

        // Save to public folder
        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap generated successfully!');
        $this->info('Total URLs: ' . (count($categories) + count($products) + count($pages) + 7));
        
        return 0;
    }

    private function addUrl($loc, $lastmod, $changefreq, $priority, $images = [])
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
        
        foreach ($images as $image) {
            $xml .= '    <image:image>' . PHP_EOL;
            $xml .= '      <image:loc>' . htmlspecialchars($image['loc']) . '</image:loc>' . PHP_EOL;
            $xml .= '      <image:title>' . htmlspecialchars($image['title']) . '</image:title>' . PHP_EOL;
            if (!empty($image['caption'])) {
                $xml .= '      <image:caption>' . htmlspecialchars($image['caption']) . '</image:caption>' . PHP_EOL;
            }
            $xml .= '    </image:image>' . PHP_EOL;
        }
        
        $xml .= '  </url>' . PHP_EOL;
        
        return $xml;
    }
}
