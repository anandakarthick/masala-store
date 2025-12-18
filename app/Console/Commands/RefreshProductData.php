<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RefreshProductData extends Command
{
    protected $signature = 'data:refresh-products {--force : Force refresh without confirmation}';
    protected $description = 'Refresh categories and products with new SV Masala data';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will delete all existing categories and products. Continue?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('Refreshing product data...');

        // Delete existing products first (due to foreign key)
        $this->info('Deleting existing product variants...');
        ProductVariant::query()->delete();
        
        $this->info('Deleting existing products...');
        Product::query()->delete();
        
        // Delete existing categories
        $this->info('Deleting existing categories...');
        Category::query()->delete();

        // Create categories
        $this->info('Creating new categories...');
        $categories = $this->createCategories();

        // Create products with variants
        $this->info('Creating new products with variants...');
        $this->createProducts($categories);

        $this->info('');
        $this->info('✓ Product data refreshed successfully!');
        $this->info('  Categories: ' . Category::count());
        $this->info('  Products: ' . Product::count());
        $this->info('  Variants: ' . ProductVariant::count());
    }

    private function createCategories(): array
    {
        $categoriesData = [
            [
                'name' => 'Spices & Masalas',
                'slug' => 'spices-masalas',
                'description' => 'Premium quality spices and masala powders for authentic Indian cooking',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Health & Millet Products',
                'slug' => 'health-millet-products',
                'description' => 'Nutritious millet-based products and health foods',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Baby Care',
                'slug' => 'baby-care',
                'description' => 'Natural and safe baby care products',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Ayurvedic & Wellness',
                'slug' => 'ayurvedic-wellness',
                'description' => 'Traditional Ayurvedic and wellness products',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $category = Category::create($data);
            $categories[$data['slug']] = $category->id;
            $this->info("  ✓ Created category: {$data['name']}");
        }

        return $categories;
    }

    private function createProducts(array $categories): void
    {
        // Powder/Gram variants with multipliers (base is 100g)
        $gramVariants = [
            ['weight' => 100, 'unit' => 'g', 'name' => '100 GM', 'multiplier' => 1.0, 'is_default' => true],
            ['weight' => 250, 'unit' => 'g', 'name' => '250 GM', 'multiplier' => 2.4],
            ['weight' => 500, 'unit' => 'g', 'name' => '500 GM', 'multiplier' => 4.5],
            ['weight' => 1000, 'unit' => 'g', 'name' => '1 KG', 'multiplier' => 8.5],
            ['weight' => 2000, 'unit' => 'g', 'name' => '2 KG', 'multiplier' => 16.0],
        ];

        // Oil/ML variants with multipliers (base is 100ml)
        $mlVariants = [
            ['weight' => 100, 'unit' => 'ml', 'name' => '100 ML', 'multiplier' => 1.0, 'is_default' => true],
            ['weight' => 250, 'unit' => 'ml', 'name' => '250 ML', 'multiplier' => 2.4],
            ['weight' => 500, 'unit' => 'ml', 'name' => '500 ML', 'multiplier' => 4.5],
            ['weight' => 1000, 'unit' => 'ml', 'name' => '1 L', 'multiplier' => 8.5],
        ];

        $products = $this->getProducts();

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            $productType = $productData['type'] ?? 'gram';
            $basePrice = $productData['base_price'];
            $baseDiscountPrice = $productData['base_discount_price'];
            
            unset($productData['category_slug'], $productData['type'], $productData['base_price'], $productData['base_discount_price']);
            
            if (!isset($categories[$categorySlug])) {
                $this->warn("  ✗ Category not found: {$categorySlug}. Skipping: {$productData['name']}");
                continue;
            }
            
            $productData['category_id'] = $categories[$categorySlug];
            $productData['sku'] = strtoupper(Str::slug($productData['name']));
            $productData['has_variants'] = true;
            $productData['price'] = $basePrice;
            $productData['discount_price'] = $baseDiscountPrice;
            
            $product = Product::create($productData);
            $this->info("  ✓ Created product: {$productData['name']}");
            
            // Create variants
            $variants = ($productType === 'ml') ? $mlVariants : $gramVariants;
            
            foreach ($variants as $index => $variant) {
                $variantPrice = round($basePrice * $variant['multiplier'], 2);
                $variantDiscountPrice = round($baseDiscountPrice * $variant['multiplier'], 2);
                
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variant['name'],
                    'sku' => $product->sku . '-' . strtoupper(str_replace(' ', '', $variant['name'])),
                    'weight' => $variant['weight'],
                    'unit' => $variant['unit'],
                    'price' => $variantPrice,
                    'discount_price' => $variantDiscountPrice,
                    'stock_quantity' => 100,
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'is_default' => $variant['is_default'] ?? false,
                    'sort_order' => $index,
                ]);
                
                $this->line("      - {$variant['name']}: ₹{$variantDiscountPrice}");
            }
        }
    }

    private function getProducts(): array
    {
        return [
            // ===== Spices & Masalas =====
            [
                'category_slug' => 'spices-masalas',
                'name' => 'Turmeric Powder',
                'slug' => 'turmeric-powder',
                'type' => 'gram',
                'base_price' => 60.00,
                'base_discount_price' => 50.00,
                'short_description' => 'Turmeric powder is a bright yellow spice derived from the root of the Curcuma longa plant, commonly used in cooking, particularly in South Asian, Middle Eastern, and Southeast Asian cuisines.',
                'description' => '<p>Turmeric powder is made by drying and grinding the rhizomes (underground stems) of the turmeric plant. The powder has a warm, slightly peppery taste with a hint of ginger, and it imparts a rich yellow-orange color to food. Its primary active compound, curcumin, is believed to be responsible for many of its health benefits, including its ability to reduce inflammation, support joint health, and boost immunity.</p><p>In addition to its culinary and medicinal uses, turmeric powder is also used in cosmetics and skincare products due to its anti-inflammatory and antimicrobial properties.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'spices-masalas',
                'name' => 'Coriander Powder',
                'slug' => 'coriander-powder',
                'type' => 'gram',
                'base_price' => 60.00,
                'base_discount_price' => 50.00,
                'short_description' => 'Pure Homemade Coriander Powder made from handpicked premium coriander seeds. Freshly ground, chemical-free, aromatic, and perfect for enhancing everyday Indian cooking.',
                'description' => '<p>Experience the authentic flavor of Indian cuisine with our <strong>Homemade Coriander Powder (Dhaniya Powder)</strong>. Made from <strong>100% naturally dried coriander seeds</strong>, this masala is <strong>stone-ground in small batches</strong> to preserve its natural aroma, essential oils, and freshness.</p><p>Our coriander powder gives every dish a <strong>warm, earthy, slightly citrusy taste</strong>, making it a must-have for curries, gravies, sabzis, chutneys, and marinades.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'spices-masalas',
                'name' => 'Cumin Powder',
                'slug' => 'cumin-powder',
                'type' => 'gram',
                'base_price' => 70.00,
                'base_discount_price' => 60.00,
                'short_description' => 'Pure Homemade Cumin Powder made from premium hand-roasted cumin seeds. Aromatic, flavorful, chemical-free, and perfect for enhancing taste in every Indian dish.',
                'description' => '<p>Bring richness and aroma to your food with our <strong>Homemade Cumin (Jeera) Powder</strong>. Prepared using <strong>handpicked high-quality cumin seeds</strong>, lightly roasted and finely ground in small batches, our jeera powder delivers a warm, nutty, and earthy flavour that elevates any recipe.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'spices-masalas',
                'name' => 'Kashmiri Chilli Powder',
                'slug' => 'kashmiri-chilli-powder',
                'type' => 'gram',
                'base_price' => 100.00,
                'base_discount_price' => 90.00,
                'short_description' => 'Pure Homemade Kashmiri Chilli Powder made from premium sun-dried Kashmiri chillies. Naturally bright red color, mild heat, and rich aroma — perfect for vibrant and flavorful cooking.',
                'description' => '<p>Add natural color and gentle heat to your dishes with our <strong>Homemade Kashmiri Chilli Powder</strong>. Made from <strong>handpicked elite-quality Kashmiri red chillies</strong>, carefully sun-dried and finely ground, this chilli powder offers a beautiful bright red color without the use of artificial dyes.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'spices-masalas',
                'name' => 'Garam Masala',
                'slug' => 'garam-masala',
                'type' => 'gram',
                'base_price' => 75.00,
                'base_discount_price' => 65.00,
                'short_description' => 'Authentic Homemade Garam Masala made with premium whole spices, roasted and ground in small batches for maximum aroma, purity, and traditional Indian flavor.',
                'description' => '<p>Experience the richness of Indian cooking with our <strong>Homemade Garam Masala</strong>, a perfectly balanced blend of <strong>handpicked whole spices</strong> including cloves, cinnamon, cardamom, cumin, black pepper, bay leaf, nutmeg, and star anise.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'spices-masalas',
                'name' => 'Cardamom Powder',
                'slug' => 'cardamom-powder',
                'type' => 'gram',
                'base_price' => 170.00,
                'base_discount_price' => 160.00,
                'short_description' => 'Pure Homemade Cardamom Powder made from high-quality green elaichi. Freshly ground, intensely aromatic, chemical-free, and perfect for sweets, desserts, tea, and cooking.',
                'description' => '<p>Indulge in the rich aroma and flavour of our <strong>Homemade Cardamom (Elaichi) Powder</strong>, prepared from <strong>handpicked premium green cardamom pods</strong>. Each pod is carefully cleaned, sun-dried, and finely ground in small batches to retain its natural fragrance, essential oils, and authentic taste.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            
            // ===== Health & Millet Products =====
            [
                'category_slug' => 'health-millet-products',
                'name' => 'Ragi Powder',
                'slug' => 'ragi-powder',
                'type' => 'gram',
                'base_price' => 20.00,
                'base_discount_price' => 18.00,
                'short_description' => 'Nutritious Homemade Ragi Powder made from premium finger millet. Freshly ground, 100% natural, rich in calcium, fiber, and iron — ideal for porridge, health drinks & baby food.',
                'description' => '<p>Boost your daily nutrition with our <strong>Homemade Ragi Powder</strong>, made from <strong>carefully cleaned and sun-dried finger millet (ragi)</strong>. Finely ground in small batches, this powder retains its natural nutrients, fibre, and earthy flavour.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'health-millet-products',
                'name' => 'Black Urad Dal Powder',
                'slug' => 'black-urad-dal-powder',
                'type' => 'gram',
                'base_price' => 28.00,
                'base_discount_price' => 26.00,
                'short_description' => 'Pure Homemade Black Urad Dal (Kali Urad) Powder made from premium whole black gram. Freshly roasted, finely ground, protein-rich, and 100% natural.',
                'description' => '<p>Our <strong>Homemade Black Urad Dal (Kali Urad) Powder</strong> is prepared from <strong>high-quality whole black urad (black gram)</strong>, cleaned, lightly roasted, and finely ground to maintain its natural aroma and nutrition.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            
            // ===== Baby Care =====
            [
                'category_slug' => 'baby-care',
                'name' => 'Bath Powder',
                'slug' => 'bath-powder',
                'type' => 'gram',
                'base_price' => 80.00,
                'base_discount_price' => 70.00,
                'short_description' => '100% Homemade Herbal Bath Powder made with natural herbs, grains, and flowers. Deep cleanses, brightens skin, controls body odor, and keeps skin soft & smooth.',
                'description' => '<p>Experience pure, traditional skincare with our <strong>Homemade Herbal Bath Powder (Ubtan Bath Powder)</strong>, crafted using handpicked herbs and natural ingredients. Made in small batches, this powder is completely free from soap, parabens, sulfates, and artificial fragrance.</p>',
                'weight' => '100',
                'unit' => 'g',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            
            // ===== Ayurvedic & Wellness (ML products) =====
            [
                'category_slug' => 'ayurvedic-wellness',
                'name' => 'Knee Pain Relief Oil',
                'slug' => 'knee-pain-relief-oil',
                'type' => 'ml',
                'base_price' => 100.00,
                'base_discount_price' => 90.00,
                'short_description' => 'Herbal Knee Pain Relief Oil made with Ayurvedic ingredients. Helps reduce joint pain, inflammation, swelling & stiffness. 100% natural, chemical-free, and fast-absorbing.',
                'description' => '<p>Experience natural pain relief with our <strong>Homemade Herbal Knee Pain Relief Oil</strong>, formulated using traditional Ayurvedic herbs. This oil is crafted using a blend of natural oils and medicinal herbs.</p>',
                'weight' => '100',
                'unit' => 'ml',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_slug' => 'ayurvedic-wellness',
                'name' => 'Hair Growth Oil',
                'slug' => 'hair-growth-oil',
                'type' => 'ml',
                'base_price' => 260.00,
                'base_discount_price' => 250.00,
                'short_description' => 'Herbal Hair Growth Oil made with natural oils and Ayurvedic herbs. Reduces hair fall, boosts hair growth, strengthens roots, and promotes thicker, healthier hair.',
                'description' => '<p>Give your hair the nourishment it deserves with our <strong>Homemade Herbal Hair Growth Oil</strong>, crafted using a powerful blend of traditional Ayurvedic herbs and pure natural oils.</p>',
                'weight' => '100',
                'unit' => 'ml',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        return $products;
    }
}
