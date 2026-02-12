<?php
// api/gallery.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$jsonFile = __DIR__ . '/../assets/data/gallery.json';
$galleryDir = __DIR__ . '/../assets/gallery';

// 1. Try to read from JSON (Order is important)
if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    if (is_array($data)) {
        echo json_encode($data);
        exit;
    }
}

// 2. If JSON not found or invalid, return empty array
echo json_encode([]);
?>
