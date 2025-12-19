<?php

namespace App\Console\Commands;

use App\Models\Category;
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

        $siteUrl = config('app.url', url('/'));
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
        
        // Home page
        $xml .= $this->addUrl($siteUrl, now()->format('Y-m-d'), 'daily', '1.0');
        
        // Static pages
        $staticPages = [
            ['url' => route('products.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('about'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => route('contact'), 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];
        
        foreach ($staticPages as $page) {
            $xml .= $this->addUrl($page['url'], now()->format('Y-m-d'), $page['changefreq'], $page['priority']);
        }
        
        // Categories
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $xml .= $this->addUrl(
                route('category.show', $category->slug),
                $category->updated_at->format('Y-m-d'),
                'weekly',
                '0.8'
            );
        }
        
        // Products with images
        $products = Product::where('is_active', true)->with('images')->get();
        foreach ($products as $product) {
            $images = [];
            foreach ($product->images as $image) {
                $images[] = [
                    'loc' => $image->url,
                    'title' => $product->name,
                    'caption' => $product->short_description ?? $product->name,
                ];
            }
            
            $xml .= $this->addUrl(
                route('products.show', $product->slug),
                $product->updated_at->format('Y-m-d'),
                'weekly',
                '0.9',
                $images
            );
        }
        
        $xml .= '</urlset>';
        
        // Save sitemap
        $path = public_path('sitemap.xml');
        File::put($path, $xml);
        
        $this->info('Sitemap generated successfully!');
        $this->info('Location: ' . $path);
        $this->info('Total URLs: ' . (count($staticPages) + 1 + $categories->count() + $products->count()));
        
        return 0;
    }
    
    private function addUrl(string $loc, string $lastmod, string $changefreq, string $priority, array $images = []): string
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
        
        // Add images
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
