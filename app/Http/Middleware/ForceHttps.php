<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     * Forces HTTPS and consistent www/non-www domain
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip in local environment
        if (app()->environment('local')) {
            return $next($request);
        }

        $host = $request->getHost();
        $scheme = $request->getScheme();
        $needsRedirect = false;
        $newUrl = $request->fullUrl();

        // Force HTTPS
        if ($scheme !== 'https') {
            $newUrl = str_replace('http://', 'https://', $newUrl);
            $needsRedirect = true;
        }

        // Force www (or non-www - choose one consistently)
        // Using www as canonical
        if (strpos($host, 'www.') !== 0 && !str_contains($host, 'localhost')) {
            $newUrl = str_replace('://' . $host, '://www.' . $host, $newUrl);
            $needsRedirect = true;
        }

        if ($needsRedirect) {
            return redirect()->to($newUrl, 301);
        }

        return $next($request);
    }
}
