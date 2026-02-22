<?php
require_once 'auth.php'; // Protect this file
check_login();
check_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['text']) && isset($data['enabled'])) {
        $file = __DIR__ . '/../assets/data/status.json';
        
        $status = [
            'text' => strip_tags($data['text']), // Security: No HTML
            'enabled' => (bool)$data['enabled']
        ];
        
        if (file_put_contents($file, json_encode($status)) !== false) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Write failed']);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}
?>
