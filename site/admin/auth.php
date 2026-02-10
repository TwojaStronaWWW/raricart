<?php
// Security: Secure Session Settings
// Prevent JS access to session cookie, Enforce HTTPS if possible, Strict SameSite
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
session_set_cookie_params([
    'lifetime' => 0, // Session cookie
    'path' => '/',
    'domain' => '',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// Configuration File Path
$config_file = __DIR__ . '/../assets/data/config.json';

// Check if Config Exists (First Run)
if (!file_exists($config_file)) {
    header("Location: setup.php");
    exit();
}

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to check CSRF
function check_csrf() {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF Token']);
        exit();
    }
}

// Function to check login
function check_login() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: index.php");
        exit();
    }
}

function get_current_user_email() {
    return $_SESSION['user_email'] ?? 'unknown';
}

// If this file is included, it just provides the function.
// If accessing dashboard, we call it.
?>
