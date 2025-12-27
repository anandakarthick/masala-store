<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $businessName = \App\Models\Setting::get('business_name', 'SV Products');
        $businessTagline = \App\Models\Setting::get('business_tagline', 'Premium Homemade Masala, Spices & Herbal Products');
        $businessPhone = \App\Models\Setting::get('business_phone', '+919876543210');
        $businessEmail = \App\Models\Setting::get('business_email', 'support@svproducts.store');
        $businessAddress = \App\Models\Setting::get('business_address', 'Chennai, Tamil Nadu, India');
        $siteUrl = 'https://www.svproducts.store'; // Canonical domain
        
        // Enhanced default SEO
        $defaultTitle = 'Buy Homemade Masala Powder Online | Pure Indian Spices';
        $defaultDescription = 'Buy premium homemade masala powder online at ' . $businessName . '. 100% pure & natural Indian spices - turmeric, coriander, garam masala, sambar powder. Chemical-free, traditional recipes. Free delivery above ₹500.';
        $defaultKeywords = 'homemade masala, homemade masala powder, buy masala online, Indian spices online, pure turmeric powder, coriander powder, garam masala, sambar powder, rasam powder, natural spices, chemical-free masala, ' . $businessName;
        
        // Generate canonical URL (always https://www.)
        $canonicalUrl = $siteUrl . parse_url(url()->current(), PHP_URL_PATH);
        
        // WhatsApp Settings
        $whatsappNumber = \App\Models\Setting::get('whatsapp_number', '');
        $whatsappEnabled = \App\Models\Setting::get('whatsapp_enabled', '1');
        $whatsappMessage = \App\Models\Setting::get('whatsapp_default_message', 'Hello! I would like to place an order.');
        
        // Marquee/Announcement Bar Settings
        $marqueeEnabled = \App\Models\Setting::get('marquee_enabled', '1');
        $marqueeText = \App\Models\Setting::get('marquee_text', '🎉 Free Shipping on Orders Above ₹500 | 100% Pure & Natural Products | Order Now! 🌿');
        $marqueeSpeed = \App\Models\Setting::get('marquee_speed', '30'); // seconds for one complete scroll
        $marqueeBgColor = \App\Models\Setting::get('marquee_bg_color', '#ea580c'); // orange-600
        
        // Social Media Links
        $socialLinks = \App\Models\SocialMediaLink::active()->get();
        
        // Footer Pages
        $footerPages = \App\Models\Page::active()->footer()->get();
        
        // Theme Colors - Orange Theme matching mobile app
        $primaryColor = '#F97316'; // orange-500
        $primaryDark = '#EA580C'; // orange-600
        $primaryDarker = '#C2410C'; // orange-700
        $primaryLight = '#FED7AA'; // orange-200
        
        // Logo and Favicon
        $faviconUrl = \App\Models\Setting::favicon();
        $logoUrl = \App\Models\Setting::logo();
    @endphp
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', $defaultTitle) | {{ $businessName }}</title>
    <meta name="title" content="@yield('title', $defaultTitle) | {{ $businessName }}">
    <meta name="description" content="@yield('meta_description', $defaultDescription)">
    <meta name="keywords" content="@yield('meta_keywords', $defaultKeywords)">
    <meta name="author" content="{{ $businessName }}">
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <meta name="googlebot" content="index, follow, max-image-preview:large">
    <meta name="bingbot" content="index, follow">
    
    <!-- Canonical & Language -->
    <link rel="canonical" href="@yield('canonical', $canonicalUrl)">
    <link rel="alternate" hreflang="en-IN" href="@yield('canonical', $canonicalUrl)">
    <link rel="alternate" hreflang="x-default" href="@yield('canonical', $canonicalUrl)">
    @stack('seo_links')
    
    <!-- Site Verification -->
    @if(config('seo.verification.google'))
    <meta name="google-site-verification" content="{{ config('seo.verification.google') }}">
    @endif
    @if(config('seo.verification.bing'))
    <meta name="msvalidate.01" content="{{ config('seo.verification.bing') }}">
    @endif
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical', $canonicalUrl)">
    <meta property="og:title" content="@yield('title', $defaultTitle) | {{ $businessName }}">
    <meta property="og:description" content="@yield('meta_description', $defaultDescription)">
    <meta property="og:site_name" content="{{ $businessName }}">
    <meta property="og:locale" content="en_IN">
    @if($logoUrl)
        <meta property="og:image" content="{{ $logoUrl }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $businessName }} - Homemade Masala & Spices">
    @endif
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('canonical', $canonicalUrl)">
    <meta name="twitter:title" content="@yield('title', $defaultTitle) | {{ $businessName }}">
    <meta name="twitter:description" content="@yield('meta_description', $defaultDescription)">
    @if($logoUrl)
        <meta name="twitter:image" content="{{ $logoUrl }}">
    @endif
    
    <!-- Geographic & Location -->
    <meta name="geo.region" content="IN-TN">
    <meta name="geo.placename" content="Chennai, Tamil Nadu">
    <meta name="geo.position" content="13.0827;80.2707">
    <meta name="ICBM" content="13.0827, 80.2707">
    <meta name="DC.title" content="{{ $businessName }} - Homemade Masala Powder Online">
    
    <!-- Mobile & App -->
    <meta name="theme-color" content="#F97316">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $businessName }}">
    <meta name="application-name" content="{{ $businessName }}">
    <meta name="format-detection" content="telephone=yes">
    
    <!-- Favicon -->
    @if($faviconUrl)
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">
    
    <!-- TODO: Replace Tailwind CDN with compiled CSS in production for better performance -->
    <!-- Run: npm run build and use @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            200: '#FED7AA',
                            300: '#FDBA74',
                            400: '#FB923C',
                            500: '#F97316',
                            600: '#EA580C',
                            700: '#C2410C',
                            800: '#9A3412',
                            900: '#7C2D12',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Organization Schema -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => $siteUrl . '/#organization',
        'name' => $businessName,
        'alternateName' => 'SV Masala',
        'url' => $siteUrl,
        'description' => 'Premium homemade masala powder, Indian spices, and herbal products. 100% pure, natural, and chemical-free products made with traditional recipes.',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $logoUrl ?? asset('images/logo.png'),
            'width' => 512,
            'height' => 512
        ],
        'image' => $logoUrl ?? asset('images/logo.png'),
        'email' => $businessEmail,
        'telephone' => $businessPhone,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $businessAddress,
            'addressLocality' => 'Chennai',
            'addressRegion' => 'Tamil Nadu',
            'postalCode' => '600001',
            'addressCountry' => 'IN'
        ],
        'contactPoint' => [
            [
                '@type' => 'ContactPoint',
                'telephone' => $businessPhone,
                'email' => $businessEmail,
                'contactType' => 'customer service',
                'availableLanguage' => ['English', 'Tamil', 'Hindi'],
                'areaServed' => 'IN',
                'hoursAvailable' => [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens' => '09:00',
                    'closes' => '21:00'
                ]
            ],
            [
                '@type' => 'ContactPoint',
                'telephone' => $businessPhone,
                'contactType' => 'sales',
                'availableLanguage' => ['English', 'Tamil']
            ]
        ],
        'sameAs' => $socialLinks->pluck('url')->toArray(),
        'foundingDate' => '2020',
        'numberOfEmployees' => [
            '@type' => 'QuantitativeValue',
            'minValue' => 1,
            'maxValue' => 10
        ],
        'slogan' => '100% Pure & Natural Homemade Masala',
        'knowsAbout' => ['Indian Spices', 'Masala Powder', 'Herbal Products', 'Traditional Recipes', 'Ayurvedic Products'],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Homemade Masala & Spices',
            'itemListElement' => [
                ['@type' => 'OfferCatalog', 'name' => 'Masala Powders'],
                ['@type' => 'OfferCatalog', 'name' => 'Indian Spices'],
                ['@type' => 'OfferCatalog', 'name' => 'Herbal Products'],
                ['@type' => 'OfferCatalog', 'name' => 'Ayurvedic Oils']
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    
    <!-- LocalBusiness Schema -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => ['LocalBusiness', 'Store', 'GroceryStore'],
        '@id' => $siteUrl . '/#business',
        'name' => $businessName,
        'alternateName' => 'SV Masala Store',
        'description' => 'Buy premium homemade masala powder online. Fresh, pure & natural Indian spices including turmeric, coriander, garam masala, sambar powder. Chemical-free products made with traditional family recipes. Free delivery above ₹500.',
        'url' => $siteUrl,
        'logo' => $logoUrl ?? asset('images/logo.png'),
        'image' => [
            $logoUrl ?? asset('images/logo.png')
        ],
        'telephone' => $businessPhone,
        'email' => $businessEmail,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $businessAddress,
            'addressLocality' => 'Chennai',
            'addressRegion' => 'Tamil Nadu',
            'postalCode' => '600001',
            'addressCountry' => 'IN'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 13.0827,
            'longitude' => 80.2707
        ],
        'hasMap' => 'https://maps.google.com/?q=Chennai,Tamil+Nadu,India',
        'priceRange' => '₹₹',
        'currenciesAccepted' => 'INR',
        'paymentAccepted' => 'Cash, UPI, Credit Card, Debit Card, Net Banking, Google Pay, PhonePe, Paytm',
        'areaServed' => [
            [
                '@type' => 'Country',
                'name' => 'India'
            ],
            [
                '@type' => 'State',
                'name' => 'Tamil Nadu'
            ]
        ],
        'serviceArea' => [
            '@type' => 'GeoCircle',
            'geoMidpoint' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 13.0827,
                'longitude' => 80.2707
            ],
            'geoRadius' => '5000000'
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'opens' => '09:00',
                'closes' => '21:00'
            ],
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'Sunday',
                'opens' => '10:00',
                'closes' => '18:00'
            ]
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.8',
            'reviewCount' => '150',
            'bestRating' => '5',
            'worstRating' => '1'
        ],
        'review' => [
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => '5',
                'bestRating' => '5'
            ],
            'author' => [
                '@type' => 'Person',
                'name' => 'Happy Customer'
            ],
            'reviewBody' => 'Excellent quality homemade masala powders. Very fresh and aromatic. Highly recommended!'
        ],
        'makesOffer' => [
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Product',
                    'name' => 'Homemade Masala Powders'
                ]
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Product',
                    'name' => 'Pure Indian Spices'
                ]
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Product',
                    'name' => 'Herbal & Ayurvedic Products'
                ]
            ]
        ],
        'potentialAction' => [
            '@type' => 'OrderAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $siteUrl . '/products',
                'actionPlatform' => [
                    'https://schema.org/DesktopWebPlatform',
                    'https://schema.org/MobileWebPlatform',
                    'https://schema.org/AndroidPlatform',
                    'https://schema.org/IOSPlatform'
                ]
            ],
            'deliveryMethod' => 'http://purl.org/goodrelations/v1#DeliveryModeOwnFleet'
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    
    <!-- WebSite Schema with SearchAction -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $businessName,
        'url' => $siteUrl,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => route('products.search') . '?q={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type="number"] { -moz-appearance: textfield; }
        .whatsapp-float { animation: whatsapp-pulse 2s infinite; }
        @keyframes whatsapp-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6); }
        }
        
        /* Marquee Animation */
        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
        }
        .marquee-content {
            display: inline-block;
            animation: marquee-scroll {{ $marqueeSpeed }}s linear infinite;
        }
        .marquee-content:hover {
            animation-play-state: paused;
        }
        @keyframes marquee-scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        
        /* Top bar height for offset */
        .top-bar-offset {
            padding-top: 72px; /* Adjust based on top bar + header height */
        }
        @media (min-width: 640px) {
            .top-bar-offset {
                padding-top: 76px;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #F97316; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #EA580C; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col" x-data="cartManager()" x-init="init()">
    <div x-show="toast.show" x-cloak
         :class="toast.type === 'success' ? 'bg-orange-500' : 'bg-red-500'"
         class="fixed top-36 right-4 z-50 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
        <span x-text="toast.message"></span>
    </div>

    <!-- Fixed Top Bar Container -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-orange-600 to-orange-500">
        <!-- Top Bar with Contact, Marquee & Social -->
        <div class="text-white text-xs sm:text-sm py-1.5">
            <div class="container mx-auto px-2 sm:px-4">
                <div class="flex justify-between items-center">
                    <!-- Left: WhatsApp Order Button -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($whatsappEnabled == '1' && $whatsappNumber)
                            <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                               target="_blank" rel="noopener"
                               class="flex items-center gap-1 bg-green-500 hover:bg-green-600 px-2 py-0.5 rounded-full text-xs transition">
                                <i class="fab fa-whatsapp"></i>
                                <span class="hidden sm:inline">Order on WhatsApp</span>
                            </a>
                        @endif
                        
                        <!-- Social Media Links -->
                        @if($socialLinks->count() > 0)
                            <div class="hidden lg:flex items-center gap-2 border-l border-orange-400 pl-2">
                                @foreach($socialLinks->take(4) as $social)
                                    <a href="{{ $social->url }}" target="_blank" rel="noopener" 
                                       class="hover:text-orange-200 transition" title="{{ $social->name }}">
                                        <i class="{{ $social->icon }}"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Center: Marquee Running Text -->
                    @if($marqueeEnabled == '1' && $marqueeText)
                        <div class="flex-1 mx-3 overflow-hidden">
                            <div class="marquee-container">
                                <div class="marquee-content text-xs">
                                    <span class="mx-4">{{ $marqueeText }}</span>
                                    <span class="mx-4">{{ $marqueeText }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Right: Phone, Track, Account -->
                    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                        <a href="tel:{{ $businessPhone }}" class="flex items-center hover:text-orange-200">
                            <i class="fas fa-phone text-xs mr-1"></i>
                            <span class="hidden sm:inline">{{ $businessPhone }}</span>
                        </a>
                        <a href="{{ route('tracking.index') }}" class="hover:text-orange-200 flex items-center border-l border-orange-400 pl-2">
                            <i class="fas fa-truck text-xs"></i>
                            <span class="hidden md:inline ml-1">Track</span>
                        </a>
                        @auth
                            <a href="{{ route('account.dashboard') }}" class="hover:text-orange-200 border-l border-orange-400 pl-2">Account</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-orange-200 border-l border-orange-400 pl-2">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header (Sticky, positioned right below fixed top bar) -->
    <header class="bg-white shadow-md fixed top-[28px] sm:top-[32px] left-0 right-0 z-40" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex items-center justify-between py-2 sm:py-3">
                <a href="{{ route('home') }}" class="flex items-center">
                    @if(\App\Models\Setting::logo())
                        <img src="{{ \App\Models\Setting::logo() }}" alt="{{ $businessName }}" class="h-8 sm:h-10">
                    @else
                        <span class="text-sm sm:text-lg font-bold text-orange-500 flex items-center">
                            <i class="fas fa-leaf mr-1"></i>
                            <span class="hidden md:inline">{{ $businessName }}</span>
                            <span class="md:hidden">SV Masala</span>
                        </span>
                    @endif
                </a>

                <form action="{{ route('products.search') }}" method="GET" class="hidden lg:flex flex-1 max-w-md mx-4">
                    <div class="relative w-full">
                        <input type="text" name="q" placeholder="Search masala, spices, oils..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                               value="{{ request('q') }}">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-orange-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <div class="flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('products.search') }}" class="lg:hidden text-gray-700 hover:text-orange-600 p-2">
                        <i class="fas fa-search text-lg"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-orange-600 p-2">
                        <i class="fas fa-shopping-cart text-lg sm:text-xl"></i>
                        <span x-show="cartCount > 0" x-text="cartCount"
                              class="absolute top-0 right-0 bg-orange-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-medium"></span>
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-700 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            @php
                $navCategories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get();
            @endphp
            
            <nav class="hidden lg:block border-t">
                <ul class="flex items-center justify-center space-x-5 py-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 font-medium transition">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-700 hover:text-orange-600 font-medium transition">All Products</a></li>
                    @foreach($navCategories as $category)
                        <li><a href="{{ route('category.show', $category->slug) }}" class="text-gray-700 hover:text-orange-600 font-medium transition">{{ $category->name }}</a></li>
                    @endforeach
                    <li>
                        <a href="{{ route('products.offers') }}" class="inline-flex items-center gap-1 bg-gradient-to-r from-red-500 to-orange-500 text-white px-3 py-1 rounded-full font-medium hover:from-red-600 hover:to-orange-600 transition-all text-xs">
                            <i class="fas fa-fire animate-pulse"></i> Offers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('combo.index') }}" class="inline-flex items-center gap-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-3 py-1 rounded-full font-medium hover:from-purple-600 hover:to-pink-600 transition-all text-xs">
                            <i class="fas fa-box-open"></i> Build Combo
                        </a>
                    </li>
                    <li><a href="{{ route('about') }}" class="text-gray-700 hover:text-orange-600 font-medium transition">About</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-700 hover:text-orange-600 font-medium transition">Contact</a></li>
                </ul>
            </nav>

            <div x-show="mobileMenuOpen" x-cloak class="lg:hidden border-t py-3">
                <form action="{{ route('products.search') }}" method="GET" class="mb-4">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <ul class="space-y-1">
                    <li><a href="{{ route('home') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-home w-5 mr-2"></i>Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="block py-2 px-3 rounded text-orange-600 bg-orange-50 font-medium"><i class="fas fa-th-large w-5 mr-2"></i>All Products</a></li>
                    <li>
                        <a href="{{ route('products.offers') }}" class="block py-2 px-3 rounded bg-gradient-to-r from-red-500 to-orange-500 text-white font-medium">
                            <i class="fas fa-fire w-5 mr-2 animate-pulse"></i>🔥 Special Offers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('combo.index') }}" class="block py-2 px-3 rounded bg-gradient-to-r from-purple-500 to-pink-500 text-white font-medium">
                            <i class="fas fa-box-open w-5 mr-2"></i>🎁 Build Your Combo
                        </a>
                    </li>
                    @foreach($navCategories as $category)
                        <li><a href="{{ route('category.show', $category->slug) }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-tag w-5 mr-2"></i>{{ $category->name }}</a></li>
                    @endforeach
                    <li class="border-t pt-2 mt-2"><a href="{{ route('about') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-info-circle w-5 mr-2"></i>About</a></li>
                    <li><a href="{{ route('contact') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-envelope w-5 mr-2"></i>Contact</a></li>
                    <li class="border-t pt-2 mt-2"><a href="{{ route('tracking.index') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-truck w-5 mr-2"></i>Track Order</a></li>
                    @if($whatsappEnabled == '1' && $whatsappNumber)
                        <li>
                            <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                               target="_blank" rel="noopener"
                               class="block py-2 px-3 rounded bg-green-500 text-white font-medium">
                                <i class="fab fa-whatsapp w-5 mr-2"></i>Order on WhatsApp
                            </a>
                        </li>
                    @endif
                    @auth
                        <li><a href="{{ route('account.dashboard') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-user w-5 mr-2"></i>My Account</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 px-3 rounded text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt w-5 mr-2"></i>Logout</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-sign-in-alt w-5 mr-2"></i>Login</a></li>
                        <li><a href="{{ route('register') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-orange-50"><i class="fas fa-user-plus w-5 mr-2"></i>Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </header>

    <!-- Spacer for fixed top bar + header -->
    <div class="h-[120px] sm:h-[130px] lg:h-[110px]"></div>

    <!-- First-Time Customer Discount Banner -->
    @php
        $firstTimeOfferMessage = \App\Services\FirstTimeCustomerService::getOfferMessage();
        $showFirstTimeBanner = $firstTimeOfferMessage && (!auth()->check() || \App\Services\FirstTimeCustomerService::isFirstTimeCustomer());
    @endphp
    @if($showFirstTimeBanner)
        <div class="bg-gradient-to-r from-orange-400 via-orange-500 to-red-500 text-white py-2.5 shadow-md relative" x-data="{ showBanner: true }" x-show="showBanner" x-cloak>
            <div class="container mx-auto px-4 pr-10">
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <span class="text-xl">🎁</span>
                    <span class="font-bold text-sm sm:text-base text-center">{{ $firstTimeOfferMessage }}</span>
                    @if(!auth()->check())
                        <a href="{{ route('login') }}" class="ml-2 bg-white text-orange-600 px-4 py-1.5 rounded-full text-xs font-bold hover:bg-orange-100 transition shadow-sm whitespace-nowrap">
                            Login Now
                        </a>
                    @else
                        <a href="{{ route('products.index') }}" class="ml-2 bg-white text-orange-600 px-4 py-1.5 rounded-full text-xs font-bold hover:bg-orange-100 transition shadow-sm whitespace-nowrap">
                            Shop Now
                        </a>
                    @endif
                </div>
            </div>
            <button @click="showBanner = false" class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-orange-200" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 container mx-auto mt-4 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 container mx-auto mt-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-4"><i class="fas fa-leaf text-orange-500"></i> {{ $businessName }}</h3>
                    <p class="text-sm mb-4">{{ $businessTagline }}</p>
                    
                    <!-- Social Media Links -->
                    @if($socialLinks->count() > 0)
                        <div class="flex items-center gap-3 mt-4">
                            @foreach($socialLinks as $social)
                                <a href="{{ $social->url }}" target="_blank" rel="noopener" 
                                   class="w-9 h-9 rounded-full flex items-center justify-center transition transform hover:scale-110"
                                   style="background-color: {{ $social->color ?? '#6B7280' }}"
                                   title="{{ $social->name }}">
                                    <i class="{{ $social->icon }} text-white"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-orange-400 transition">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-orange-400 transition">All Products</a></li>
                        <li><a href="{{ route('products.offers') }}" class="hover:text-orange-400 transition flex items-center gap-1"><i class="fas fa-fire text-orange-400"></i> Offers</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-orange-400 transition">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-orange-400 transition">Contact</a></li>
                    </ul>
                </div>
                
                <!-- Categories -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Categories</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach(\App\Models\Category::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get() as $cat)
                            <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-orange-400 transition">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-sm">
                        <li><i class="fas fa-map-marker-alt mr-2 text-orange-500"></i>{{ $businessAddress }}</li>
                        <li><a href="tel:{{ $businessPhone }}" class="hover:text-orange-400 transition"><i class="fas fa-phone mr-2 text-orange-500"></i>{{ $businessPhone }}</a></li>
                        <li><a href="mailto:{{ $businessEmail }}" class="hover:text-orange-400 transition"><i class="fas fa-envelope mr-2 text-orange-500"></i>{{ $businessEmail }}</a></li>
                        @if($whatsappEnabled == '1' && $whatsappNumber)
                            <li>
                                <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                                   target="_blank" rel="noopener" class="hover:text-orange-400 transition">
                                    <i class="fab fa-whatsapp mr-2 text-green-500"></i>WhatsApp Order
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="border-t border-gray-800 py-4">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-sm">
                    <p>&copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.</p>
                    
                    <!-- Legal Pages Links -->
                    @if($footerPages->count() > 0)
                        <div class="flex items-center gap-4">
                            @foreach($footerPages as $page)
                                <a href="{{ route('page.show', $page->slug) }}" class="hover:text-orange-400 transition">{{ $page->title }}</a>
                                @if(!$loop->last)
                                    <span class="text-gray-600">|</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Cart Button - Always visible -->
    <a href="{{ route('cart.index') }}" 
       class="fixed bottom-24 right-6 z-50 w-14 h-14 bg-orange-500 hover:bg-orange-600 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110"
       title="View Cart">
        <i class="fas fa-shopping-cart text-xl"></i>
        <span x-show="cartCount > 0" x-text="cartCount"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full min-w-[20px] h-[20px] flex items-center justify-center font-bold"></span>
    </a>

    <!-- Floating WhatsApp Button -->
    @if($whatsappEnabled == '1' && $whatsappNumber)
        <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
           target="_blank" rel="noopener"
           class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center shadow-lg whatsapp-float"
           title="Order on WhatsApp">
            <i class="fab fa-whatsapp text-2xl"></i>
        </a>
    @endif

    <script>
        var csrfHelper = {
            getToken: function() { 
                var meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.content : ''; 
            },
            updateToken: function(t) { 
                var m = document.querySelector('meta[name="csrf-token"]'); 
                if(m) m.content = t; 
            },
            refreshToken: function() {
                var self = this;
                return fetch('{{ route("csrf.token") }}', { 
                    method: 'GET', 
                    headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, 
                    credentials: 'same-origin' 
                }).then(function(r) {
                    if(r.ok) { 
                        return r.json().then(function(d) { 
                            if(d.csrf_token) { 
                                self.updateToken(d.csrf_token); 
                                return d.csrf_token; 
                            }
                            return null;
                        });
                    }
                    return null;
                }).catch(function(e) { 
                    console.error('CSRF refresh failed:', e); 
                    return null;
                });
            },
            fetchWithCSRF: function(url, options) {
                var self = this;
                options = options || {};
                options.headers = { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.getToken(), 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest'
                };
                options.credentials = 'same-origin';
                return fetch(url, options).then(function(r) {
                    if(r.status === 419) {
                        return self.refreshToken().then(function(t) {
                            if(t) { 
                                options.headers['X-CSRF-TOKEN'] = t; 
                                return fetch(url, options); 
                            } else { 
                                setTimeout(function() { window.location.reload(); }, 1000); 
                                return null; 
                            }
                        });
                    }
                    return r;
                });
            }
        };
        
        setInterval(function() { csrfHelper.refreshToken(); }, 30*60*1000);

        function cartManager() {
            return {
                cartCount: {{ \App\Models\Cart::getCart()->total_items }},
                toast: { show: false, message: '', type: 'success' },
                init: function() {
                    var self = this;
                    window.addEventListener('cart-updated', function(e) { self.cartCount = e.detail.count; });
                    window.addToCart = function(p, q, v) { self.addToCart(p, q, v); };
                },
                showToast: function(msg, type) {
                    var self = this;
                    this.toast.show = true;
                    this.toast.message = msg;
                    this.toast.type = type || 'success';
                    setTimeout(function() { self.toast.show = false; }, 3000);
                },
                addToCart: function(productId, quantity, variantId) {
                    var self = this;
                    var body = { product_id: productId, quantity: quantity || 1 };
                    if(variantId) body.variant_id = variantId;
                    csrfHelper.fetchWithCSRF('{{ route("cart.add") }}', { 
                        method: 'POST', 
                        body: JSON.stringify(body) 
                    }).then(function(r) {
                        if(!r) return;
                        return r.json();
                    }).then(function(d) {
                        if(d && d.success) { 
                            self.cartCount = d.cart_count; 
                            self.showToast(d.message, 'success'); 
                        } else if(d) {
                            self.showToast(d.message || 'Error adding to cart', 'error');
                        }
                    }).catch(function(e) { 
                        self.showToast('Session expired. Refreshing...', 'error'); 
                        setTimeout(function() { window.location.reload(); }, 1500); 
                    });
                }
            };
        }
    </script>
    @stack('scripts')
</body>
</html>
