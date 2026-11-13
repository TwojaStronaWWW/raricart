<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && strtolower(trim((string)$_SERVER['HTTP_SEC_FETCH_SITE'])) === 'cross-site') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$jsonFile = __DIR__ . '/../assets/data/gallery.json';
$distFile = __DIR__ . '/../assets/data/gallery.json.dist';
$images = [];
$useJson = false;

$readJsonWithSharedLock = static function (string $path): string {
    if (!is_file($path) || filesize($path) <= 10) {
        return '';
    }
    $fp = @fopen($path, 'rb');
    if (!$fp) {
        return '';
    }
    $content = '';
    if (@flock($fp, LOCK_SH)) {
        $content = (string)stream_get_contents($fp);
        @flock($fp, LOCK_UN);
    }
    fclose($fp);
    return $content;
};

$raw = $readJsonWithSharedLock($jsonFile);
if ($raw === '') {
    $raw = $readJsonWithSharedLock($distFile);
}

if ($raw !== '') {
    $decoded = json_decode($raw, true, 16);
    if (is_array($decoded) && !empty($decoded)) {
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $images[] = $item;
            } elseif (is_array($item)) {
                $url = $item['url'] ?? $item['src'] ?? null;
                if ($url) {
                    $images[] = $url;
                }
            }
        }
        $useJson = !empty($images);
    }
}

// Fallback do skanowania lokalnego tylko jeśli baza JSON jest całkowicie pusta
if (!$useJson) {
    $dir = __DIR__ . '/../assets/gallery/';
    if (is_dir($dir)) {
        $files = scandir($dir);
        if (is_array($files)) {
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                        $images[] = 'assets/gallery/' . $file;
                    }
                }
            }
        }
    }
}

echo json_encode(array_values($images), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
