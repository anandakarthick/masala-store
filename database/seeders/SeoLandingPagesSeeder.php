<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class SeoLandingPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Homemade Masala Powder - 100% Pure & Natural',
                'slug' => 'homemade-masala-powder',
                'meta_title' => 'Buy Homemade Masala Powder Online | Pure Indian Spices | SV Products',
                'meta_description' => 'Buy authentic homemade masala powder online. 100% pure, natural & chemical-free. Turmeric, coriander, garam masala, sambar powder. Free delivery above ₹500.',
                'content' => $this->getHomemadeMasalaContent(),
                'is_active' => true,
                'show_in_footer' => false,
                'sort_order' => 10,
            ],
            [
                'title' => 'Buy Indian Spices Online - Fresh & Authentic',
                'slug' => 'buy-indian-spices-online',
                'meta_title' => 'Buy Indian Spices Online | Authentic Masala Powders | SV Products',
                'meta_description' => 'Buy authentic Indian spices online at best prices. Fresh turmeric, coriander, cumin, garam masala & more. Homemade quality, delivered to your doorstep.',
                'content' => $this->getIndianSpicesContent(),
                'is_active' => true,
                'show_in_footer' => false,
                'sort_order' => 11,
            ],
            [
                'title' => 'South Indian Masala Powders - Traditional Recipes',
                'slug' => 'south-indian-masala',
                'meta_title' => 'South Indian Masala Powder | Sambar, Rasam Powder Online | SV Products',
                'meta_description' => 'Authentic South Indian masala powders - sambar podi, rasam powder, idli podi & more. Traditional Tamil Nadu recipes. Chemical-free & homemade.',
                'content' => $this->getSouthIndianMasalaContent(),
                'is_active' => true,
                'show_in_footer' => false,
                'sort_order' => 12,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }

    private function getHomemadeMasalaContent(): string
    {
        return <<<HTML
<h2>Why Choose Homemade Masala Powder?</h2>
<p>At <strong>SV Products</strong>, we bring you the authentic taste of <strong>homemade masala powder</strong> that your grandmother used to make. Our masala powders are freshly ground in small batches using premium quality raw materials, ensuring maximum freshness, aroma, and flavor in every pack.</p>

<h3>What Makes Our Masala Different?</h3>
<ul>
    <li><strong>100% Pure & Natural</strong> - No chemicals, preservatives, or artificial colors</li>
    <li><strong>Freshly Ground</strong> - Made in small batches for maximum freshness</li>
    <li><strong>Traditional Recipes</strong> - Time-tested family recipes passed down through generations</li>
    <li><strong>Premium Quality</strong> - We source only the finest raw materials</li>
    <li><strong>Hygienic Processing</strong> - Prepared in clean, hygienic conditions</li>
</ul>

<h2>Our Homemade Masala Range</h2>

<h3>Essential Masala Powders</h3>
<p>Every Indian kitchen needs these basic spices:</p>
<ul>
    <li><strong>Turmeric Powder (Haldi/Manjal)</strong> - Golden spice with numerous health benefits</li>
    <li><strong>Coriander Powder (Dhania)</strong> - Aromatic and essential for curries</li>
    <li><strong>Red Chilli Powder</strong> - Perfect heat and vibrant color</li>
    <li><strong>Cumin Powder (Jeera)</strong> - Earthy flavor for authentic taste</li>
    <li><strong>Black Pepper Powder</strong> - King of spices</li>
</ul>

<h3>Specialty Masala Blends</h3>
<ul>
    <li><strong>Garam Masala</strong> - The aromatic spice blend for North Indian dishes</li>
    <li><strong>Sambar Powder</strong> - Essential for South Indian sambar</li>
    <li><strong>Rasam Powder</strong> - Perfect blend for tangy rasam</li>
    <li><strong>Biryani Masala</strong> - For aromatic rice dishes</li>
    <li><strong>Meat Masala</strong> - Special blend for non-veg dishes</li>
</ul>

<h2>Benefits of Homemade Masala</h2>
<p>Unlike commercial brands that may contain fillers and additives, our <strong>homemade masala powders</strong> offer:</p>
<ul>
    <li>Stronger aroma and flavor</li>
    <li>Better health benefits</li>
    <li>No artificial additives</li>
    <li>Longer-lasting freshness</li>
    <li>Authentic traditional taste</li>
</ul>

<h2>How to Store Masala Powder</h2>
<p>To maintain freshness and potency:</p>
<ul>
    <li>Store in airtight containers</li>
    <li>Keep away from direct sunlight</li>
    <li>Store in a cool, dry place</li>
    <li>Use clean, dry spoons</li>
    <li>Best consumed within 6-12 months</li>
</ul>

<h2>Order Homemade Masala Online</h2>
<p>Experience the difference of authentic <strong>homemade masala powder</strong>. We deliver across India with <strong>free shipping on orders above ₹500</strong>. All our products come with manufacturing and expiry dates clearly mentioned.</p>

<p><a href="/products">Shop Now</a> and taste the difference!</p>
HTML;
    }

    private function getIndianSpicesContent(): string
    {
        return <<<HTML
<h2>Buy Authentic Indian Spices Online</h2>
<p>Looking to <strong>buy Indian spices online</strong>? SV Products brings you the finest quality spices sourced from the best growing regions of India. Our spices are processed with care to retain their natural oils, aroma, and flavor.</p>

<h3>Why Buy Spices from SV Products?</h3>
<ul>
    <li><strong>Farm Fresh Quality</strong> - Direct sourcing from farmers</li>
    <li><strong>No Middlemen</strong> - Better prices for you</li>
    <li><strong>Freshly Packed</strong> - Ground and packed to order</li>
    <li><strong>Pan-India Delivery</strong> - We deliver everywhere in India</li>
    <li><strong>Free Delivery</strong> - On orders above ₹500</li>
</ul>

<h2>Popular Indian Spices</h2>

<h3>Whole Spices</h3>
<ul>
    <li><strong>Cardamom (Elaichi)</strong> - Green and black varieties</li>
    <li><strong>Cinnamon (Dalchini)</strong> - True Ceylon cinnamon</li>
    <li><strong>Cloves (Laung)</strong> - Aromatic flower buds</li>
    <li><strong>Black Pepper (Kali Mirch)</strong> - Malabar pepper</li>
    <li><strong>Cumin Seeds (Jeera)</strong> - Essential for tempering</li>
    <li><strong>Mustard Seeds (Rai)</strong> - For South Indian cooking</li>
</ul>

<h3>Ground Spices</h3>
<ul>
    <li><strong>Turmeric Powder</strong> - Salem & Erode turmeric</li>
    <li><strong>Coriander Powder</strong> - Freshly ground</li>
    <li><strong>Red Chilli Powder</strong> - Multiple heat levels available</li>
    <li><strong>Cumin Powder</strong> - Aromatic and flavorful</li>
    <li><strong>Garam Masala</strong> - Our signature blend</li>
</ul>

<h2>Health Benefits of Indian Spices</h2>
<p>Indian spices are not just about taste - they offer numerous health benefits:</p>
<ul>
    <li><strong>Turmeric</strong> - Anti-inflammatory, antioxidant properties</li>
    <li><strong>Cumin</strong> - Aids digestion, rich in iron</li>
    <li><strong>Coriander</strong> - Helps lower blood sugar</li>
    <li><strong>Black Pepper</strong> - Improves nutrient absorption</li>
    <li><strong>Cardamom</strong> - Good for heart health</li>
</ul>

<h2>How to Order</h2>
<p>Ordering <strong>Indian spices online</strong> from SV Products is easy:</p>
<ol>
    <li>Browse our collection</li>
    <li>Add items to cart</li>
    <li>Choose your delivery address</li>
    <li>Pay securely online or choose COD</li>
    <li>Receive fresh spices at your doorstep!</li>
</ol>

<p><a href="/products">Browse All Spices</a></p>
HTML;
    }

    private function getSouthIndianMasalaContent(): string
    {
        return <<<HTML
<h2>Authentic South Indian Masala Powders</h2>
<p>Discover the authentic taste of <strong>South Indian masala powders</strong> at SV Products. Our recipes come from traditional Tamil Nadu kitchens, perfected over generations. Whether you're making sambar, rasam, or any South Indian delicacy, our masala powders deliver the authentic flavor you're looking for.</p>

<h3>Our South Indian Specialty Range</h3>

<h4>Sambar Powder (Sambar Podi)</h4>
<p>Our <strong>sambar powder</strong> is a perfect blend of roasted lentils, dried chilies, coriander, and aromatic spices. It gives your sambar that authentic South Indian taste with the right balance of heat and flavor.</p>
<ul>
    <li>Perfect for all types of sambar</li>
    <li>Traditional Tamil Nadu recipe</li>
    <li>No artificial colors or preservatives</li>
</ul>

<h4>Rasam Powder (Rasam Podi)</h4>
<p>Make tangy, flavorful <strong>rasam</strong> with our traditional rasam powder. The perfect blend of black pepper, cumin, coriander, and other spices that gives rasam its characteristic taste and aroma.</p>
<ul>
    <li>Ideal for tomato rasam, pepper rasam</li>
    <li>Authentic temple-style recipe</li>
    <li>Rich in digestive spices</li>
</ul>

<h4>Idli Podi (Gun Powder)</h4>
<p>The classic South Indian condiment! Our <strong>idli podi</strong> is made with roasted lentils, dried chilies, and sesame seeds. Perfect with idli, dosa, or even rice.</p>

<h4>Other South Indian Specialties</h4>
<ul>
    <li><strong>Curry Leaves Powder</strong> - For added nutrition and flavor</li>
    <li><strong>Paruppu Podi</strong> - Lentil powder for rice</li>
    <li><strong>Puli Kulambu Powder</strong> - For tangy tamarind curries</li>
    <li><strong>Vatha Kulambu Powder</strong> - Traditional gravy masala</li>
    <li><strong>Milagai Podi</strong> - Spicy chilli powder blend</li>
</ul>

<h2>What Makes Our South Indian Masala Special?</h2>
<ul>
    <li><strong>Traditional Recipes</strong> - Authentic family recipes from Tamil Nadu</li>
    <li><strong>Stone Ground</strong> - Traditional grinding for better texture</li>
    <li><strong>Fresh Ingredients</strong> - Premium quality raw materials</li>
    <li><strong>No Preservatives</strong> - 100% natural and pure</li>
    <li><strong>Balanced Flavors</strong> - Perfect spice proportions</li>
</ul>

<h2>Tips for South Indian Cooking</h2>
<ul>
    <li>Always use fresh curry leaves for tempering</li>
    <li>Roast spices before grinding for better flavor</li>
    <li>Use tamarind paste for authentic tangy taste</li>
    <li>Add coconut for creamy South Indian curries</li>
</ul>

<h2>Order Now</h2>
<p>Bring the authentic taste of South India to your kitchen. <a href="/products">Shop our South Indian masala collection</a> today!</p>

<p><strong>Free delivery</strong> on orders above ₹500 across India.</p>
HTML;
    }
}
