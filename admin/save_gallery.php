<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/R2Client.php';

require_post_method(true);
check_login();
check_csrf(true);
@session_write_close();

header('Content-Type: application/json; charset=UTF-8');

$contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($contentLength > 262144) {
    http_response_code(413);
    echo json_encode(['status' => 'error', 'message' => 'Zbyt duży rozmiar danych galerii.']);
    exit;
}

$jsonFile = __DIR__ . '/../assets/data/gallery.json';
$raw = file_get_contents('php://input', false, null, 0, 262145);
if (is_string($raw) && strlen($raw) > 262144) {
    http_response_code(413);
    echo json_encode(['status' => 'error', 'message' => 'Zbyt duży rozmiar danych galerii.']);
    exit;
}

$input = json_decode((string)$raw, true, 16);

if (isset($input['images']) && is_array($input['images'])) {
    $encoded = json_encode(array_values($input['images']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded !== false && file_put_contents($jsonFile, $encoded, LOCK_EX) !== false) {
        if (defined('R2_ACCOUNT_ID') && defined('R2_ACCESS_KEY_ID') && defined('R2_SECRET_ACCESS_KEY') && defined('R2_BUCKET_NAME')) {
            try {
                $r2 = new \Raricart\Api\R2Client(R2_ACCOUNT_ID, R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET_NAME);
                $r2->putObject('system/gallery.json', $encoded, 'application/json');
            } catch (\Throwable $e) {
                // Fail-open R2 backup
            }
        }
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
