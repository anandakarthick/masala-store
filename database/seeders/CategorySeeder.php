<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Masala',
                'slug' => 'masala',
                'description' => 'Premium quality spices and masala powders',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Whole Spices', 'slug' => 'whole-spices', 'description' => 'Whole spices for authentic flavor'],
                    ['name' => 'Ground Spices', 'slug' => 'ground-spices', 'description' => 'Finely ground spice powders'],
                    ['name' => 'Spice Blends', 'slug' => 'spice-blends', 'description' => 'Ready-to-use spice mixtures'],
                ],
            ],
            [
                'name' => 'Oils',
                'slug' => 'oils',
                'description' => 'Pure and healthy cooking oils',
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Coconut Oil', 'slug' => 'coconut-oil', 'description' => 'Pure coconut oil'],
                    ['name' => 'Groundnut Oil', 'slug' => 'groundnut-oil', 'description' => 'Cold pressed groundnut oil'],
                    ['name' => 'Sesame Oil', 'slug' => 'sesame-oil', 'description' => 'Traditional sesame oil'],
                ],
            ],
            [
                'name' => 'Candles',
                'slug' => 'candles',
                'description' => 'Decorative and aromatic candles',
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Decorative Candles', 'slug' => 'decorative-candles', 'description' => 'Beautiful decorative candles'],
                    ['name' => 'Aromatic Candles', 'slug' => 'aromatic-candles', 'description' => 'Scented candles for ambiance'],
                    ['name' => 'Diya & Lamp', 'slug' => 'diya-lamp', 'description' => 'Traditional diyas and lamps'],
                ],
            ],
            [
                'name' => 'Return Gifts',
                'slug' => 'return-gifts',
                'description' => 'Perfect gifts for special occasions',
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Gift Boxes', 'slug' => 'gift-boxes', 'description' => 'Beautifully packaged gift sets'],
                    ['name' => 'Combo Packs', 'slug' => 'combo-packs', 'description' => 'Value combo gift packs'],
                    ['name' => 'Festival Specials', 'slug' => 'festival-specials', 'description' => 'Special festival gift items'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                Category::firstOrCreate(
                    ['slug' => $childData['slug']],
                    $childData
                );
            }
        }
    }
}
