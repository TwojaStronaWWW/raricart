<?php
// api/core.php
// Centralny plik ładujący dane bazy JSON i udostępniający funkcje pomocnicze.

// Security headers
ini_set('display_errors', 0);
error_reporting(E_ALL);
if (!headers_sent()) {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
}

clearstatcache();

// Load dynamic content on server side to prevent flickering
$content = [];
$content_file = __DIR__ . '/../assets/data/content.json';
$content_dist = __DIR__ . '/../assets/data/content.json.dist';

if (file_exists($content_file) && filesize($content_file) > 10) {
    $content = json_decode((string)file_get_contents($content_file), true);
}
if (!is_array($content) || empty($content)) {
    if (file_exists($content_dist) && filesize($content_dist) > 10) {
        $content = json_decode((string)file_get_contents($content_dist), true);
    }
}
if (!is_array($content)) $content = [];

if (!function_exists('get_val')) {
    function get_val($key, $default) {
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

        // Cache busting dla plików lokalnych
        if (is_string($result) && $result !== $default && strpos($result, '?') === false && strpos($result, 'http') !== 0) {
            $local_path = __DIR__ . '/../' . ltrim($result, '/');
            if (file_exists($local_path)) {
                $result .= '?v=' . filemtime($local_path);
            }
        }
        
        return $result;
    }
}
?>
