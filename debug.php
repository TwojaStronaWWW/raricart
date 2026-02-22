<?php
// debug.php - Narzędzie diagnostyczne
header("Content-Type: text/plain; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

echo "=== DIAGNOSTYKA RARICART ===\n";
echo "Server Time: " . date("Y-m-d H:i:s") . " (" . time() . ")\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SAPI: " . php_sapi_name() . "\n\n";

$files = [
    'index.php',
    '.htaccess',
    'assets/data/content.json',
    'assets/data/gallery.json',
    'assets/data/status.json',
    'assets/js/script.js',
    'assets/css/styles.css'
];

clearstatcache();
echo "--- STATUS PLIKÓW ---\n";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo sprintf("%-30s | Mtime: %s | Size: %d bytes\n", 
            $file, 
            date("Y-m-d H:i:s", filemtime($file)),
            filesize($file)
        );
    } else {
        echo sprintf("%-30s | NIE ISTNIEJE\n", $file);
    }
}

echo "\n--- NAGŁÓWKI HTTP ---\n";
if (function_exists('apache_request_headers')) {
    echo "Request Headers:\n";
    print_r(apache_request_headers());
}

echo "\n--- OPCACHE ---\n";
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status(false);
    echo "OPcache enabled: " . ($status ? "YES" : "NO") . "\n";
    if ($status) {
        echo "Scripts cached: " . count($status['scripts']) . "\n";
    }
} else {
    echo "OPcache extension not loaded.\n";
}

echo "\n--- CLOUDFLARE? ---\n";
if (isset($_SERVER['HTTP_CF_RAY'])) {
    echo "YES! Detected Cloudflare Ray ID: " . $_SERVER['HTTP_CF_RAY'] . "\n";
    echo "Connecting IP: " . ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? 'unknown') . "\n";
} else {
    echo "NO. Not detected via headers.\n";
}

echo "\n--- LITESPEED? ---\n";
echo isset($_SERVER['LSWWS_LOGID']) ? "YES (LiteSpeed detected)" : "UNKNOWN (not detected via \$_SERVER)";
?>
