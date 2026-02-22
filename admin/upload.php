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
        
        // Combine allowed types based on context if needed
        $allowedTypes = array_merge($allowedImageTypes, $allowedVideoTypes);

        if (!in_array($mimeType, $allowedTypes)) {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => "Invalid file type ($mimeType). Allowed: JPG, PNG, WEBP, MP4."]);
            exit;
        }
        
        // Generate Unique Filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $isImage = in_array($mimeType, $allowedImageTypes);
        $prefix = $isImage ? 'img_' : 'vid_';
        
        // If image, force .webp extension
        if ($isImage) {
            $ext = 'webp';
        }
        
        $filename = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if ($isImage) {
            // --- IMAGE PROCESSING (Resize & Convert to WebP) ---
            $sourceImage = null;
            
            // Fix: Suppress errors during creation, but handle if it fails
            switch ($mimeType) {
                case 'image/jpeg': 
                    $sourceImage = @imagecreatefromjpeg($file['tmp_name']); 
                    break;
                case 'image/png': 
                    $sourceImage = @imagecreatefrompng($file['tmp_name']); 
                    if ($sourceImage) {
                        imagepalettetotruecolor($sourceImage);
                        imagealphablending($sourceImage, true);
                        imagesavealpha($sourceImage, true);
                    }
                    break;
                case 'image/gif': 
                    $sourceImage = @imagecreatefromgif($file['tmp_name']); 
                    break;
                case 'image/webp': 
                    $sourceImage = @imagecreatefromwebp($file['tmp_name']); 
                    break;
            }

            if ($sourceImage) {
                $width = imagesx($sourceImage);
                $height = imagesy($sourceImage);
                $maxWidth = 1920;

                // Resize if needed
                if ($width > $maxWidth) {
                    $newWidth = $maxWidth;
                    $newHeight = floor($height * ($maxWidth / $width));
                } else {
                    $newWidth = $width;
                    $newHeight = $height;
                }
                
                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                
                // Handle transparency for WebP output
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                
                imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($sourceImage);
                $sourceImage = $newImage;

                // Save as WebP
                if (imagewebp($sourceImage, $destination, 85)) { // Quality 85
                    imagedestroy($sourceImage);
                    ob_clean();
                    echo json_encode(['status' => 'success', 'file' => $webPath . $filename, 'type' => 'image']);
                    exit;
                } else {
                    imagedestroy($sourceImage);
                    ob_clean();
                    echo json_encode(['status' => 'error', 'message' => 'Failed to save converted WebP image.']);
                    exit;
                }
            } else {
                 // Fallback if GD fails to open image (e.g. corrupt or unsupported by local GD)
                 if (move_uploaded_file($file['tmp_name'], $destination)) {
                    ob_clean();
                    echo json_encode(['status' => 'success', 'file' => $webPath . $filename, 'type' => 'image', 'warning' => 'Image saved but not optimized (GD error).']);
                 } else {
                    ob_clean();
                    echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
                 }
            }
        } else {
            // --- VIDEO HANDLING (Direct Upload) ---
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                ob_clean();
                echo json_encode(['status' => 'success', 'file' => $webPath . $filename, 'type' => 'video']);
            } else {
                ob_clean();
                echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded video.']);
            }
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
