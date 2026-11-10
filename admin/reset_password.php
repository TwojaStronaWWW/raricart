<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/leads-store.php';

$config_file = __DIR__ . '/../assets/data/config.json';
$tokens_file = __DIR__ . '/../assets/data/reset_tokens.json';

$rawToken = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$tokenHash = (preg_match('/^[a-f0-9]{32,128}$/', $rawToken) === 1) ? hash('sha256', $rawToken) : '';

$error = '';
$passwordResetDone = false;
$email = '';
$ip = admin_client_ip();

if (!rate_limit('admin_reset_pw_' . $ip, 10, 900)) {
    http_response_code(429);
    $error = "Zbyt wiele prób. Spróbuj ponownie za 15 minut.";
} elseif ($tokenHash === '' || !file_exists($tokens_file)) {
    $error = "Nieprawidłowy lub wygasły link resetujący.";
} else {
    $fp = @fopen($tokens_file, 'c+b');
    if ($fp && @flock($fp, LOCK_EX)) {
        rewind($fp);
        $rawTokens = stream_get_contents($fp);
        $tokens = (is_string($rawTokens) && $rawTokens !== '') ? json_decode($rawTokens, true) : [];
        if (!is_array($tokens)) {
            $tokens = [];
        }

        // Znajdź pasujący skrót SHA-256 w stałym czasie względem kluczy
        $matchedKey = null;
        foreach (array_keys($tokens) as $storedHash) {
            if (is_string($storedHash) && strlen($storedHash) === 64 && hash_equals($storedHash, $tokenHash)) {
                $matchedKey = $storedHash;
                break;
            }
        }

        if ($matchedKey === null) {
            $error = "Nieprawidłowy lub wygasły link resetujący.";
        } elseif (time() > (int)($tokens[$matchedKey]['expiry'] ?? 0)) {
            $error = "Link wygasł. Poproś o nowy.";
            unset($tokens[$matchedKey]);
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, (string)json_encode($tokens, JSON_PRETTY_PRINT));
            fflush($fp);
        } else {
            $email = (string)($tokens[$matchedKey]['email'] ?? '');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error && $matchedKey !== null && $email !== '') {
            verify_sec_fetch_site(false);
            $csrfToken = (string)($_POST['csrf_token'] ?? '');
            $pass = (string)($_POST['password'] ?? '');
            $confirm = (string)($_POST['confirm'] ?? '');

            if ($csrfToken === '' || !hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrfToken)) {
                http_response_code(403);
                $error = "Nieprawidłowy token zabezpieczający. Odśwież stronę.";
            } elseif (strlen($pass) < 8) {
                $error = "Hasło za krótkie (wymagane min. 8 znaków).";
            } elseif ($pass !== $confirm) {
                $error = "Hasła nie są identyczne.";
            } else {
                $newHash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
                $tx = update_json_file($config_file, static function (array &$config) use ($email, $newHash): bool {
                    if (!isset($config['users']) || !is_array($config['users'])) {
                        $config['users'] = [];
                    }
                    $config['users'][$email] = $newHash;
                    return true;
                }, false);

                if ($tx['ok']) {
                    unset($tokens[$matchedKey]);
                    ftruncate($fp, 0);
                    rewind($fp);
                    fwrite($fp, (string)json_encode($tokens, JSON_PRETTY_PRINT));
                    fflush($fp);

                    session_regenerate_id(true);
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $passwordResetDone = true;
                } else {
                    $error = "Błąd zapisu pliku konfiguracji.";
                }
            }
        }

        @flock($fp, LOCK_UN);
        fclose($fp);
    } else {
        if ($fp) {
            fclose($fp);
        }
        $error = "Usługa chwilowo niedostępna.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ustaw Nowe Hasło - Raricart</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="auth-page">
    <div class="login-box">
        <h2 class="auth-heading">Nowe Hasło</h2>
        <?php if ($email !== '' && !$passwordResetDone): ?>
            <p class="auth-subtext">Dla konta: <strong><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="auth-msg error" role="alert">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($passwordResetDone): ?>
            <div class="auth-msg success" role="status">
                Hasło zmienione pomyślnie! <br>
                <a href="index.php" class="auth-link">Zaloguj się teraz</a>
            </div>
        <?php elseif ($email !== '' && !$error): ?>
            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($rawToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="password" name="password" placeholder="Nowe Hasło (min. 8 znaków)" minlength="8" required autocomplete="new-password" class="status-input auth-input btn-block">
                <input type="password" name="confirm" placeholder="Potwierdź Hasło" minlength="8" required autocomplete="new-password" class="status-input auth-input-lg btn-block">
                <button type="submit" class="action-btn btn-block">Zmień Hasło</button>
            </form>
        <?php else: ?>
            <p class="auth-footer">
                <a href="forgot_password.php" class="auth-link">Wygeneruj nowy link resetujący</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
