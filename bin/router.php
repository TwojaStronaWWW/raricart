<?php
declare(strict_types=1);

/**
 * Local Development Server Router
 * Emulates Apache/LiteSpeed mod_rewrite rules for PHP built-in server.
 */

$rootDir = dirname(__DIR__);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rewrite rules matching .htaccess
if ($uri === '/robots.txt') {
    require $rootDir . '/api/robots.php';
    return;
}

if ($uri === '/pakiety' || $uri === '/pakiety/') {
    require $rootDir . '/pakiety.php';
    return;
}

// If file exists and is not a directory, let PHP serve it
$filePath = $rootDir . $uri;
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// If directory contains index.php, serve it
if (is_dir($filePath)) {
    $index = rtrim($filePath, '/\\') . DIRECTORY_SEPARATOR . 'index.php';
    if (file_exists($index)) {
        require $index;
        return;
    }
}

// Fallback to root index.php
require $rootDir . '/index.php';
