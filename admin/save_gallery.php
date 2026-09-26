<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

check_login();
check_csrf();

header('Content-Type: application/json; charset=UTF-8');

$jsonFile = __DIR__ . '/../assets/data/gallery.json';
$input = json_decode((string)file_get_contents('php://input'), true);

if (isset($input['images']) && is_array($input['images'])) {
    // Atomic write with LOCK_EX to prevent corruption
    $encoded = json_encode(array_values($input['images']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded !== false && file_put_contents($jsonFile, $encoded, LOCK_EX) !== false) {
        header("X-LiteSpeed-Purge: *");
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu pliku gallery.json']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowy format danych']);
}
