<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF token mismatch gracefully
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            // For AJAX requests, return JSON with new token
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please try again.',
                    'csrf_token' => csrf_token(),
                    'reload' => true
                ], 419);
            }
            
            // For regular form submissions, redirect back with message
            return redirect()
                ->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', 'Your session has expired. Please try again.');
        });
    })->create();
