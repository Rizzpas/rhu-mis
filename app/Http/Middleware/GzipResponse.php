<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipResponse
{
    /**
     * Handle an incoming request and gzip compress the response if supported.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only compress successful HTML/JSON text responses when client supports gzip
        if (!function_exists('gzencode') || $response->headers->has('Content-Encoding')) {
            return $response;
        }

        $acceptEncoding = $request->header('Accept-Encoding', '');
        if (!str_contains($acceptEncoding, 'gzip')) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html') && !str_contains($contentType, 'application/json')) {
            return $response;
        }

        $content = $response->getContent();
        if ($content && strlen($content) > 1024) {
            $compressed = gzencode($content, 6);
            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Vary', 'Accept-Encoding');
                $response->headers->remove('Content-Length');
            }
        }

        return $response;
    }
}
