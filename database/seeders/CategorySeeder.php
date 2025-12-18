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
            [
                'name' => 'Combo Masala',
                'slug' => 'combo-masala',
                'description' => 'Special combo packs of premium masalas at great value',
                'sort_order' => 5,
                'children' => [
                    ['name' => 'Kitchen Essentials Combo', 'slug' => 'kitchen-essentials-combo', 'description' => 'Essential spices for everyday cooking'],
                    ['name' => 'North Indian Combo', 'slug' => 'north-indian-combo', 'description' => 'Spices for North Indian cuisine'],
                    ['name' => 'South Indian Combo', 'slug' => 'south-indian-combo', 'description' => 'Spices for South Indian dishes'],
                    ['name' => 'Biryani Combo', 'slug' => 'biryani-combo', 'description' => 'Complete biryani spice collection'],
                    ['name' => 'Pickle Masala Combo', 'slug' => 'pickle-masala-combo', 'description' => 'Masalas for homemade pickles'],
                ],
            ],
            [
                'name' => 'Combo Gift Pack',
                'slug' => 'combo-gift-pack',
                'description' => 'Premium gift packs perfect for festivals and special occasions',
                'sort_order' => 6,
                'children' => [
                    ['name' => 'Festival Gift Pack', 'slug' => 'festival-gift-pack', 'description' => 'Special packs for Diwali, Pongal & other festivals'],
                    ['name' => 'Wedding Gift Pack', 'slug' => 'wedding-gift-pack', 'description' => 'Elegant gift packs for wedding ceremonies'],
                    ['name' => 'Housewarming Gift Pack', 'slug' => 'housewarming-gift-pack', 'description' => 'Traditional gifts for new home celebrations'],
                    ['name' => 'Corporate Gift Pack', 'slug' => 'corporate-gift-pack', 'description' => 'Professional gift packs for business gifting'],
                    ['name' => 'Mini Gift Pack', 'slug' => 'mini-gift-pack', 'description' => 'Small and affordable gift sets'],
                    ['name' => 'Premium Gift Pack', 'slug' => 'premium-gift-pack', 'description' => 'Luxury gift collections with premium products'],
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
