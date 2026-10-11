<?php

/**
 * Production-emulating local server router for Laravel.
 * Adds gzip compression and static caching headers to mirror Nginx/Apache in production.
 */

$publicPath = __DIR__.'/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''
);

$filePath = $publicPath.$uri;

// If static asset exists, serve it with proper caching & gzip compression
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    $contentTypes = [
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'json'  => 'application/json; charset=UTF-8',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'webp'  => 'image/webp',
        'gif'   => 'image/gif',
        'woff2' => 'font/woff2',
        'woff'  => 'font/woff',
        'ttf'   => 'font/ttf',
        'ico'   => 'image/x-icon',
    ];

    $mime = $contentTypes[$ext] ?? 'application/octet-stream';
    header("Content-Type: {$mime}");

    // Static assets: cache aggressively
    if (in_array($ext, ['css', 'js', 'woff2', 'woff', 'ttf', 'png', 'jpg', 'jpeg', 'webp', 'svg', 'ico'])) {
        header("Cache-Control: public, max-age=31536000, immutable");
    }

    // Gzip compressible assets
    $compressible = in_array($ext, ['css', 'js', 'json', 'svg', 'html', 'txt']);
    $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';

    if ($compressible && str_contains($acceptEncoding, 'gzip')) {
        header("Content-Encoding: gzip");
        header("Vary: Accept-Encoding");
        echo gzencode(file_get_contents($filePath), 6);
        return true;
    }

    readfile($filePath);
    return true;
}

require_once $publicPath.'/index.php';
