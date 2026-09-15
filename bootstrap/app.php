<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\TrackUserActivity::class,
            \App\Http\Middleware\PreventBackHistory::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'logout',
            'appointment/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return a generic 404 instead of redirecting to /login when an
        // unauthenticated user probes a protected route.  This prevents
        // information disclosure — attackers won't learn that a login
        // page exists by simply visiting /admin, /doctor, etc.
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            abort(404);
        });
    })->create();
