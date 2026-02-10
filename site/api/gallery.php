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

// 2. Fallback: Scan directory (Auto-discovery)
$images = [];
if (is_dir($galleryDir)) {
    $files = scandir($galleryDir);
    foreach ($files as $file) {
        if (in_array($file, ['.', '..'])) continue;
        
        // Check extension
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $images[] = 'assets/gallery/' . $file;
        }
    }
}

// 3. Save this state to JSON for next time (Self-healing)
if (!empty($images)) {
    // Ensure dir exists
    if (!file_exists(dirname($jsonFile))) {
        mkdir(dirname($jsonFile), 0755, true);
    }
    file_put_contents($jsonFile, json_encode($images, JSON_PRETTY_PRINT));
}

echo json_encode($images);
?>
