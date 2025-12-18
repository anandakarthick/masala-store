<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Masala Products
            [
                'category' => 'ground-spices',
                'name' => 'Red Chilli Powder',
                'sku' => 'MSL-RCP-100',
                'description' => 'Premium quality red chilli powder, perfect for adding heat and color to your dishes.',
                'short_description' => 'Pure red chilli powder',
                'price' => 120,
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'gst_percentage' => 5,
                'is_featured' => true,
            ],
            [
                'category' => 'ground-spices',
                'name' => 'Turmeric Powder',
                'sku' => 'MSL-TRM-200',
                'description' => 'Pure organic turmeric powder with high curcumin content.',
                'short_description' => 'Organic turmeric powder',
                'price' => 150,
                'weight' => '200',
                'unit' => 'g',
                'stock_quantity' => 150,
                'gst_percentage' => 5,
                'is_featured' => true,
            ],
            [
                'category' => 'spice-blends',
                'name' => 'Garam Masala',
                'sku' => 'MSL-GRM-100',
                'description' => 'Traditional garam masala blend made with premium whole spices.',
                'short_description' => 'Aromatic spice blend',
                'price' => 180,
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 80,
                'gst_percentage' => 5,
                'is_featured' => true,
            ],
            [
                'category' => 'spice-blends',
                'name' => 'Sambar Powder',
                'sku' => 'MSL-SMB-250',
                'description' => 'Authentic South Indian sambar powder for delicious sambar.',
                'short_description' => 'Traditional sambar masala',
                'price' => 160,
                'weight' => '250',
                'unit' => 'g',
                'stock_quantity' => 120,
                'gst_percentage' => 5,
            ],
            
            // Oil Products
            [
                'category' => 'coconut-oil',
                'name' => 'Pure Coconut Oil',
                'sku' => 'OIL-COC-500',
                'description' => 'Cold pressed pure coconut oil, perfect for cooking and beauty care.',
                'short_description' => 'Cold pressed coconut oil',
                'price' => 250,
                'weight' => '500',
                'unit' => 'ml',
                'stock_quantity' => 50,
                'gst_percentage' => 5,
                'is_featured' => true,
            ],
            [
                'category' => 'groundnut-oil',
                'name' => 'Groundnut Oil',
                'sku' => 'OIL-GND-1L',
                'description' => 'Pure cold pressed groundnut oil for healthy cooking.',
                'short_description' => 'Cold pressed groundnut oil',
                'price' => 320,
                'weight' => '1',
                'unit' => 'L',
                'stock_quantity' => 40,
                'gst_percentage' => 5,
            ],
            [
                'category' => 'sesame-oil',
                'name' => 'Gingelly Oil',
                'sku' => 'OIL-SES-500',
                'description' => 'Traditional gingelly (sesame) oil for authentic taste.',
                'short_description' => 'Traditional sesame oil',
                'price' => 280,
                'weight' => '500',
                'unit' => 'ml',
                'stock_quantity' => 45,
                'gst_percentage' => 5,
            ],
            
            // Candles
            [
                'category' => 'decorative-candles',
                'name' => 'Rose Scented Candle',
                'sku' => 'CNL-RSE-01',
                'description' => 'Beautiful rose scented decorative candle for home decor.',
                'short_description' => 'Rose fragrance candle',
                'price' => 150,
                'weight' => '200',
                'unit' => 'g',
                'stock_quantity' => 60,
                'gst_percentage' => 12,
                'is_featured' => true,
            ],
            [
                'category' => 'diya-lamp',
                'name' => 'Brass Diya Set',
                'sku' => 'CNL-DYA-SET',
                'description' => 'Traditional brass diya set of 5 for puja and festivals.',
                'short_description' => 'Set of 5 brass diyas',
                'price' => 450,
                'weight' => '1',
                'unit' => 'piece',
                'stock_quantity' => 30,
                'gst_percentage' => 12,
            ],
            
            // Return Gifts
            [
                'category' => 'gift-boxes',
                'name' => 'Spice Gift Box',
                'sku' => 'GFT-SPB-01',
                'description' => 'Premium spice gift box containing 6 different spices.',
                'short_description' => '6 spices gift set',
                'price' => 599,
                'weight' => '1',
                'unit' => 'piece',
                'stock_quantity' => 25,
                'gst_percentage' => 12,
                'is_featured' => true,
            ],
            [
                'category' => 'combo-packs',
                'name' => 'Wedding Return Gift Combo',
                'sku' => 'GFT-WED-01',
                'description' => 'Beautiful wedding return gift combo with spices and candles.',
                'short_description' => 'Wedding return gift set',
                'price' => 299,
                'weight' => '1',
                'unit' => 'piece',
                'stock_quantity' => 100,
                'gst_percentage' => 12,
            ],
            [
                'category' => 'festival-specials',
                'name' => 'Diwali Gift Hamper',
                'sku' => 'GFT-DIW-01',
                'description' => 'Special Diwali gift hamper with sweets, diyas, and spices.',
                'short_description' => 'Diwali special hamper',
                'price' => 799,
                'discount_price' => 699,
                'weight' => '1',
                'unit' => 'piece',
                'stock_quantity' => 50,
                'gst_percentage' => 12,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category'];
            unset($productData['category']);

            $category = Category::where('slug', $categorySlug)->first();
            
            if ($category) {
                $productData['category_id'] = $category->id;
                Product::firstOrCreate(
                    ['sku' => $productData['sku']],
                    $productData
                );
            }
        }
    }
}
