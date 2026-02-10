<?php
// tests/api_audit.php - Pełny test backendu
header("Content-Type: text/plain; charset=UTF-8");

echo "=== RARICART BACKEND AUDIT ===\n";

function check_file($path) {
    if (file_exists($path)) {
        echo "[OK] $path (" . filesize($path) . " bytes)\n";
        return true;
    } else {
        echo "[FAIL] $path NIE ISTNIEJE!\n";
        return false;
    }
}

echo "\n--- PLIKI DANYCH ---\n";
check_file('../assets/data/content.json');
check_file('../assets/data/gallery.json');
check_file('../assets/data/status.json');

echo "\n--- API STATUS ---\n";
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . str_replace('tests/api_audit.php', '', $_SERVER['REQUEST_URI']);

$endpoints = [
    'api/get_status.php',
    'api/get_gallery.php',
    'assets/data/content.json'
];

foreach ($endpoints as $ep) {
    $url = $baseUrl . $ep;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $json = json_decode($res, true);
        if ($json !== null) {
            echo "[OK] $ep - HTTP 200, Valid JSON\n";
        } else {
            echo "[WARN] $ep - HTTP 200, ale INVALID JSON!\n";
        }
    } else {
        echo "[FAIL] $ep - HTTP $httpCode\n";
    }
}

echo "\n--- AUDYT GALERII ---\n";
$galDir = '../assets/gallery/';
if (is_dir($galDir)) {
    $files = array_diff(scandir($galDir), ['.', '..']);
    echo "[INFO] Liczba plików w galerii: " . count($files) . "\n";
} else {
    echo "[FAIL] Katalog galerii nie istnieje!\n";
}

echo "\nDONE.\n";
?>
