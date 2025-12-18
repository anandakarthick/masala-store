<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create variant attributes table (size, color, brand, material, etc.)
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Size, Color, Brand, Material, etc.
            $table->string('code')->unique(); // size, color, brand, material
            $table->string('type')->default('select'); // select, color, text
            $table->string('display_type')->default('dropdown'); // dropdown, buttons, color_swatch
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Create attribute values table (S, M, L, XL, Red, Blue, etc.)
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_attribute_id')->constrained()->onDelete('cascade');
            $table->string('value'); // S, M, L, Red, Blue, Cotton
            $table->string('display_value')->nullable(); // Small, Medium, Large
            $table->string('color_code')->nullable(); // #FF0000 for colors
            $table->string('image')->nullable(); // For color swatches
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['variant_attribute_id', 'value']);
        });

        // Add new columns to product_variants table
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('size')->nullable()->after('unit');
            $table->string('color')->nullable()->after('size');
            $table->string('color_code')->nullable()->after('color'); // Hex color code
            $table->string('brand')->nullable()->after('color_code');
            $table->string('material')->nullable()->after('brand');
            $table->string('style')->nullable()->after('material');
            $table->string('pattern')->nullable()->after('style');
            $table->string('fit')->nullable()->after('pattern'); // Regular, Slim, Loose
            $table->string('sleeve_type')->nullable()->after('fit'); // Full, Half, Sleeveless
            $table->string('neck_type')->nullable()->after('sleeve_type'); // Round, V-Neck, Collar
            $table->string('occasion')->nullable()->after('neck_type'); // Casual, Formal, Party
            $table->json('attributes')->nullable()->after('occasion'); // For any additional attributes
            $table->string('variant_image')->nullable()->after('attributes'); // Specific image for this variant
        });

        // Add product_type to products table to identify clothing vs food products
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type')->default('food')->after('is_combo'); // food, clothing, electronics, etc.
        });

        // Insert default attributes
        DB::table('variant_attributes')->insert([
            [
                'name' => 'Size',
                'code' => 'size',
                'type' => 'select',
                'display_type' => 'buttons',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Color',
                'code' => 'color',
                'type' => 'color',
                'display_type' => 'color_swatch',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brand',
                'code' => 'brand',
                'type' => 'select',
                'display_type' => 'dropdown',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Material',
                'code' => 'material',
                'type' => 'select',
                'display_type' => 'dropdown',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pattern',
                'code' => 'pattern',
                'type' => 'select',
                'display_type' => 'dropdown',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fit',
                'code' => 'fit',
                'type' => 'select',
                'display_type' => 'buttons',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert size values
        $sizeAttr = DB::table('variant_attributes')->where('code', 'size')->first();
        if ($sizeAttr) {
            DB::table('variant_attribute_values')->insert([
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'XS', 'display_value' => 'Extra Small', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'S', 'display_value' => 'Small', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'M', 'display_value' => 'Medium', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'L', 'display_value' => 'Large', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'XL', 'display_value' => 'Extra Large', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'XXL', 'display_value' => '2X Large', 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'XXXL', 'display_value' => '3X Large', 'sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                // Numeric sizes
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '28', 'display_value' => '28', 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '30', 'display_value' => '30', 'sort_order' => 11, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '32', 'display_value' => '32', 'sort_order' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '34', 'display_value' => '34', 'sort_order' => 13, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '36', 'display_value' => '36', 'sort_order' => 14, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '38', 'display_value' => '38', 'sort_order' => 15, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '40', 'display_value' => '40', 'sort_order' => 16, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '42', 'display_value' => '42', 'sort_order' => 17, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $sizeAttr->id, 'value' => '44', 'display_value' => '44', 'sort_order' => 18, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                // Free size
                ['variant_attribute_id' => $sizeAttr->id, 'value' => 'Free Size', 'display_value' => 'Free Size', 'sort_order' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Insert color values
        $colorAttr = DB::table('variant_attributes')->where('code', 'color')->first();
        if ($colorAttr) {
            DB::table('variant_attribute_values')->insert([
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Black', 'display_value' => 'Black', 'color_code' => '#000000', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'White', 'display_value' => 'White', 'color_code' => '#FFFFFF', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Red', 'display_value' => 'Red', 'color_code' => '#FF0000', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Blue', 'display_value' => 'Blue', 'color_code' => '#0000FF', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Navy Blue', 'display_value' => 'Navy Blue', 'color_code' => '#000080', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Green', 'display_value' => 'Green', 'color_code' => '#008000', 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Yellow', 'display_value' => 'Yellow', 'color_code' => '#FFFF00', 'sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Orange', 'display_value' => 'Orange', 'color_code' => '#FFA500', 'sort_order' => 8, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Pink', 'display_value' => 'Pink', 'color_code' => '#FFC0CB', 'sort_order' => 9, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Purple', 'display_value' => 'Purple', 'color_code' => '#800080', 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Brown', 'display_value' => 'Brown', 'color_code' => '#8B4513', 'sort_order' => 11, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Grey', 'display_value' => 'Grey', 'color_code' => '#808080', 'sort_order' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Beige', 'display_value' => 'Beige', 'color_code' => '#F5F5DC', 'sort_order' => 13, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Maroon', 'display_value' => 'Maroon', 'color_code' => '#800000', 'sort_order' => 14, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Olive', 'display_value' => 'Olive', 'color_code' => '#808000', 'sort_order' => 15, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Teal', 'display_value' => 'Teal', 'color_code' => '#008080', 'sort_order' => 16, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $colorAttr->id, 'value' => 'Multicolor', 'display_value' => 'Multicolor', 'color_code' => null, 'sort_order' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Insert material values
        $materialAttr = DB::table('variant_attributes')->where('code', 'material')->first();
        if ($materialAttr) {
            DB::table('variant_attribute_values')->insert([
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Cotton', 'display_value' => 'Cotton', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Polyester', 'display_value' => 'Polyester', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Silk', 'display_value' => 'Silk', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Wool', 'display_value' => 'Wool', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Linen', 'display_value' => 'Linen', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Denim', 'display_value' => 'Denim', 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Leather', 'display_value' => 'Leather', 'sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Rayon', 'display_value' => 'Rayon', 'sort_order' => 8, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Nylon', 'display_value' => 'Nylon', 'sort_order' => 9, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Georgette', 'display_value' => 'Georgette', 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Chiffon', 'display_value' => 'Chiffon', 'sort_order' => 11, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Velvet', 'display_value' => 'Velvet', 'sort_order' => 12, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $materialAttr->id, 'value' => 'Cotton Blend', 'display_value' => 'Cotton Blend', 'sort_order' => 13, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Insert pattern values
        $patternAttr = DB::table('variant_attributes')->where('code', 'pattern')->first();
        if ($patternAttr) {
            DB::table('variant_attribute_values')->insert([
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Solid', 'display_value' => 'Solid', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Striped', 'display_value' => 'Striped', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Checked', 'display_value' => 'Checked', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Printed', 'display_value' => 'Printed', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Floral', 'display_value' => 'Floral', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Polka Dots', 'display_value' => 'Polka Dots', 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Graphic', 'display_value' => 'Graphic', 'sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $patternAttr->id, 'value' => 'Embroidered', 'display_value' => 'Embroidered', 'sort_order' => 8, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Insert fit values
        $fitAttr = DB::table('variant_attributes')->where('code', 'fit')->first();
        if ($fitAttr) {
            DB::table('variant_attribute_values')->insert([
                ['variant_attribute_id' => $fitAttr->id, 'value' => 'Regular', 'display_value' => 'Regular Fit', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $fitAttr->id, 'value' => 'Slim', 'display_value' => 'Slim Fit', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $fitAttr->id, 'value' => 'Loose', 'display_value' => 'Loose Fit', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $fitAttr->id, 'value' => 'Relaxed', 'display_value' => 'Relaxed Fit', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['variant_attribute_id' => $fitAttr->id, 'value' => 'Tailored', 'display_value' => 'Tailored Fit', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'size', 'color', 'color_code', 'brand', 'material', 
                'style', 'pattern', 'fit', 'sleeve_type', 'neck_type', 
                'occasion', 'attributes', 'variant_image'
            ]);
        });

        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('variant_attributes');
    }
};
