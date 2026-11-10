<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/leads-store.php';

$config_file = __DIR__ . '/../assets/data/config.json';
$tokens_file = __DIR__ . '/../assets/data/reset_tokens.json';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_sec_fetch_site(false);

    $ip = admin_client_ip();
    $csrfToken = (string)($_POST['csrf_token'] ?? '');
    if ($csrfToken === '' || !hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrfToken)) {
        http_response_code(403);
        $error = "Nieprawidłowy token zabezpieczający. Odśwież stronę.";
    } elseif (is_ip_banned($ip) || !rate_limit('admin_forgot_pw_' . $ip, 3, 900)) {
        http_response_code(429);
        $error = "Zbyt wiele prób resetowania hasła. Spróbuj ponownie za 15 minut.";
    } else {
        $rawEmail = str_replace(["\r", "\n", "\0", " "], '', (string)($_POST['email'] ?? ''));
        $email = strtolower((string)filter_var($rawEmail, FILTER_SANITIZE_EMAIL));

        $config_data = read_json_file($config_file);
        $matchedUserKey = null;
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && isset($config_data['users']) && is_array($config_data['users'])) {
            foreach (array_keys($config_data['users']) as $uKey) {
                if (strtolower(trim((string)$uKey)) === $email) {
                    $matchedUserKey = (string)$uKey;
                    break;
                }
            }
        }

        if ($matchedUserKey !== null) {
            // Generujemy jednorazowy kryptograficzny token 256-bit, ale na dysku przechowujemy WYŁĄCZNIE jego skrót SHA-256!
            $resetToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $resetToken);
            $now = time();
            $expiry = $now + (30 * 60); // 30 minut

            $fp = @fopen($tokens_file, 'c+b');
            if ($fp && @flock($fp, LOCK_EX)) {
                rewind($fp);
                $rawTokens = stream_get_contents($fp);
                $tokens = (is_string($rawTokens) && $rawTokens !== '') ? json_decode($rawTokens, true) : [];
                if (!is_array($tokens)) {
                    $tokens = [];
                }

                // Oczyszczenie wygasłych tokenów i wcześniejszych tokenów tego samego użytkownika
                foreach ($tokens as $k => $entry) {
                    if (!is_array($entry) || (int)($entry['expiry'] ?? 0) <= $now || strtolower((string)($entry['email'] ?? '')) === $email) {
                        unset($tokens[$k]);
                    }
                }

                $tokens[$tokenHash] = [
                    'email' => $matchedUserKey,
                    'expiry' => $expiry,
                    'ip_masked' => mask_ip($ip),
                ];

                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, (string)json_encode($tokens, JSON_PRETTY_PRINT));
                fflush($fp);
                @flock($fp, LOCK_UN);
                fclose($fp);

                // Ścisła biała lista hostów przeciwko Password Reset Poisoning
                $rawHost = strtolower(trim((string)($_SERVER['HTTP_HOST'] ?? 'raricart.pl')));
                $allowedHosts = ['raricart.pl', 'www.raricart.pl', 'test.raricart.pl', 'localhost'];
                $safeHost = in_array($rawHost, $allowedHosts, true) ? $rawHost : 'raricart.pl';
                $scheme = ($safeHost === 'localhost' && empty($_SERVER['HTTPS'])) ? 'http' : 'https';
                $resetLink = $scheme . "://" . $safeHost . "/admin/reset_password.php?token=" . urlencode($resetToken);

                $rawSubject = "Reset Hasła - Raricart Admin";
                $subject = '=?UTF-8?B?' . base64_encode($rawSubject) . '?=';
                $body = "Witaj,\n\nAby zresetować hasło dla konta {$matchedUserKey}, kliknij w poniższy link:\n\n{$resetLink}\n\nLink wygaśnie za 30 minut.\n\nJeśli to nie Ty, zignoruj tę wiadomość.";
                $headers = "MIME-Version: 1.0\r\n"
                    . "From: Raricart Admin <no-reply@raricart.pl>\r\n"
                    . "Content-Type: text/plain; charset=UTF-8\r\n"
                    . "Content-Transfer-Encoding: 8bit\r\n";

                if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1') {
                    @mail($matchedUserKey, $subject, $body, $headers);
                }
            } elseif ($fp) {
                fclose($fp);
            }
        }

        // Jednolita odpowiedź zapobiegająca enumeracji kont e-mail
        $message = "Jeśli podany adres e-mail istnieje w bazie, wysłaliśmy instrukcje resetowania hasła.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Hasła - Raricart</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="auth-page">
    <div class="login-box">
        <h2 class="auth-heading">Reset Hasła</h2>
        <p class="auth-subtext">Podaj swój adres e-mail, aby otrzymać jednorazowy link resetujący.</p>

        <?php if ($error): ?>
            <div class="auth-msg error" role="alert">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="auth-msg success" role="status">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="email" name="email" placeholder="Twój E-mail" required autocomplete="email" class="status-input auth-input btn-block">
            <button type="submit" class="action-btn btn-block">Wyślij Link</button>
        </form>

        <p class="auth-footer">
            <a href="index.php" class="auth-link">&larr; Wróć do logowania</a>
        </p>
    </div>
</body>
</html>
