<?php
// Disable error printing immediately
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once 'auth.php';
check_login();
check_csrf();

// Clear any previous buffer from includes
if (ob_get_length()) ob_clean();

header('Content-Type: application/json');
ob_start(); // Start fresh buffer for our JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $target = $_POST['target'] ?? 'gallery'; // 'gallery' or 'content'
    
    // Define Paths
    if ($target === 'content') {
        $uploadDir = __DIR__ . '/../assets/images/';
        $webPath = 'assets/images/';
    } else {
        $uploadDir = __DIR__ . '/../assets/gallery/';
        $webPath = 'assets/gallery/';
    }

    // Ensure Directory Exists
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Server error: Cannot create upload directory.']);
            exit;
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file = $_FILES['image'];
        
        // Validation
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        // Combine allowed types based on context if needed, but for now allow both for content
        $allowedTypes = array_merge($allowedImageTypes, $allowedVideoTypes);

        if (!in_array($mimeType, $allowedTypes)) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => "Invalid file type ($mimeType). Allowed: JPG, PNG, WEBP, MP4."]);
            exit;
        }
        
        // Generate Unique Filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $prefix = in_array($mimeType, $allowedVideoTypes) ? 'vid_' : 'img_';
        $filename = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            ob_clean();
            echo json_encode(['status' => 'success', 'file' => $webPath . $filename, 'type' => in_array($mimeType, $allowedVideoTypes) ? 'video' : 'image']);
        } else {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
        }
    } else {
        ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded or error occurred.']);
    }
} else {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
