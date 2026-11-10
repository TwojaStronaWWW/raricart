<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_post_method(true);
check_login(true);
check_csrf(true);

header('Content-Type: application/json; charset=UTF-8');

$config_file = __DIR__ . '/../assets/data/config.json';
$currentUser = get_current_user_email();

$rawBody = file_get_contents('php://input', false, null, 0, 16384);
$input = json_decode((string)$rawBody, true, 8);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowy format żądania.']);
    exit;
}

$oldPass = (string)($input['old_password'] ?? '');
$newPass = (string)($input['new_password'] ?? '');
$confirm = (string)($input['confirm_password'] ?? '');

if ($oldPass === '' || $newPass === '' || $confirm === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Wypełnij wszystkie pola.']);
    exit;
}

if ($newPass !== $confirm) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nowe hasła nie są identyczne.']);
    exit;
}

if (strlen($newPass) < 8) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nowe hasło musi mieć co najmniej 8 znaków.']);
    exit;
}

if (hash_equals($oldPass, $newPass)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nowe hasło musi różnić się od obecnego.']);
    exit;
}

$fp = @fopen($config_file, 'c+b');
if (!$fp || !@flock($fp, LOCK_EX)) {
    if ($fp) {
        fclose($fp);
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Błąd blokady pliku konfiguracji.']);
    exit;
}

rewind($fp);
$rawConfig = stream_get_contents($fp);
$config = (is_string($rawConfig) && $rawConfig !== '') ? json_decode($rawConfig, true) : [];
if (!is_array($config) || !isset($config['users']) || !is_array($config['users']) || !isset($config['users'][$currentUser])) {
    @flock($fp, LOCK_UN);
    fclose($fp);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Błąd użytkownika sesji.']);
    exit;
}

$userEntry = $config['users'][$currentUser];
$existingHash = is_array($userEntry) ? (string)($userEntry['hash'] ?? '') : (string)$userEntry;
if ($existingHash === '' || strpos($existingHash, '$2') !== 0 || !password_verify($oldPass, $existingHash)) {
    @flock($fp, LOCK_UN);
    fclose($fp);
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Stare hasło jest nieprawidłowe.']);
    exit;
}

$newHash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
if (is_array($config['users'][$currentUser])) {
    $config['users'][$currentUser]['hash'] = $newHash;
    unset($config['users'][$currentUser]['force_change']);
} else {
    $config['users'][$currentUser] = $newHash;
}

$encoded = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
ftruncate($fp, 0);
rewind($fp);
$written = fwrite($fp, (string)$encoded);
fflush($fp);
@flock($fp, LOCK_UN);
fclose($fp);

if ($written !== false) {
    unset($_SESSION['must_change_password']);
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    echo json_encode([
        'status' => 'success',
        'message' => 'Hasło zostało zmienione. Możesz teraz korzystać z panelu.',
        'csrf_token' => $_SESSION['csrf_token']
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu pliku konfiguracji.']);
}
