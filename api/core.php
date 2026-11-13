<?php
declare(strict_types=1);
// api/core.php
// Centralny plik ładujący dane bazy JSON i udostępniający funkcje pomocnicze.

// Security headers & PHP2Shell / Phar Deserialization Guard (OWASP)
ini_set('display_errors', '0');
error_reporting(E_ALL);
foreach (['phar', 'ftp', 'ftps'] as $__unsafeWrapper) {
    if (in_array($__unsafeWrapper, stream_get_wrappers(), true)) {
        @stream_wrapper_unregister($__unsafeWrapper);
    }
}
if (!headers_sent()) {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
}

clearstatcache();

if (!function_exists('read_json_file_shared')) {
    function read_json_file_shared(string $path): ?array {
        if (!is_file($path) || filesize($path) <= 10) {
            return null;
        }
        $fp = @fopen($path, 'rb');
        if (!$fp) {
            return null;
        }
        $raw = '';
        if (@flock($fp, LOCK_SH)) {
            $raw = (string)stream_get_contents($fp);
            @flock($fp, LOCK_UN);
        }
        fclose($fp);
        if ($raw === '') {
            return null;
        }
        $decoded = json_decode($raw, true, 16);
        return is_array($decoded) ? $decoded : null;
    }
}

// Load dynamic content on server side to prevent flickering
$content = [];
$content_file = __DIR__ . '/../assets/data/content.json';
$content_dist = __DIR__ . '/../assets/data/content.json.dist';

$content = read_json_file_shared($content_file);
if (!is_array($content) || empty($content)) {
    $content = read_json_file_shared($content_dist);
}
if (!is_array($content)) {
    $content = [];
}

if (!function_exists('get_val')) {
    function get_val(string $key, mixed $default = ''): mixed {
        global $content;
        
        // Obsługa zagnieżdżonych kluczy (np. offer_cards.pancakes)
        $keys = explode('.', $key);
        $val = $content;
        
        foreach ($keys as $k) {
            if (is_array($val) && isset($val[$k])) {
                $val = $val[$k];
            } else {
                return $default;
            }
        }
        
        // Zabezpieczenie przed pustymi wartościami, które wpadły do JSON
        $result = (empty($val) && $val !== '0' && $val !== 0 && $val !== false) ? $default : $val;

        // Cache busting dla plików lokalnych (z blokadą wrapperów strumieniowych :// oraz Path Traversal ..)
        if (is_string($result) && $result !== $default && strpos($result, '?') === false && strpos($result, 'http') !== 0 && strpos($result, '://') === false && strpos($result, '..') === false) {
            $local_path = __DIR__ . '/../' . ltrim($result, '/');
            if (file_exists($local_path)) {
                $result .= '?v=' . filemtime($local_path);
            }
        }
        
        return $result;
    }
}
?>
