<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    /**
     * Get app configuration for mobile app
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'google_maps_api_key' => config('services.google_maps.api_key'),
                'app_name' => config('app.name'),
                'currency' => '₹',
                'currency_code' => 'INR',
            ],
        ]);
    }
}
