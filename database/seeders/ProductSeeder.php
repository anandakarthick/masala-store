<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get categories
        $categories = Category::pluck('id', 'slug')->toArray();

        if (empty($categories)) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Product definitions with base prices (for 100g/100ml)
        $products = $this->getProducts();

        // Powder/Gram variants with multipliers (base is 100g)
        $gramVariants = [
            ['weight' => 100, 'unit' => 'g', 'name' => '100 GM', 'multiplier' => 1.0, 'is_default' => true],
            ['weight' => 250, 'unit' => 'g', 'name' => '250 GM', 'multiplier' => 2.4],  // Slight discount for bulk
            ['weight' => 500, 'unit' => 'g', 'name' => '500 GM', 'multiplier' => 4.5],  // More discount
            ['weight' => 1000, 'unit' => 'g', 'name' => '1 KG', 'multiplier' => 8.5],   // Better value
            ['weight' => 2000, 'unit' => 'g', 'name' => '2 KG', 'multiplier' => 16.0],  // Best value
        ];

        // Oil/ML variants with multipliers (base is 100ml)
        $mlVariants = [
            ['weight' => 100, 'unit' => 'ml', 'name' => '100 ML', 'multiplier' => 1.0, 'is_default' => true],
            ['weight' => 250, 'unit' => 'ml', 'name' => '250 ML', 'multiplier' => 2.4],
            ['weight' => 500, 'unit' => 'ml', 'name' => '500 ML', 'multiplier' => 4.5],
            ['weight' => 1000, 'unit' => 'ml', 'name' => '1 L', 'multiplier' => 8.5],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            $productType = $productData['type'] ?? 'gram'; // 'gram' or 'ml'
            $basePrice = $productData['base_price'];
            $baseDiscountPrice = $productData['base_discount_price'];
            
            unset($productData['category_slug'], $productData['type'], $productData['base_price'], $productData['base_discount_price']);
            
            if (!isset($categories[$categorySlug])) {
                $this->command->warn("Category not found: {$categorySlug}. Skipping product: {$productData['name']}");
                continue;
            }
            
            $productData['category_id'] = $categories[$categorySlug];
            $productData['sku'] = strtoupper(Str::slug($productData['name']));
            $productData['has_variants'] = true;
            
            // Set base product price as the 100g/100ml price
            $productData['price'] = $basePrice;
            $productData['discount_price'] = $baseDiscountPrice;
            
            // Create or update product
            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
            
            $this->command->info("Created product: {$productData['name']}");
            
            // Delete existing variants
            $product->variants()->delete();
            
            // Create variants based on product type
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
                
                $this->command->info("  - Added variant: {$variant['name']} (₹{$variantDiscountPrice})");
            }
        }

        $this->command->info('Products and variants seeded successfully!');
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
                'description' => '<p>Turmeric powder is made by drying and grinding the rhizomes (underground stems) of the turmeric plant. The powder has a warm, slightly peppery taste with a hint of ginger, and it imparts a rich yellow-orange color to food. Its primary active compound, curcumin, is believed to be responsible for many of its health benefits, including its ability to reduce inflammation, support joint health, and boost immunity.</p><p>In addition to its culinary and medicinal uses, turmeric powder is also used in cosmetics and skincare products due to its anti-inflammatory and antimicrobial properties. It can be applied topically for conditions such as acne, eczema, and minor cuts. However, its effectiveness as a medicine is often enhanced when combined with black pepper, which improves the absorption of curcumin.</p>',
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
                'description' => '<p>Experience the authentic flavor of Indian cuisine with our <strong>Homemade Coriander Powder (Dhaniya Powder)</strong>. Made from <strong>100% naturally dried coriander seeds</strong>, this masala is <strong>stone-ground in small batches</strong> to preserve its natural aroma, essential oils, and freshness.</p><p>Our coriander powder gives every dish a <strong>warm, earthy, slightly citrusy taste</strong>, making it a must-have for curries, gravies, sabzis, chutneys, and marinades. Free from <strong>preservatives, artificial colors, and chemicals</strong>, it is a healthier choice for your kitchen.</p><p>Perfect for homes, restaurants, and gifting premium homemade masalas.</p>',
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
                'description' => '<p>Bring richness and aroma to your food with our <strong>Homemade Cumin (Jeera) Powder</strong>. Prepared using <strong>handpicked high-quality cumin seeds</strong>, lightly roasted and finely ground in small batches, our jeera powder delivers a warm, nutty, and earthy flavour that elevates any recipe.</p><p>This cumin powder enhances curries, dals, sabzis, raitas, chaats, and spice mixes with a natural smoky aroma. It contains <strong>no preservatives, no artificial colors, and no added chemicals</strong>, making it a pure and healthy addition to your kitchen.</p><p>Perfect for households, restaurants, cloud kitchens, and premium homemade masala brands.</p>',
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
                'description' => '<p>Add natural color and gentle heat to your dishes with our <strong>Homemade Kashmiri Chilli Powder</strong>. Made from <strong>handpicked elite-quality Kashmiri red chillies</strong>, carefully sun-dried and finely ground, this chilli powder offers a beautiful bright red color without the use of artificial dyes.</p><p>Known for its <strong>mild spiciness, rich aroma, and vibrant natural color</strong>, this powder enhances gravies, curries, tandoori items, biryanis, chutneys, and marinades. Prepared in <strong>small batches</strong> to retain freshness and the authentic Kashmiri flavour, it contains <strong>no preservatives, no chemicals, and no artificial colors</strong>.</p><p>Perfect for homes, restaurants, cloud kitchens, and premium homemade spice brands.</p>',
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
                'description' => '<p>Experience the richness of Indian cooking with our <strong>Homemade Garam Masala</strong>, a perfectly balanced blend of <strong>handpicked whole spices</strong> including cloves, cinnamon, cardamom, cumin, black pepper, bay leaf, nutmeg, and star anise.</p><p>Each spice is <strong>lightly roasted</strong> to enhance aroma and ground in <strong>small batches</strong> to retain freshness, essential oils, and traditional taste. This aromatic blend adds <strong>warmth, depth, and complexity</strong> to curries, gravies, biryanis, sabzis, dals, marinades, paneer dishes, and non-veg recipes.</p><p>Our garam masala contains <strong>no preservatives, no artificial colors, no fillers, and no chemicals</strong> — only pure, natural, authentic flavours. Perfect for home kitchens, restaurants, and premium homemade masala brands.</p>',
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
                'description' => '<p>Indulge in the rich aroma and flavour of our <strong>Homemade Cardamom (Elaichi) Powder</strong>, prepared from <strong>handpicked premium green cardamom pods</strong>. Each pod is carefully cleaned, sun-dried, and finely ground in small batches to retain its natural fragrance, essential oils, and authentic taste.</p><p>This elaichi powder is known for its <strong>sweet, floral aroma</strong> and is ideal for:</p><ul><li>Indian sweets & desserts (kheer, payasam, ladoo, halwa)</li><li>Masala tea & milk</li><li>Biryani & pulao</li><li>Cakes, cookies & baking</li><li>Everyday cooking & flavour enhancement</li></ul><p>Made without <strong>preservatives, colour, or additives</strong>, it is a pure, natural, and premium-quality spice for your kitchen.</p><p>Perfect for homes, cafés, bakeries, sweet shops, and homemade masala brands.</p>',
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
                'base_price' => 20.00,  // Base price for 100g (100/500 * 100 = 20)
                'base_discount_price' => 18.00,
                'short_description' => 'Nutritious Homemade Ragi Powder made from premium finger millet. Freshly ground, 100% natural, rich in calcium, fiber, and iron — ideal for porridge, health drinks & baby food.',
                'description' => '<p>Boost your daily nutrition with our <strong>Homemade Ragi Powder</strong>, made from <strong>carefully cleaned and sun-dried finger millet (ragi)</strong>. Finely ground in small batches, this powder retains its natural nutrients, fibre, and earthy flavour.</p><p>Ragi is known as a <strong>superfood</strong> rich in:</p><ul><li><strong>Calcium</strong> (great for bone health)</li><li><strong>Dietary fibre</strong> (good digestion)</li><li><strong>Iron & minerals</strong></li><li><strong>Plant-based protein</strong></li></ul><p>It is ideal for preparing:</p><ul><li>Ragi porridge</li><li>Ragi malt</li><li>Baby/infant food</li><li>Rotis & dosa batter</li><li>Healthy drinks</li><li>Ragi laddoo & baked goods</li></ul><p>Completely <strong>chemical-free, preservative-free, and colour-free</strong>, making it a perfect choice for health-conscious families.</p><p>Ideal for households, health-food brands, organic stores, and homemade product sellers.</p>',
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
                'base_price' => 28.00,  // Base price for 100g
                'base_discount_price' => 26.00,
                'short_description' => 'Pure Homemade Black Urad Dal (Kali Urad) Powder made from premium whole black gram. Freshly roasted, finely ground, protein-rich, and 100% natural.',
                'description' => '<p>Our <strong>Homemade Black Urad Dal (Kali Urad) Powder</strong> is prepared from <strong>high-quality whole black urad (black gram)</strong>, cleaned, lightly roasted, and finely ground to maintain its natural aroma and nutrition.</p><p>Black urad is packed with:</p><ul><li><strong>Plant-based protein</strong></li><li><strong>Dietary fibre</strong></li><li><strong>Calcium & Iron</strong></li><li><strong>Healthy complex carbs</strong></li></ul><p>This nutritious powder is commonly used in:</p><ul><li>Idli & dosa batter (for softness & texture)</li><li>Papad, vada, murukku, karasev</li><li>Ayurvedic health mixes</li><li>Thickening curries & gravies</li><li>High-protein porridge</li><li>Traditional South Indian snacks</li></ul><p>Completely <strong>free from chemicals, preservatives, and additives</strong>, it is a pure and healthy homemade ingredient suitable for daily cooking.</p><p>Perfect for home kitchens, organic stores, health brands, and homemade food sellers.</p>',
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
                'short_description' => '100% Homemade Herbal Bath Powder made with natural herbs, grains, and flowers. Deep cleanses, brightens skin, controls body odor, and keeps skin soft & smooth. Chemical-free alternative to soap.',
                'description' => '<p>Experience pure, traditional skincare with our <strong>Homemade Herbal Bath Powder (Ubtan Bath Powder)</strong>, crafted using handpicked herbs and natural ingredients. Made in small batches, this powder is completely free from soap, parabens, sulfates, and artificial fragrance.</p><p>Key Ingredients:</p><ul><li>Green gram (Moong dal)</li><li>Bengal gram (Besan)</li><li>Wild turmeric (Kasthuri Manjal)</li><li>Rose petals</li><li>Neem leaves</li><li>Avarampoo</li><li>Multani Mitti</li><li>Sandalwood powder</li><li>Vetiver</li><li>Orange peel</li><li>Fenugreek</li></ul><p>Benefits:</p><ul><li>Deep cleanses & removes dirt</li><li>Brightens skin tone naturally</li><li>Controls body odour</li><li>Reduces tan & pigmentation</li><li>Helps with acne, rashes, pimples</li><li>Makes skin soft, smooth, and glowing</li><li>Suitable for <strong>sensitive skin</strong></li><li>100% herbal alternative to chemical soaps</li></ul><p>How to use: Mix powder with water / rose water / milk / curd / aloe gel. Apply to skin, massage gently, and rinse off. Safe for <strong>daily use</strong>.</p><p>Suitable for Men, Women, Kids and all skin types.</p>',
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
                'type' => 'ml',  // ML variants
                'base_price' => 100.00,
                'base_discount_price' => 90.00,
                'short_description' => 'Herbal Knee Pain Relief Oil made with Ayurvedic ingredients. Helps reduce joint pain, inflammation, swelling & stiffness. 100% natural, chemical-free, and fast-absorbing.',
                'description' => '<p>Experience natural pain relief with our <strong>Homemade Herbal Knee Pain Relief Oil</strong>, formulated using traditional Ayurvedic herbs.</p><p>This oil is crafted using a blend of natural oils and medicinal herbs that help:</p><ul><li>Reduce joint pain and stiffness</li><li>Decrease inflammation and swelling</li><li>Improve mobility and flexibility</li><li>Provide soothing warmth to affected areas</li><li>Support joint health naturally</li></ul><p>Made with 100% natural ingredients including sesame oil, castor oil, eucalyptus, camphor, and other Ayurvedic herbs. Contains no chemicals, parabens, or synthetic fragrances.</p><p>Directions: Apply oil to affected area and massage gently for 5-10 minutes. Use 2-3 times daily for best results.</p><p>Suitable for knee pain, back pain, joint pain, and muscle soreness.</p>',
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
                'type' => 'ml',  // ML variants
                'base_price' => 260.00,
                'base_discount_price' => 250.00,
                'short_description' => 'Herbal Hair Growth Oil made with natural oils and Ayurvedic herbs. Reduces hair fall, boosts hair growth, strengthens roots, and promotes thicker, healthier hair. 100% chemical-free.',
                'description' => '<p>Give your hair the nourishment it deserves with our <strong>Homemade Herbal Hair Growth Oil</strong>, crafted using a powerful blend of traditional Ayurvedic herbs and pure natural oils. This oil is prepared in small batches to maintain freshness and maximum healing properties.</p><h4>Key Ingredients:</h4><ul><li>Coconut oil</li><li>Castor oil</li><li>Sesame oil</li><li>Almond oil</li><li>Hibiscus flowers & leaves</li><li>Amla (Indian gooseberry)</li><li>Bhringraj</li><li>Neem</li></ul><h4>Benefits:</h4><ul><li>Reduces <strong>hair fall & breakage</strong></li><li>Promotes <strong>new hair growth</strong></li><li>Helps grow <strong>thicker & stronger hair</strong></li><li>Strengthens hair roots</li><li>Prevents <strong>dandruff & itchy scalp</strong></li><li>Repairs <strong>dry, damaged, and frizzy hair</strong></li><li>Boosts scalp circulation</li><li>Adds natural shine & softness</li></ul><p>Suitable for <strong>men, women, and kids</strong>.</p><p><strong>How to use:</strong> Apply oil to scalp and hair, massage gently for 5-10 minutes. Leave overnight or at least 1 hour before washing. Use 3-4 times a week for best results.</p>',
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
