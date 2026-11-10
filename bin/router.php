<?php
declare(strict_types=1);

/**
 * Local Development Server Router (CLI-Server only)
 * Emulates Apache/LiteSpeed mod_rewrite & .htaccess security rules for PHP built-in server.
 */
if (PHP_SAPI !== 'cli-server') {
    http_response_code(403);
    exit("Forbidden.\n");
}

$rootDir = dirname(__DIR__);
$uri = (string)parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);

// Block sensitive directories and extensions identically to .htaccess
if (
    preg_match('#^/(bin|config|\.git|\.github|\.githooks|tests|drafts|scripts|docker|assets/data)(/|$)#i', $uri)
    || preg_match('#(\.(json|bak|csv|sqlite|db|dist|log|md|pem|key|kdbx|sh|toml|lock|yml|yaml|htpasswd)$|/\.env)#i', $uri)
) {
    http_response_code(403);
    exit('403 Forbidden');
}

if ($uri === '/robots.txt') {
    require $rootDir . '/api/robots.php';
    return true;
}

if ($uri === '/pakiety' || $uri === '/pakiety/') {
    require $rootDir . '/pakiety.php';
    return true;
}

if ($uri === '/polityka-prywatnosci' || $uri === '/polityka-prywatnosci/') {
    require $rootDir . '/polityka-prywatnosci.php';
    return true;
}

$filePath = $rootDir . $uri;
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

if (is_dir($filePath)) {
    $index = rtrim($filePath, '/\\') . DIRECTORY_SEPARATOR . 'index.php';
    if (file_exists($index)) {
        require $index;
        return true;
    }
}

require $rootDir . '/index.php';
