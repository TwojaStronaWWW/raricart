<?php
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$jsonFile = __DIR__ . '/../assets/data/gallery.json';

$images = [];
$dir = __DIR__ . '/../assets/gallery/';

if (file_exists($jsonFile)) {
    // Agresywne sprawdzanie: Jeśli JSON ma więcej niż 1 godzinę, spróbujmy odświeżyć
    if (time() - filemtime($jsonFile) > 3600) {
        $useJson = false;
    } else {
        $images = json_decode(file_get_contents($jsonFile), true);
        $useJson = is_array($images) && !empty($images);
    }
} else {
    $useJson = false;
}

if (!$useJson) {
    // Fallback: Jeśli brak JSON lub jest stary, skanujemy katalog
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $images[] = 'assets/gallery/' . $file;
                }
            }
        }
    }
}

// Return as JSON
echo json_encode($images);
?>
