<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/MediaService.php';

use Raricart\Api\MediaService;

check_login();
check_csrf();

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Niedozwolona metoda żądania.']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nie przesłano pliku lub wystąpił błąd uploadu.']);
    exit;
}

$file = $_FILES['image'];
$target = isset($_POST['target']) && $_POST['target'] === 'content' ? 'content' : 'gallery';

$result = MediaService::processAndUpload($file['tmp_name'], $file['name'], $target);

if ($result['status'] === 'success') {
    // Purge LiteSpeed edge cache on media upload
    header("X-LiteSpeed-Purge: *");
} else {
    http_response_code(422);
}

echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
