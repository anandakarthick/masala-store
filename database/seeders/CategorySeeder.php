<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categories with SEO meta fields
        $categories = [
            [
                'name' => 'Spices & Masalas',
                'slug' => 'spices-masalas',
                'description' => 'Discover our range of premium quality homemade spices and masala powders. We offer pure turmeric powder, coriander powder, cumin powder, garam masala, Kashmiri chilli powder, and cardamom powder. All our spices are freshly ground in small batches, 100% natural, and free from chemicals and preservatives. Perfect for authentic Indian cooking.',
                'meta_title' => 'Buy Homemade Spices & Masala Powders Online | Pure & Natural',
                'meta_description' => 'Shop premium homemade spices and masala powders - Turmeric, Coriander, Cumin, Garam Masala, Kashmiri Chilli. 100% pure, chemical-free. Free delivery above ₹500.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Health & Millet Products',
                'slug' => 'health-millet-products',
                'description' => 'Explore our nutritious millet-based products and health foods. Our range includes Ragi Powder (finger millet), Black Urad Dal Powder, and other protein-rich, calcium-rich powders. These superfood powders are ideal for making porridge, health drinks, baby food, rotis, and more. 100% natural with no chemicals or preservatives.',
                'meta_title' => 'Buy Ragi Powder, Millet Products & Health Foods Online',
                'meta_description' => 'Shop nutritious millet products - Ragi Powder, Black Urad Dal Powder & more. Rich in calcium, protein & fiber. 100% natural, chemical-free. Free delivery above ₹500.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Baby Care',
                'slug' => 'baby-care',
                'description' => 'Natural and safe baby care products made with traditional herbal ingredients. Our herbal bath powder is gentle on sensitive skin and suitable for babies, kids, and adults. Made with green gram, Bengal gram, wild turmeric, rose petals, neem, and other natural ingredients. 100% chemical-free alternative to soaps.',
                'meta_title' => 'Natural Herbal Baby Care Products | Safe & Chemical-Free',
                'meta_description' => 'Shop natural herbal baby care products - Bath powders safe for sensitive skin. Made with traditional herbs. 100% chemical-free. Free delivery above ₹500.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Ayurvedic & Wellness',
                'slug' => 'ayurvedic-wellness',
                'description' => 'Traditional Ayurvedic and wellness products for natural health care. Our range includes Hair Growth Oil made with hibiscus, amla, bhringraj, and other herbs, and Knee Pain Relief Oil for joint pain and muscle soreness. All oils are prepared using traditional Ayurvedic methods with 100% natural ingredients.',
                'meta_title' => 'Ayurvedic Oils & Wellness Products | Hair Oil, Pain Relief Oil',
                'meta_description' => 'Shop Ayurvedic wellness products - Hair Growth Oil, Knee Pain Relief Oil. Made with natural herbs. 100% chemical-free. Free delivery above ₹500.',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
            $this->command->info("Created/Updated category: {$categoryData['name']}");
        }

        $this->command->info('Categories seeded successfully!');
    }
}
