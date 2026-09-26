<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$jsonFile = __DIR__ . '/../assets/data/gallery.json';
$images = [];
$useJson = false;

if (file_exists($jsonFile)) {
    $raw = file_get_contents($jsonFile);
    $decoded = json_decode((string)$raw, true);
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
