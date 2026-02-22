<?php
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$file = __DIR__ . '/../assets/data/status.json';

if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo json_encode(['enabled' => false, 'text' => '']);
}
?>
