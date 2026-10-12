<?php
declare(strict_types=1);

/**
 * Główny runner testów jednostkowych PHP dla Raricart
 */

echo "🚀 Uruchamianie testów jednostkowych PHP...\n\n";

$tests = [
    __DIR__ . '/unit/php-backend.test.php'
];

$totalFailed = 0;

foreach ($tests as $t) {
    passthru(escapeshellcmd(PHP_BINARY) . ' ' . escapeshellarg($t), $exitCode);
    if ($exitCode !== 0) {
        $totalFailed++;
    }
}

if ($totalFailed > 0) {
    echo "\n❌ Część testów PHP zakończyła się niepowodzeniem!\n";
    exit(1);
}

echo "\n🎉 Wszystkie testy jednostkowe PHP zakończone sukcesem!\n";
exit(0);
