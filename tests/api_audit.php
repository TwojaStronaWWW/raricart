<?php
declare(strict_types=1);

// tests/api_audit.php - Pełny test backendu (wyłącznie CLI)
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI access only.\n");
}

echo "=== RARICART BACKEND AUDIT ===\n";

function check_file(string $path): bool {
    if (file_exists($path)) {
        echo "[OK] $path (" . filesize($path) . " bytes)\n";
        return true;
    }
    echo "[FAIL] $path NIE ISTNIEJE!\n";
    return false;
}

$rootDir = dirname(__DIR__);

echo "\n--- PLIKI DANYCH ---\n";
check_file($rootDir . '/assets/data/content.json');
check_file($rootDir . '/assets/data/gallery.json');
check_file($rootDir . '/assets/data/status.json');

$baseUrl = getenv('RARICART_AUDIT_BASE_URL') ?: '';
if ($baseUrl !== '') {
    echo "\n--- API STATUS ($baseUrl) ---\n";
    $endpoints = [
        'api/get_status.php' => 200,
        'api/get_gallery.php' => 200,
        'assets/data/content.json' => 403,
    ];

    foreach ($endpoints as $ep => $expectedStatus) {
        $url = rtrim($baseUrl, '/') . '/' . $ep;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 5,
        ]);
        $res = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === $expectedStatus) {
            echo "[OK] $ep - HTTP $httpCode (oczekiwano $expectedStatus)\n";
        } else {
            echo "[FAIL] $ep - HTTP $httpCode (oczekiwano $expectedStatus)\n";
        }
    }
}

echo "\n--- AUDYT GALERII ---\n";
$galDir = $rootDir . '/assets/gallery/';
if (is_dir($galDir)) {
    $files = array_diff(scandir($galDir) ?: [], ['.', '..']);
    echo "[INFO] Liczba plików w galerii: " . count($files) . "\n";
} else {
    echo "[FAIL] Katalog galerii nie istnieje!\n";
}

echo "\nDONE.\n";
