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

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            $productType = $productData['type'] ?? 'gram';
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
            $productData['price'] = $basePrice;
            $productData['discount_price'] = $baseDiscountPrice;
            
            // Generate SEO meta fields if not set
            if (empty($productData['meta_title'])) {
                $productData['meta_title'] = 'Buy ' . $productData['name'] . ' Online | 100% Pure & Natural';
            }
            if (empty($productData['meta_description'])) {
                $productData['meta_description'] = 'Shop ' . $productData['name'] . ' - ' . Str::limit($productData['short_description'], 120) . ' Free delivery above ₹500.';
            }
            
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
                'description' => 'Turmeric powder is made by drying and grinding the rhizomes (underground stems) of the turmeric plant. The powder has a warm, slightly peppery taste with a hint of ginger, and it imparts a rich yellow-orange color to food. Its primary active compound, curcumin, is believed to be responsible for many of its health benefits, including its ability to reduce inflammation, support joint health, and boost immunity.

In addition to its culinary and medicinal uses, turmeric powder is also used in cosmetics and skincare products due to its anti-inflammatory and antimicrobial properties. It can be applied topically for conditions such as acne, eczema, and minor cuts. However, its effectiveness as a medicine is often enhanced when combined with black pepper, which improves the absorption of curcumin.

Key Benefits:
• Anti-inflammatory and antioxidant properties
• Supports joint health and immunity
• Natural food coloring agent
• Used in Ayurvedic medicine for centuries
• Chemical-free and 100% natural',
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
                'description' => 'Experience the authentic flavor of Indian cuisine with our Homemade Coriander Powder (Dhaniya Powder). Made from 100% naturally dried coriander seeds, this masala is stone-ground in small batches to preserve its natural aroma, essential oils, and freshness.

Our coriander powder gives every dish a warm, earthy, slightly citrusy taste, making it a must-have for curries, gravies, sabzis, chutneys, and marinades. Free from preservatives, artificial colors, and chemicals, it is a healthier choice for your kitchen.

Key Benefits:
• Stone-ground in small batches for freshness
• Warm, earthy, slightly citrusy taste
• No preservatives or artificial colors
• Perfect for curries, gravies, sabzis, chutneys
• 100% natural and chemical-free

Perfect for homes, restaurants, and gifting premium homemade masalas.',
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
                'description' => 'Bring richness and aroma to your food with our Homemade Cumin (Jeera) Powder. Prepared using handpicked high-quality cumin seeds, lightly roasted and finely ground in small batches, our jeera powder delivers a warm, nutty, and earthy flavour that elevates any recipe.

This cumin powder enhances curries, dals, sabzis, raitas, chaats, and spice mixes with a natural smoky aroma. It contains no preservatives, no artificial colors, and no added chemicals, making it a pure and healthy addition to your kitchen.

Key Benefits:
• Hand-roasted for enhanced aroma
• Warm, nutty, and earthy flavour
• No preservatives or artificial colors
• Perfect for curries, dals, raitas, chaats
• 100% natural and chemical-free

Perfect for households, restaurants, cloud kitchens, and premium homemade masala brands.',
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
                'description' => 'Add natural color and gentle heat to your dishes with our Homemade Kashmiri Chilli Powder. Made from handpicked elite-quality Kashmiri red chillies, carefully sun-dried and finely ground, this chilli powder offers a beautiful bright red color without the use of artificial dyes.

Known for its mild spiciness, rich aroma, and vibrant natural color, this powder enhances gravies, curries, tandoori items, biryanis, chutneys, and marinades. Prepared in small batches to retain freshness and the authentic Kashmiri flavour, it contains no preservatives, no chemicals, and no artificial colors.

Key Benefits:
• Naturally bright red color
• Mild spiciness with rich aroma
• No artificial dyes or colors
• Perfect for tandoori, biryani, curries
• 100% natural sun-dried chillies

Perfect for homes, restaurants, cloud kitchens, and premium homemade spice brands.',
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
                'description' => 'Experience the richness of Indian cooking with our Homemade Garam Masala, a perfectly balanced blend of handpicked whole spices including cloves, cinnamon, cardamom, cumin, black pepper, bay leaf, nutmeg, and star anise.

Each spice is lightly roasted to enhance aroma and ground in small batches to retain freshness, essential oils, and traditional taste. This aromatic blend adds warmth, depth, and complexity to curries, gravies, biryanis, sabzis, dals, marinades, paneer dishes, and non-veg recipes.

Key Ingredients:
• Cloves, Cinnamon, Cardamom
• Cumin, Black Pepper, Bay Leaf
• Nutmeg, Star Anise

Key Benefits:
• Perfectly balanced spice blend
• Lightly roasted for enhanced aroma
• No preservatives, fillers, or chemicals
• Adds warmth and depth to dishes
• 100% natural and authentic

Perfect for home kitchens, restaurants, and premium homemade masala brands.',
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
                'description' => 'Indulge in the rich aroma and flavour of our Homemade Cardamom (Elaichi) Powder, prepared from handpicked premium green cardamom pods. Each pod is carefully cleaned, sun-dried, and finely ground in small batches to retain its natural fragrance, essential oils, and authentic taste.

This elaichi powder is known for its sweet, floral aroma and is ideal for:
• Indian sweets & desserts (kheer, payasam, ladoo, halwa)
• Masala tea & milk
• Biryani & pulao
• Cakes, cookies & baking
• Everyday cooking & flavour enhancement

Key Benefits:
• Sweet, floral aroma
• Freshly ground in small batches
• No preservatives, colour, or additives
• Premium quality green cardamom
• 100% natural and pure

Perfect for homes, cafés, bakeries, sweet shops, and homemade masala brands.',
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
                'description' => 'Boost your daily nutrition with our Homemade Ragi Powder, made from carefully cleaned and sun-dried finger millet (ragi). Finely ground in small batches, this powder retains its natural nutrients, fibre, and earthy flavour.

Ragi is known as a superfood rich in:
• Calcium (great for bone health)
• Dietary fibre (good digestion)
• Iron & minerals
• Plant-based protein

Ideal for preparing:
• Ragi porridge
• Ragi malt
• Baby/infant food
• Rotis & dosa batter
• Healthy drinks
• Ragi laddoo & baked goods

Key Benefits:
• Rich in calcium, iron, and fiber
• Excellent for bone health
• Suitable for babies and adults
• Chemical-free and preservative-free
• 100% natural finger millet

Ideal for households, health-food brands, organic stores, and homemade product sellers.',
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
                'description' => 'Our Homemade Black Urad Dal (Kali Urad) Powder is prepared from high-quality whole black urad (black gram), cleaned, lightly roasted, and finely ground to maintain its natural aroma and nutrition.

Black urad is packed with:
• Plant-based protein
• Dietary fibre
• Calcium & Iron
• Healthy complex carbs

This nutritious powder is commonly used in:
• Idli & dosa batter (for softness & texture)
• Papad, vada, murukku, karasev
• Ayurvedic health mixes
• Thickening curries & gravies
• High-protein porridge
• Traditional South Indian snacks

Key Benefits:
• High in plant-based protein
• Rich in calcium and iron
• Improves texture in batters
• Chemical-free and preservative-free
• 100% natural black gram

Perfect for home kitchens, organic stores, health brands, and homemade food sellers.',
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
                'description' => 'Experience pure, traditional skincare with our Homemade Herbal Bath Powder (Ubtan Bath Powder), crafted using handpicked herbs and natural ingredients. Made in small batches, this powder is completely free from soap, parabens, sulfates, and artificial fragrance.

Key Ingredients:
• Green gram (Moong dal)
• Bengal gram (Besan)
• Wild turmeric (Kasthuri Manjal)
• Rose petals
• Neem leaves
• Avarampoo
• Multani Mitti
• Sandalwood powder
• Vetiver
• Orange peel
• Fenugreek

Key Benefits:
• Deep cleanses & removes dirt
• Brightens skin tone naturally
• Controls body odour
• Reduces tan & pigmentation
• Helps with acne, rashes, pimples
• Makes skin soft, smooth, and glowing
• Suitable for sensitive skin
• 100% herbal alternative to chemical soaps

How to use: Mix powder with water / rose water / milk / curd / aloe gel. Apply to skin, massage gently, and rinse off. Safe for daily use.

Suitable for Men, Women, Kids and all skin types.',
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
                'description' => 'Experience natural pain relief with our Homemade Herbal Knee Pain Relief Oil, formulated using traditional Ayurvedic herbs.

This oil is crafted using a blend of natural oils and medicinal herbs that help:
• Reduce joint pain and stiffness
• Decrease inflammation and swelling
• Improve mobility and flexibility
• Provide soothing warmth to affected areas
• Support joint health naturally

Key Ingredients:
• Sesame oil
• Castor oil
• Eucalyptus
• Camphor
• Other Ayurvedic herbs

Key Benefits:
• Fast-absorbing formula
• Provides soothing warmth
• Improves joint mobility
• 100% natural ingredients
• No chemicals or parabens

Directions: Apply oil to affected area and massage gently for 5-10 minutes. Use 2-3 times daily for best results.

Suitable for knee pain, back pain, joint pain, and muscle soreness.',
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
                'short_description' => 'Herbal Hair Growth Oil made with natural oils and Ayurvedic herbs. Reduces hair fall, boosts hair growth, strengthens roots, and promotes thicker, healthier hair. 100% chemical-free.',
                'description' => 'Give your hair the nourishment it deserves with our Homemade Herbal Hair Growth Oil, crafted using a powerful blend of traditional Ayurvedic herbs and pure natural oils. This oil is prepared in small batches to maintain freshness and maximum healing properties.

Key Ingredients:
• Coconut oil
• Castor oil
• Sesame oil
• Almond oil
• Hibiscus flowers & leaves
• Amla (Indian gooseberry)
• Bhringraj
• Neem

Key Benefits:
• Reduces hair fall & breakage
• Promotes new hair growth
• Helps grow thicker & stronger hair
• Strengthens hair roots
• Prevents dandruff & itchy scalp
• Repairs dry, damaged, and frizzy hair
• Boosts scalp circulation
• Adds natural shine & softness

Suitable for men, women, and kids.

How to use: Apply oil to scalp and hair, massage gently for 5-10 minutes. Leave overnight or at least 1 hour before washing. Use 3-4 times a week for best results.',
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
