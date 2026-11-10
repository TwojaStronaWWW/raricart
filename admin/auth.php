<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/rate-limit.php';

const SESSION_TTL = 1800; // 30 minut bezczynności

// Security: Hardened PHP Session Configuration (OWASP ASVS / [REF-05])
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');

if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.use_strict_mode', '1');
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.cookie_samesite', 'Strict');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    session_start();
}

// Configuration File Path
$config_file = __DIR__ . '/../assets/data/config.json';

// Check if Config Exists (First Run)
if (!file_exists($config_file) && basename((string)($_SERVER['SCRIPT_NAME'] ?? '')) !== 'setup.php') {
    header("Location: setup.php");
    exit();
}

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function admin_client_ip(): string {
    return get_client_ip();
}

function verify_sec_fetch_site(bool $jsonResponse = false): void {
    if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && strtolower(trim((string)$_SERVER['HTTP_SEC_FETCH_SITE'])) === 'cross-site') {
        http_response_code(403);
        if ($jsonResponse) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'error', 'message' => 'Cross-Site Request Blocked']);
        } else {
            echo '403 Forbidden (Cross-Site Request Blocked)';
        }
        exit();
    }
}

function require_post_method(bool $jsonResponse = true): void {
    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        if ($jsonResponse) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'error', 'message' => 'Niedozwolona metoda HTTP. Wymagane POST.']);
        } else {
            echo '405 Method Not Allowed';
        }
        exit();
    }
}

function check_csrf(bool $jsonResponse = true): void {
    verify_sec_fetch_site($jsonResponse);
    $token = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '');
    $sessionToken = (string)($_SESSION['csrf_token'] ?? '');
    if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
        http_response_code(403);
        if ($jsonResponse) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF Token']);
        } else {
            echo '403 Forbidden (Invalid CSRF Token)';
        }
        exit();
    }
}

function is_admin_json_request(): bool {
    $accept = strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? ''));
    $contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
    $hasCsrfHeader = isset($_SERVER['HTTP_X_CSRF_TOKEN']);
    return $hasCsrfHeader
        || strpos($accept, 'application/json') !== false
        || strpos($contentType, 'application/json') !== false;
}

function destroy_admin_session(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'] ?? '/',
                'domain' => $params['domain'] ?? '',
                'secure' => (bool)($params['secure'] ?? false),
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );
    }
    @session_unset();
    @session_destroy();
}

/**
 * Weryfikuje zalogowanie, TTL sesji oraz serwerowe wymuszenie zmiany hasła (must_change_password).
 */
function check_login(bool $allowPasswordChange = false): void {
    verify_sec_fetch_site(is_admin_json_request());

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        if (is_admin_json_request()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'error', 'message' => 'Sesja wygasła. Zaloguj się ponownie.']);
        } else {
            header("Location: index.php");
        }
        exit();
    }

    $now = time();
    if (isset($_SESSION['last_activity']) && ($now - (int)$_SESSION['last_activity'] > SESSION_TTL)) {
        destroy_admin_session();
        if (is_admin_json_request()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'error', 'message' => 'Sesja wygasła z powodu bezczynności.']);
        } else {
            header("Location: index.php?expired=1");
        }
        exit();
    }
    $_SESSION['last_activity'] = $now;

    // Serwerowe wymuszenie must_change_password na wszystkich chronionych endpointach
    if (!empty($_SESSION['must_change_password']) && !$allowPasswordChange) {
        if (is_admin_json_request() || strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'status' => 'error',
                'message' => 'Wymagana zmiana hasła tymczasowego przed dostępem do funkcji administracyjnych.'
            ]);
        } else {
            header("Location: dashboard.php");
        }
        exit();
    }
}

function get_current_user_email(): string {
    return (string)($_SESSION['user_email'] ?? 'unknown');
}
