<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categories from old database (product_categories table)
        // Matching the new SV Masala & Herbal Products business
        $categories = [
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

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
