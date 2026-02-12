<?php
require_once 'auth.php';

check_login();
check_csrf();

header('Content-Type: application/json');

$jsonFile = __DIR__ . '/../assets/data/gallery.json';
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['images']) && is_array($input['images'])) {
    if (file_put_contents($jsonFile, json_encode(array_values($input['images']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
        header("X-LiteSpeed-Purge: *"); // Purge cache on save
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Write failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
