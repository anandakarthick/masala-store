<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Products WITHOUT variants (gift packs, combo packs, etc.)
        $simpleProducts = [
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
                'category' => 'decorative-candles',
                'name' => 'Lavender Candle',
                'sku' => 'CNL-LAV-01',
                'description' => 'Relaxing lavender scented candle for peaceful ambiance.',
                'short_description' => 'Lavender fragrance candle',
                'price' => 175,
                'weight' => '200',
                'unit' => 'g',
                'stock_quantity' => 45,
                'gst_percentage' => 12,
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
            
            // Gift Packs (no variants)
            [
                'category' => 'festival-gift-pack',
                'name' => 'Diwali Special Gift Box',
                'sku' => 'CGP-DIW-01',
                'description' => 'Beautifully packaged Diwali gift box with premium spices, scented candles, brass diya, and dry fruits.',
                'short_description' => 'Premium Diwali gift hamper',
                'price' => 1299,
                'discount_price' => 1099,
                'weight' => '1.5',
                'unit' => 'kg',
                'stock_quantity' => 30,
                'gst_percentage' => 12,
                'is_featured' => true,
            ],
            [
                'category' => 'wedding-gift-pack',
                'name' => 'Premium Wedding Gift Box',
                'sku' => 'CGP-WED-01',
                'description' => 'Elegant wedding gift box with assorted spices, oils, decorative candles, and silver-coated items.',
                'short_description' => 'Luxury wedding gift set',
                'price' => 1599,
                'discount_price' => 1399,
                'weight' => '2',
                'unit' => 'kg',
                'stock_quantity' => 25,
                'gst_percentage' => 12,
                'is_featured' => true,
            ],
            [
                'category' => 'mini-gift-pack',
                'name' => 'Mini Spice Trio',
                'sku' => 'CGP-MIN-01',
                'description' => 'Cute mini gift pack with 3 essential spices in decorative glass jars.',
                'short_description' => 'Mini 3-spice gift set',
                'price' => 149,
                'discount_price' => 129,
                'weight' => '150',
                'unit' => 'g',
                'stock_quantity' => 150,
                'gst_percentage' => 12,
            ],
            [
                'category' => 'corporate-gift-pack',
                'name' => 'Corporate Premium Box',
                'sku' => 'CGP-CRP-01',
                'description' => 'Professional corporate gift box with premium spices, organic oils, and artisan candles in elegant packaging.',
                'short_description' => 'Corporate gift hamper',
                'price' => 2499,
                'discount_price' => 2199,
                'weight' => '2.5',
                'unit' => 'kg',
                'stock_quantity' => 20,
                'gst_percentage' => 12,
                'is_featured' => true,
            ],
            
            // Combo Masala Packs (no variants - they are combos themselves)
            [
                'category' => 'kitchen-essentials-combo',
                'name' => 'Kitchen Essentials Masala Box',
                'sku' => 'CMB-KIT-01',
                'description' => 'Complete kitchen essentials with 8 must-have spices: Turmeric, Chilli, Coriander, Cumin, Garam Masala, Pepper, Mustard, and Fenugreek.',
                'short_description' => '8 essential spices combo',
                'price' => 599,
                'discount_price' => 499,
                'weight' => '800',
                'unit' => 'g',
                'stock_quantity' => 50,
                'gst_percentage' => 5,
                'is_featured' => true,
            ],
            [
                'category' => 'biryani-combo',
                'name' => 'Biryani Master Combo',
                'sku' => 'CMB-BRY-01',
                'description' => 'Everything you need for perfect biryani: Biryani Masala, Saffron, Shah Jeera, Whole Spices Mix, and Fried Onions.',
                'short_description' => 'Complete biryani spice kit',
                'price' => 550,
                'discount_price' => 475,
                'weight' => '400',
                'unit' => 'g',
                'stock_quantity' => 35,
                'gst_percentage' => 5,
                'is_featured' => true,
            ],
        ];

        // Create simple products
        foreach ($simpleProducts as $productData) {
            $categorySlug = $productData['category'];
            unset($productData['category']);

            $category = Category::where('slug', $categorySlug)->first();
            
            if ($category) {
                $productData['category_id'] = $category->id;
                $productData['has_variants'] = false;
                Product::firstOrCreate(
                    ['sku' => $productData['sku']],
                    $productData
                );
            }
        }

        // Products WITH variants (Masalas & Oils)
        $variantProducts = [
            // Masala Products with variants
            [
                'category' => 'ground-spices',
                'name' => 'Red Chilli Powder',
                'sku' => 'MSL-RCP',
                'description' => 'Premium quality red chilli powder, perfect for adding heat and color to your dishes. Made from handpicked red chillies.',
                'short_description' => 'Pure red chilli powder',
                'gst_percentage' => 5,
                'is_featured' => true,
                'variants' => [
                    ['name' => '50g', 'sku' => 'MSL-RCP-50', 'weight' => 50, 'unit' => 'g', 'price' => 35, 'stock' => 100],
                    ['name' => '100g', 'sku' => 'MSL-RCP-100', 'weight' => 100, 'unit' => 'g', 'price' => 65, 'stock' => 150, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-RCP-200', 'weight' => 200, 'unit' => 'g', 'price' => 120, 'stock' => 100],
                    ['name' => '250g', 'sku' => 'MSL-RCP-250', 'weight' => 250, 'unit' => 'g', 'price' => 145, 'stock' => 80],
                    ['name' => '500g', 'sku' => 'MSL-RCP-500', 'weight' => 500, 'unit' => 'g', 'price' => 275, 'stock' => 60],
                    ['name' => '1kg', 'sku' => 'MSL-RCP-1000', 'weight' => 1000, 'unit' => 'g', 'price' => 520, 'stock' => 40],
                ],
            ],
            [
                'category' => 'ground-spices',
                'name' => 'Turmeric Powder',
                'sku' => 'MSL-TRM',
                'description' => 'Pure organic turmeric powder with high curcumin content. Adds beautiful color and health benefits to your food.',
                'short_description' => 'Organic turmeric powder',
                'gst_percentage' => 5,
                'is_featured' => true,
                'variants' => [
                    ['name' => '50g', 'sku' => 'MSL-TRM-50', 'weight' => 50, 'unit' => 'g', 'price' => 30, 'stock' => 120],
                    ['name' => '100g', 'sku' => 'MSL-TRM-100', 'weight' => 100, 'unit' => 'g', 'price' => 55, 'stock' => 150, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-TRM-200', 'weight' => 200, 'unit' => 'g', 'price' => 100, 'stock' => 100],
                    ['name' => '250g', 'sku' => 'MSL-TRM-250', 'weight' => 250, 'unit' => 'g', 'price' => 120, 'stock' => 80],
                    ['name' => '500g', 'sku' => 'MSL-TRM-500', 'weight' => 500, 'unit' => 'g', 'price' => 230, 'stock' => 70],
                    ['name' => '1kg', 'sku' => 'MSL-TRM-1000', 'weight' => 1000, 'unit' => 'g', 'price' => 440, 'stock' => 50],
                ],
            ],
            [
                'category' => 'ground-spices',
                'name' => 'Coriander Powder',
                'sku' => 'MSL-COR',
                'description' => 'Fresh and aromatic coriander powder for everyday cooking. Ground from premium coriander seeds.',
                'short_description' => 'Fresh coriander powder',
                'gst_percentage' => 5,
                'variants' => [
                    ['name' => '50g', 'sku' => 'MSL-COR-50', 'weight' => 50, 'unit' => 'g', 'price' => 25, 'stock' => 100],
                    ['name' => '100g', 'sku' => 'MSL-COR-100', 'weight' => 100, 'unit' => 'g', 'price' => 45, 'stock' => 120, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-COR-200', 'weight' => 200, 'unit' => 'g', 'price' => 85, 'stock' => 90],
                    ['name' => '500g', 'sku' => 'MSL-COR-500', 'weight' => 500, 'unit' => 'g', 'price' => 195, 'stock' => 60],
                ],
            ],
            [
                'category' => 'ground-spices',
                'name' => 'Cumin Powder',
                'sku' => 'MSL-CUM',
                'description' => 'Freshly ground cumin powder with rich aroma. Essential spice for Indian cooking.',
                'short_description' => 'Pure cumin powder',
                'gst_percentage' => 5,
                'variants' => [
                    ['name' => '50g', 'sku' => 'MSL-CUM-50', 'weight' => 50, 'unit' => 'g', 'price' => 40, 'stock' => 90],
                    ['name' => '100g', 'sku' => 'MSL-CUM-100', 'weight' => 100, 'unit' => 'g', 'price' => 75, 'stock' => 100, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-CUM-200', 'weight' => 200, 'unit' => 'g', 'price' => 140, 'stock' => 70],
                    ['name' => '500g', 'sku' => 'MSL-CUM-500', 'weight' => 500, 'unit' => 'g', 'price' => 330, 'stock' => 40],
                ],
            ],
            [
                'category' => 'spice-blends',
                'name' => 'Garam Masala',
                'sku' => 'MSL-GRM',
                'description' => 'Traditional garam masala blend made with premium whole spices. Perfect for curries and biryanis.',
                'short_description' => 'Aromatic spice blend',
                'gst_percentage' => 5,
                'is_featured' => true,
                'variants' => [
                    ['name' => '50g', 'sku' => 'MSL-GRM-50', 'weight' => 50, 'unit' => 'g', 'price' => 55, 'stock' => 80],
                    ['name' => '100g', 'sku' => 'MSL-GRM-100', 'weight' => 100, 'unit' => 'g', 'price' => 100, 'stock' => 100, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-GRM-200', 'weight' => 200, 'unit' => 'g', 'price' => 185, 'stock' => 60],
                    ['name' => '500g', 'sku' => 'MSL-GRM-500', 'weight' => 500, 'unit' => 'g', 'price' => 440, 'stock' => 35],
                ],
            ],
            [
                'category' => 'spice-blends',
                'name' => 'Sambar Powder',
                'sku' => 'MSL-SMB',
                'description' => 'Authentic South Indian sambar powder for delicious sambar. Made with traditional recipe.',
                'short_description' => 'Traditional sambar masala',
                'gst_percentage' => 5,
                'variants' => [
                    ['name' => '100g', 'sku' => 'MSL-SMB-100', 'weight' => 100, 'unit' => 'g', 'price' => 65, 'stock' => 100, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-SMB-200', 'weight' => 200, 'unit' => 'g', 'price' => 120, 'stock' => 80],
                    ['name' => '500g', 'sku' => 'MSL-SMB-500', 'weight' => 500, 'unit' => 'g', 'price' => 280, 'stock' => 50],
                ],
            ],
            [
                'category' => 'spice-blends',
                'name' => 'Rasam Powder',
                'sku' => 'MSL-RSM',
                'description' => 'Traditional rasam powder for authentic South Indian rasam. Tangy and aromatic.',
                'short_description' => 'Authentic rasam masala',
                'gst_percentage' => 5,
                'variants' => [
                    ['name' => '100g', 'sku' => 'MSL-RSM-100', 'weight' => 100, 'unit' => 'g', 'price' => 60, 'stock' => 90, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-RSM-200', 'weight' => 200, 'unit' => 'g', 'price' => 110, 'stock' => 70],
                    ['name' => '500g', 'sku' => 'MSL-RSM-500', 'weight' => 500, 'unit' => 'g', 'price' => 260, 'stock' => 45],
                ],
            ],
            [
                'category' => 'spice-blends',
                'name' => 'Biryani Masala',
                'sku' => 'MSL-BRY',
                'description' => 'Special biryani masala for perfect biryani every time. Aromatic blend of premium spices.',
                'short_description' => 'Premium biryani spice blend',
                'gst_percentage' => 5,
                'is_featured' => true,
                'variants' => [
                    ['name' => '50g', 'sku' => 'MSL-BRY-50', 'weight' => 50, 'unit' => 'g', 'price' => 65, 'stock' => 70],
                    ['name' => '100g', 'sku' => 'MSL-BRY-100', 'weight' => 100, 'unit' => 'g', 'price' => 120, 'stock' => 80, 'is_default' => true],
                    ['name' => '200g', 'sku' => 'MSL-BRY-200', 'weight' => 200, 'unit' => 'g', 'price' => 220, 'stock' => 50],
                ],
            ],
            
            // Oil Products with variants
            [
                'category' => 'coconut-oil',
                'name' => 'Pure Coconut Oil',
                'sku' => 'OIL-COC',
                'description' => 'Cold pressed pure coconut oil, perfect for cooking and beauty care. Made from fresh coconuts.',
                'short_description' => 'Cold pressed coconut oil',
                'gst_percentage' => 5,
                'is_featured' => true,
                'variants' => [
                    ['name' => '100ml', 'sku' => 'OIL-COC-100', 'weight' => 100, 'unit' => 'ml', 'price' => 65, 'stock' => 80],
                    ['name' => '200ml', 'sku' => 'OIL-COC-200', 'weight' => 200, 'unit' => 'ml', 'price' => 120, 'stock' => 100],
                    ['name' => '500ml', 'sku' => 'OIL-COC-500', 'weight' => 500, 'unit' => 'ml', 'price' => 275, 'stock' => 70, 'is_default' => true],
                    ['name' => '1L', 'sku' => 'OIL-COC-1000', 'weight' => 1000, 'unit' => 'ml', 'price' => 520, 'stock' => 50],
                    ['name' => '5L', 'sku' => 'OIL-COC-5000', 'weight' => 5000, 'unit' => 'ml', 'price' => 2450, 'stock' => 20],
                ],
            ],
            [
                'category' => 'coconut-oil',
                'name' => 'Virgin Coconut Oil',
                'sku' => 'OIL-VCO',
                'description' => 'Premium virgin coconut oil extracted from fresh coconuts. Ideal for cooking and skincare.',
                'short_description' => 'Premium virgin coconut oil',
                'gst_percentage' => 5,
                'variants' => [
                    ['name' => '100ml', 'sku' => 'OIL-VCO-100', 'weight' => 100, 'unit' => 'ml', 'price' => 150, 'stock' => 60],
                    ['name' => '250ml', 'sku' => 'OIL-VCO-250', 'weight' => 250, 'unit' => 'ml', 'price' => 350, 'stock' => 50, 'is_default' => true],
                    ['name' => '500ml', 'sku' => 'OIL-VCO-500', 'weight' => 500, 'unit' => 'ml', 'price' => 650, 'stock' => 35],
                ],
            ],
            [
                'category' => 'groundnut-oil',
                'name' => 'Groundnut Oil',
                'sku' => 'OIL-GND',
                'description' => 'Pure cold pressed groundnut oil for healthy cooking. Rich in nutrients and flavor.',
                'short_description' => 'Cold pressed groundnut oil',
                'gst_percentage' => 5,
                'variants' => [
                    ['name' => '200ml', 'sku' => 'OIL-GND-200', 'weight' => 200, 'unit' => 'ml', 'price' => 85, 'stock' => 70],
                    ['name' => '500ml', 'sku' => 'OIL-GND-500', 'weight' => 500, 'unit' => 'ml', 'price' => 195, 'stock' => 60, 'is_default' => true],
                    ['name' => '1L', 'sku' => 'OIL-GND-1000', 'weight' => 1000, 'unit' => 'ml', 'price' => 370, 'stock' => 45],
                    ['name' => '5L', 'sku' => 'OIL-GND-5000', 'weight' => 5000, 'unit' => 'ml', 'price' => 1750, 'stock' => 25],
                ],
            ],
            [
                'category' => 'sesame-oil',
                'name' => 'Gingelly Oil',
                'sku' => 'OIL-SES',
                'description' => 'Traditional gingelly (sesame) oil for authentic taste. Perfect for South Indian cooking.',
                'short_description' => 'Traditional sesame oil',
                'gst_percentage' => 5,
                'variants' => [
                    ['name' => '100ml', 'sku' => 'OIL-SES-100', 'weight' => 100, 'unit' => 'ml', 'price' => 75, 'stock' => 60],
                    ['name' => '200ml', 'sku' => 'OIL-SES-200', 'weight' => 200, 'unit' => 'ml', 'price' => 140, 'stock' => 70],
                    ['name' => '500ml', 'sku' => 'OIL-SES-500', 'weight' => 500, 'unit' => 'ml', 'price' => 330, 'stock' => 50, 'is_default' => true],
                    ['name' => '1L', 'sku' => 'OIL-SES-1000', 'weight' => 1000, 'unit' => 'ml', 'price' => 620, 'stock' => 35],
                ],
            ],
        ];

        // Create products with variants
        foreach ($variantProducts as $productData) {
            $categorySlug = $productData['category'];
            $variants = $productData['variants'];
            unset($productData['category'], $productData['variants']);

            $category = Category::where('slug', $categorySlug)->first();
            
            if ($category) {
                $productData['category_id'] = $category->id;
                $productData['has_variants'] = true;
                // Set base price from first variant
                $productData['price'] = $variants[0]['price'];
                $productData['weight'] = $variants[0]['weight'];
                $productData['unit'] = $variants[0]['unit'];
                $productData['stock_quantity'] = 0; // Stock is managed at variant level
                
                $product = Product::firstOrCreate(
                    ['sku' => $productData['sku']],
                    $productData
                );

                // Create variants
                $sortOrder = 0;
                foreach ($variants as $variantData) {
                    ProductVariant::firstOrCreate(
                        ['sku' => $variantData['sku']],
                        [
                            'product_id' => $product->id,
                            'name' => $variantData['name'],
                            'sku' => $variantData['sku'],
                            'weight' => $variantData['weight'],
                            'unit' => $variantData['unit'],
                            'price' => $variantData['price'],
                            'discount_price' => $variantData['discount_price'] ?? null,
                            'stock_quantity' => $variantData['stock'],
                            'is_default' => $variantData['is_default'] ?? false,
                            'is_active' => true,
                            'sort_order' => $sortOrder++,
                        ]
                    );
                }
            }
        }
    }
}
