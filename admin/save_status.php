<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_post_method(true);
check_login();
check_csrf(true);
@session_write_close();

header('Content-Type: application/json; charset=UTF-8');

$raw = file_get_contents('php://input', false, null, 0, 8193);
if (is_string($raw) && strlen($raw) > 8192) {
    http_response_code(413);
    echo json_encode(['status' => 'error', 'message' => 'Payload too large']);
    exit;
}

$data = json_decode((string)$raw, true, 8);

if (is_array($data) && isset($data['text']) && isset($data['enabled'])) {
    $file = __DIR__ . '/../assets/data/status.json';
    $cleanText = substr(trim(str_replace(["\r", "\n", "\0"], ' ', strip_tags((string)$data['text']))), 0, 240);

    $status = [
        'text' => $cleanText,
        'enabled' => (bool)$data['enabled']
    ];

    if (file_put_contents($file, json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false) {
        header("X-LiteSpeed-Purge: *");
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Write failed']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}
