<?php
// bin/purge_cache.php - Narzędzie czyszczenia pamięci podręcznej LiteSpeed i OPcache
header("X-LiteSpeed-Purge: *");
header("X-LiteSpeed-Cache-Control: no-cache");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Content-Type: text/plain; charset=UTF-8");

echo "=== RARICART CACHE PURGE ===\n";
echo "1. LiteSpeed Purge Header sent: X-LiteSpeed-Purge: *\n";

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "2. OPcache reset performed.\n";
}

clearstatcache();
echo "3. Stat cache cleared.\n";

echo "\nGOTOWE. Odśwież teraz stronę główną.\n";
