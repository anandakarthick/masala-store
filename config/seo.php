<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Verification Codes
    |--------------------------------------------------------------------------
    */
    'verification' => [
        'google' => env('GOOGLE_SITE_VERIFICATION', ''),
        'bing' => env('BING_SITE_VERIFICATION', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default SEO Settings
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'title_suffix' => ' | SV Products - Homemade Masala & Spices',
        'separator' => ' - ',
    ],

    /*
    |--------------------------------------------------------------------------
    | Primary Keywords for Homepage
    |--------------------------------------------------------------------------
    */
    'primary_keywords' => [
        'homemade masala',
        'homemade masala powder',
        'buy masala online',
        'Indian spices online',
        'homemade spices',
        'natural masala powder',
        'pure masala powder',
        'traditional masala',
        'authentic Indian spices',
        'chemical-free masala',
    ],

    /*
    |--------------------------------------------------------------------------
    | Long-tail Keywords
    |--------------------------------------------------------------------------
    */
    'longtail_keywords' => [
        'buy homemade masala powder online India',
        'homemade turmeric powder near me',
        'pure coriander powder online',
        'natural garam masala without preservatives',
        'authentic sambar powder Chennai',
        'homemade rasam powder Tamil Nadu',
        'buy chilli powder online India',
        'organic masala powder delivery',
        'traditional South Indian masala',
        'homemade spice mix online shopping',
    ],

    /*
    |--------------------------------------------------------------------------
    | Product-specific Keywords
    |--------------------------------------------------------------------------
    */
    'product_keywords' => [
        'turmeric' => ['turmeric powder', 'haldi powder', 'manjal powder', 'pure turmeric', 'organic turmeric'],
        'coriander' => ['coriander powder', 'dhania powder', 'kothamalli powder', 'pure coriander'],
        'chilli' => ['chilli powder', 'red chilli powder', 'kashmiri chilli', 'milagai podi'],
        'cumin' => ['cumin powder', 'jeera powder', 'seeragam powder', 'pure cumin'],
        'garam_masala' => ['garam masala', 'garam masala powder', 'mixed spice powder'],
        'sambar' => ['sambar powder', 'sambar podi', 'sambar masala', 'South Indian sambar'],
        'rasam' => ['rasam powder', 'rasam podi', 'rasam masala'],
    ],
];
