<?php
require_once 'auth.php';
check_login();

header('Content-Type: application/json');

// Check CSRF
check_csrf();

$config_file = __DIR__ . '/../assets/data/config.json';
$currentUser = get_current_user_email();

$input = json_decode(file_get_contents('php://input'), true);
$oldPass = $input['old_password'] ?? '';
$newPass = $input['new_password'] ?? '';
$confirm = $input['confirm_password'] ?? '';

if (!$oldPass || !$newPass || !$confirm) {
    echo json_encode(['status' => 'error', 'message' => 'Wypełnij wszystkie pola.']);
    exit;
}

if ($newPass !== $confirm) {
    echo json_encode(['status' => 'error', 'message' => 'Nowe hasła nie są identyczne.']);
    exit;
}

if (strlen($newPass) < 5) {
    echo json_encode(['status' => 'error', 'message' => 'Nowe hasło musi mieć min. 5 znaków.']);
    exit;
}

// Load Config
$config = json_decode(file_get_contents($config_file), true);

if (!isset($config['users'][$currentUser])) {
    echo json_encode(['status' => 'error', 'message' => 'Błąd użytkownika sesji.']);
    exit;
}

// Verify Old Password
if (!password_verify($oldPass, $config['users'][$currentUser])) {
    echo json_encode(['status' => 'error', 'message' => 'Stare hasło jest nieprawidłowe.']);
    exit;
}

// Update Password
$newHash = password_hash($newPass, PASSWORD_DEFAULT);

if (is_array($config['users'][$currentUser])) {
    $config['users'][$currentUser]['hash'] = $newHash;
    unset($config['users'][$currentUser]['force_change']);
} else {
    $config['users'][$currentUser] = $newHash;
}

if (file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT))) {
    unset($_SESSION['must_change_password']);
    echo json_encode(['status' => 'success', 'message' => 'Hasło zostało zmienione. Możesz teraz korzystać z panelu.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu pliku konfiguracji.']);
}
?>
