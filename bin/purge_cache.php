<?php
declare(strict_types=1);

// bin/purge_cache.php - Narzędzie czyszczenia pamięci podręcznej (wyłącznie CLI)
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI only.\n");
}

echo "=== RARICART CACHE PURGE ===\n";

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "1. OPcache reset performed.\n";
}

clearstatcache();
echo "2. Stat cache cleared.\n";
echo "GOTOWE.\n";
