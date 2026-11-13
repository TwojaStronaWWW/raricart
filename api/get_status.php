<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && strtolower(trim((string)$_SERVER['HTTP_SEC_FETCH_SITE'])) === 'cross-site') {
    http_response_code(403);
    echo json_encode(['enabled' => false, 'text' => '']);
    exit;
}

$file = __DIR__ . '/../assets/data/status.json';

if (is_file($file)) {
    $fp = @fopen($file, 'rb');
    if ($fp && @flock($fp, LOCK_SH)) {
        $raw = (string)stream_get_contents($fp);
        @flock($fp, LOCK_UN);
        fclose($fp);
        $decoded = json_decode($raw, true, 16);
        if (is_array($decoded)) {
            echo json_encode([
                'enabled' => (bool)($decoded['enabled'] ?? false),
                'text' => (string)($decoded['text'] ?? '')
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    } elseif ($fp) {
        fclose($fp);
    }
}

echo json_encode(['enabled' => false, 'text' => '']);

