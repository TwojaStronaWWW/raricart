<?php
declare(strict_types=1);

// bin/debug.php - Narzędzie diagnostyczne (wyłącznie CLI)
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI only.\n");
}

echo "=== DIAGNOSTYKA RARICART ===\n";
echo "Server Time: " . date("Y-m-d H:i:s") . " (" . time() . ")\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SAPI: " . PHP_SAPI . "\n\n";

$rootDir = dirname(__DIR__);
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
    $fullPath = $rootDir . '/' . $file;
    if (file_exists($fullPath)) {
        echo sprintf(
            "%-30s | Mtime: %s | Size: %d bytes\n",
            $file,
            date("Y-m-d H:i:s", (int)filemtime($fullPath)),
            (int)filesize($fullPath)
        );
    } else {
        echo sprintf("%-30s | NIE ISTNIEJE\n", $file);
    }
}

echo "\n--- OPCACHE ---\n";
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status(false);
    echo "OPcache enabled: " . ($status ? "YES" : "NO") . "\n";
    if (is_array($status) && isset($status['scripts']) && is_array($status['scripts'])) {
        echo "Scripts cached: " . count($status['scripts']) . "\n";
    }
} else {
    echo "OPcache extension not loaded.\n";
}
