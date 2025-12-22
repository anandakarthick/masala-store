<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SEO Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains SEO-related configuration for the masala store.
    | Update these values according to your business requirements.
    |
    */

    // Site verification codes (add your own)
    'verification' => [
        'google' => env('GOOGLE_SITE_VERIFICATION', ''),
        'bing' => env('BING_SITE_VERIFICATION', ''),
        'yandex' => env('YANDEX_VERIFICATION', ''),
        'pinterest' => env('PINTEREST_VERIFICATION', ''),
    ],

    // Default meta values
    'defaults' => [
        'title_suffix' => ' - SV Masala & Herbal Products',
        'description' => 'Buy premium quality homemade masala powders, Indian spices, and herbal products online. 100% pure, chemical-free. Free delivery on orders above ₹500.',
        'keywords' => 'homemade masala, Indian spices, turmeric powder, coriander powder, garam masala, herbal products, ayurvedic oils',
        'author' => 'SV Masala & Herbal Products',
        'robots' => 'index, follow',
    ],

    // Open Graph defaults
    'og' => [
        'type' => 'website',
        'locale' => 'en_IN',
        'site_name' => 'SV Masala & Herbal Products',
    ],

    // Twitter Card defaults
    'twitter' => [
        'card' => 'summary_large_image',
        'site' => '@svmasala', // Your Twitter handle
    ],

    // Geo targeting
    'geo' => [
        'region' => 'IN-TN',
        'placename' => 'Chennai',
        'position' => '13.0827;80.2707',
        'icbm' => '13.0827, 80.2707',
    ],

    // Sitemap settings
    'sitemap' => [
        'max_images_per_url' => 1000,
        'changefreq' => [
            'home' => 'daily',
            'products' => 'daily',
            'categories' => 'daily',
            'product' => 'weekly',
            'pages' => 'monthly',
        ],
        'priority' => [
            'home' => '1.0',
            'products' => '0.9',
            'categories' => '0.8',
            'product' => '0.7',
            'pages' => '0.5',
        ],
    ],

    // Structured data
    'structured_data' => [
        'organization' => [
            'type' => 'LocalBusiness',
            'price_range' => '₹₹',
            'currencies_accepted' => 'INR',
            'payment_accepted' => 'Cash, UPI, Credit Card, Debit Card',
            'opening_hours' => [
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'opens' => '09:00',
                'closes' => '21:00',
            ],
        ],
    ],

    // Pagination SEO
    'pagination' => [
        'noindex_paginated' => false, // Set true to noindex pages 2+
        'use_rel_prev_next' => true,
    ],
];
