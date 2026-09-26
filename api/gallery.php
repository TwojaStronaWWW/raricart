<?php
// api/gallery.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$jsonFile = __DIR__ . '/../assets/data/gallery.json';
$distFile = __DIR__ . '/../assets/data/gallery.json.dist';

$fileToRead = (file_exists($jsonFile) && filesize($jsonFile) > 10) ? $jsonFile : ((file_exists($distFile) && filesize($distFile) > 10) ? $distFile : null);

if ($fileToRead !== null) {
    $content = file_get_contents($fileToRead);
    $data = json_decode((string)$content, true);
    if (is_array($data)) {
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 2. If JSON not found or invalid, return empty array
echo json_encode([]);
?>
