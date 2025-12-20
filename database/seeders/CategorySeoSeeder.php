<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Updates existing categories with SEO meta titles and descriptions
     */
    public function run(): void
    {
        $categorySeoData = [
            'spices-masalas' => [
                'name' => 'Spices & Masalas',
                'meta_title' => 'Buy Homemade Spices & Masalas Online | Pure Indian Masala Powders',
                'meta_description' => 'Buy pure homemade spices & masalas online. Premium quality turmeric, coriander, cumin, garam masala, chilli powder & more. 100% natural, no chemicals. Free delivery above ₹500.',
                'description' => 'Discover our authentic collection of homemade Indian spices and masala powders. Each spice is carefully sourced and freshly ground to preserve its natural aroma, flavor, and health benefits. From everyday essentials like turmeric and coriander to aromatic garam masala and specialty blends, our spices bring authentic taste to your kitchen. All products are 100% pure with no added preservatives or artificial colors.',
                // 'image' => 'categories/spices-masalas.jpg',
            ],
            'health-millet-products' => [
                'name' => 'Health & Millet Products',
                'meta_title' => 'Buy Healthy Millet Products Online | Organic Millets & Health Foods',
                'meta_description' => 'Shop premium millet products & health foods online. Ragi, jowar, bajra, foxtail millet & more. Rich in nutrients, gluten-free, diabetic-friendly. Free delivery above ₹500.',
                'description' => 'Explore our range of nutritious millet products and health foods. Millets are ancient grains packed with essential nutrients, fiber, and minerals. Perfect for health-conscious individuals, diabetics, and those seeking gluten-free alternatives. Our collection includes ragi (finger millet), jowar (sorghum), bajra (pearl millet), foxtail millet, and various millet-based ready mixes. Embrace a healthier lifestyle with our natural, chemical-free products.',
                // 'image' => 'categories/health-millet.jpg',
            ],
            'baby-care' => [
                'name' => 'Baby Care',
                'meta_title' => 'Buy Natural Baby Care Products Online | Homemade Baby Foods & Oils',
                'meta_description' => 'Safe & natural baby care products. Homemade baby foods, massage oils, bath powders & more. 100% chemical-free, gentle for babies. Free delivery above ₹500.',
                'description' => 'Nurture your little ones with our range of natural and homemade baby care products. We understand that babies deserve the purest and safest products. Our collection includes nutritious homemade baby foods, gentle massage oils, natural bath powders, and herbal remedies specially formulated for infants and toddlers. All products are made with carefully selected natural ingredients, free from harmful chemicals, preservatives, and artificial additives.',
                // 'image' => 'categories/baby-care.jpg',
            ],
            'ayurvedic-wellness' => [
                'name' => 'Ayurvedic & Wellness',
                'meta_title' => 'Buy Ayurvedic Products Online | Natural Wellness & Herbal Products',
                'meta_description' => 'Shop authentic Ayurvedic & wellness products. Herbal oils, natural remedies, immunity boosters & traditional medicines. 100% natural. Free delivery above ₹500.',
                'description' => 'Discover the healing power of Ayurveda with our authentic wellness products. Rooted in ancient Indian wisdom, our Ayurvedic collection includes herbal oils for hair and skin, natural immunity boosters, digestive aids, and traditional remedies for various health concerns. Each product is crafted using time-tested formulations and pure herbal ingredients. Experience holistic wellness the natural way with our chemical-free, preservative-free Ayurvedic products.',
                // 'image' => 'categories/ayurvedic-wellness.jpg',
            ],
        ];

        foreach ($categorySeoData as $slug => $data) {
            // Try to find by slug first, then by name
            $category = Category::where('slug', $slug)->first();
            
            if (!$category) {
                // Try alternative slugs
                $alternativeSlugs = [
                    'spices-masalas' => ['spices', 'masalas', 'spices-and-masalas'],
                    'health-millet-products' => ['health-products', 'millet-products', 'health-and-millet-products', 'millets'],
                    'baby-care' => ['baby', 'baby-products', 'babycare'],
                    'ayurvedic-wellness' => ['ayurvedic', 'wellness', 'ayurvedic-and-wellness', 'herbal'],
                ];
                
                if (isset($alternativeSlugs[$slug])) {
                    foreach ($alternativeSlugs[$slug] as $altSlug) {
                        $category = Category::where('slug', $altSlug)->first();
                        if ($category) break;
                    }
                }
            }
            
            if (!$category) {
                // Try to find by name
                $category = Category::where('name', 'LIKE', '%' . explode(' ', $data['name'])[0] . '%')->first();
            }

            if ($category) {
                $category->update([
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'description' => $data['description'],
                ]);
                
                $this->command->info("✓ Updated SEO for: {$category->name}");
            } else {
                // Create new category if not exists
                $newCategory = Category::create([
                    'name' => $data['name'],
                    'slug' => $slug,
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'sort_order' => 0,
                ]);
                
                $this->command->info("✓ Created new category: {$data['name']}");
            }
        }

        $this->command->info('');
        $this->command->info('Category SEO data has been updated successfully!');
    }
}
