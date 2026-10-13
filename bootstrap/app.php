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
            \App\Http\Middleware\GzipResponse::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'logout',
            'appointment/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle unauthenticated requests:
        // On the staff subdomain (or in local fallback), redirect cleanly to login.
        // On the public domain, return 404 to hide internal staff routes from external probing.
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $staffDomain = config('app.staff_domain');
            if (empty($staffDomain) || $request->getHost() === $staffDomain) {
                return redirect()->guest(route('login'));
            }

            abort(404);
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $secureImageMsg = \App\Rules\SecureImage::errorMessage();
                $allErrors = collect($e->errors())->flatten()->all();
                if (in_array($secureImageMsg, $allErrors, true)) {
                    return response()->json([
                        'message' => $secureImageMsg,
                        'errors' => $e->errors(),
                    ], 415);
                }
            }
        });
    })->create();
