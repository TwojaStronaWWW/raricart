<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/leads-store.php';

$config_file = __DIR__ . '/../assets/data/config.json';

// Stały skrót bcrypt do wyrównania czasu odpowiedzi przy nieistniejącym koncie (ochrona przed enumeracją loginów)
const ADMIN_DUMMY_BCRYPT_HASH = '$2y$12$R9h/cIPz0gi.URNNX3kh2OPST9/PgBkqquzi.Ss7KIUgO2t0jWMUW'; // gitleaks:allow

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

if (!file_exists($config_file)) {
    header("Location: setup.php");
    exit();
}

$error = '';
if (!empty($_GET['expired'])) {
    $error = 'Sesja wygasła z powodu bezczynności. Zaloguj się ponownie.';
}

// 15-minutowy IP Penalty Box z prawdziwym IP za Cloudflare i blokadą plikową LOCK_EX
$attemptsFile = __DIR__ . '/../assets/data/login_attempts.json';
$ip = admin_client_ip();
$ipKey = hash('sha256', $ip);
$maxAttempts = 5;
$lockoutSeconds = 900; // 15 min

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_sec_fetch_site(false);

    $csrfToken = (string)($_POST['csrf_token'] ?? '');
    if ($csrfToken === '' || !hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrfToken)) {
        $error = "Nieprawidłowy token zabezpieczający. Odśwież stronę.";
    } elseif (is_ip_banned($ip)) {
        http_response_code(429);
        $error = "Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za 15 minut.";
    } else {
        $now = time();
        $fpAttempts = @fopen($attemptsFile, 'c+b');
        $attempts = [];
        $isLocked = false;

        if ($fpAttempts && @flock($fpAttempts, LOCK_EX)) {
            rewind($fpAttempts);
            $rawAttempts = stream_get_contents($fpAttempts);
            if (is_string($rawAttempts) && $rawAttempts !== '') {
                $decodedAttempts = json_decode($rawAttempts, true);
                if (is_array($decodedAttempts)) {
                    $attempts = $decodedAttempts;
                }
            }

            foreach ($attempts as $k => $v) {
                $lockedUntil = (int)($v['locked_until'] ?? (($v['time'] ?? 0) + $lockoutSeconds));
                if ($lockedUntil <= $now) {
                    unset($attempts[$k]);
                }
            }

            if (isset($attempts[$ipKey]) && (int)($attempts[$ipKey]['count'] ?? 0) >= $maxAttempts) {
                $isLocked = true;
            }

            if ($isLocked) {
                ftruncate($fpAttempts, 0);
                rewind($fpAttempts);
                fwrite($fpAttempts, (string)json_encode($attempts, JSON_PRETTY_PRINT));
                fflush($fpAttempts);
                @flock($fpAttempts, LOCK_UN);
                fclose($fpAttempts);

                ban_client_ip($ip, $lockoutSeconds, 'admin_login_lockout');
                http_response_code(429);
                $error = "Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za 15 minut.";
            } else {
                $rawEmail = trim((string)($_POST['email'] ?? ''));
                $email = strtolower((string)filter_var($rawEmail, FILTER_SANITIZE_EMAIL));
                $password = (string)($_POST['password'] ?? '');

                $config_data = read_json_file($config_file);
                $usersMap = [];
                if (isset($config_data['users']) && is_array($config_data['users'])) {
                    foreach ($config_data['users'] as $uEmail => $uVal) {
                        $usersMap[strtolower(trim((string)$uEmail))] = ['key' => (string)$uEmail, 'data' => $uVal];
                    }
                }

                $matchedEntry = ($email !== '' && isset($usersMap[$email])) ? $usersMap[$email] : null;
                $storedHash = '';
                $forceChange = false;
                if ($matchedEntry !== null) {
                    $userData = $matchedEntry['data'];
                    $storedHash = is_array($userData) ? (string)($userData['hash'] ?? '') : (string)$userData;
                    $forceChange = is_array($userData) && !empty($userData['force_change']);
                }

                $hasValidBcrypt = ($storedHash !== '' && strpos($storedHash, '$2') === 0);
                $hashToVerify = $hasValidBcrypt ? $storedHash : ADMIN_DUMMY_BCRYPT_HASH;
                $verified = password_verify($password, $hashToVerify) && $matchedEntry !== null && $hasValidBcrypt;

                if ($verified) {
                    unset($attempts[$ipKey]);
                    ftruncate($fpAttempts, 0);
                    rewind($fpAttempts);
                    fwrite($fpAttempts, (string)json_encode($attempts, JSON_PRETTY_PRINT));
                    fflush($fpAttempts);
                    @flock($fpAttempts, LOCK_UN);
                    fclose($fpAttempts);

                    session_regenerate_id(true);
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_email'] = $matchedEntry['key'];
                    $_SESSION['last_activity'] = $now;
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    if ($forceChange) {
                        $_SESSION['must_change_password'] = true;
                    } else {
                        unset($_SESSION['must_change_password']);
                    }

                    header("Location: dashboard.php");
                    exit();
                }

                $prevCount = (int)($attempts[$ipKey]['count'] ?? 0) + 1;
                $attempts[$ipKey] = [
                    'count' => $prevCount,
                    'time' => $now,
                    'locked_until' => $now + $lockoutSeconds,
                    'ip_masked' => mask_ip($ip),
                ];

                ftruncate($fpAttempts, 0);
                rewind($fpAttempts);
                fwrite($fpAttempts, (string)json_encode($attempts, JSON_PRETTY_PRINT));
                fflush($fpAttempts);
                @flock($fpAttempts, LOCK_UN);
                fclose($fpAttempts);

                if ($prevCount >= $maxAttempts) {
                    ban_client_ip($ip, $lockoutSeconds, 'admin_login_lockout');
                    http_response_code(429);
                    $error = "Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za 15 minut.";
                } else {
                    // Jednolity komunikat błędu (brak enumeracji istniejących adresów e-mail)
                    http_response_code(401);
                    $error = "Nieprawidłowy adres e-mail lub hasło.";
                }
            }
        } else {
            if ($fpAttempts) {
                fclose($fpAttempts);
            }
            http_response_code(503);
            $error = "Usługa chwilowo niedostępna. Spróbuj ponownie za chwilę.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Raricart Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="auth-page">
    <div class="login-box">
        <div class="logo">Raricart Admin</div>
        <?php if ($error): ?>
            <div class="auth-msg error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="email" name="email" placeholder="E-mail (np. kontakt@raricart.pl)" required autofocus autocomplete="username" class="status-input auth-input">
            <input type="password" name="password" placeholder="Hasło" required autocomplete="current-password" class="status-input auth-input-lg">
            <button type="submit" class="action-btn btn-block">Zaloguj się</button>
        </form>
        <p class="auth-footer">
            <a href="forgot_password.php" class="auth-link">Zapomniałeś hasła?</a>
        </p>
    </div>
</body>
</html>
