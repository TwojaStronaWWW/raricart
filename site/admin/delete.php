<?php
require_once 'auth.php';
check_login();
check_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $filename = basename($data['file']); // Security: basename prevents directory traversal
    
    $filepath = __DIR__ . '/../assets/gallery/' . $filename;
    
    if (file_exists($filepath)) {
        unlink($filepath);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File not found']);
    }
}
?>
