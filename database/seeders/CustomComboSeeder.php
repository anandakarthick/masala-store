<?php

namespace Database\Seeders;

use App\Models\CustomComboSetting;
use Illuminate\Database\Seeder;

class CustomComboSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $combos = [
            [
                'name' => 'Pick Any 3',
                'slug' => 'pick-any-3',
                'description' => 'Choose any 3 products from our collection and get 10% off on your combo!',
                'min_products' => 3,
                'max_products' => 3,
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'allow_same_product' => false,
                'allow_variants' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pick Any 5',
                'slug' => 'pick-any-5',
                'description' => 'Choose any 5 products from our collection and get 15% off on your combo!',
                'min_products' => 5,
                'max_products' => 5,
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'allow_same_product' => false,
                'allow_variants' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Build Your Box (3-10 items)',
                'slug' => 'build-your-box',
                'description' => 'Create your own custom box with 3 to 10 products and save 12% on the entire order!',
                'min_products' => 3,
                'max_products' => 10,
                'discount_type' => 'percentage',
                'discount_value' => 12,
                'allow_same_product' => true,
                'allow_variants' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Family Pack (5-15 items)',
                'slug' => 'family-pack',
                'description' => 'Perfect for families! Select 5 to 15 products and get 20% off on your entire combo!',
                'min_products' => 5,
                'max_products' => 15,
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'allow_same_product' => true,
                'allow_variants' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($combos as $combo) {
            CustomComboSetting::updateOrCreate(
                ['slug' => $combo['slug']],
                $combo
            );
        }

        $this->command->info('Custom combo settings created successfully!');
    }
}
