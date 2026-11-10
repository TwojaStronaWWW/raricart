<?php
declare(strict_types=1);

$config_file = __DIR__ . '/../assets/data/config.json';
$data_dir = __DIR__ . '/../assets/data';

// Zablokuj dostęp natychmiast, jeśli konfiguracja już istnieje
if (file_exists($config_file)) {
    http_response_code(403);
    exit("Setup został już wykonany. Użyj narzędzia CLI bin/set-admin-password.php na serwerze.");
}

require_once __DIR__ . '/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_sec_fetch_site(false);

    $ip = admin_client_ip();
    $csrfToken = (string)($_POST['csrf_token'] ?? '');
    if ($csrfToken === '' || !hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrfToken)) {
        http_response_code(403);
        $error = "Nieprawidłowy token zabezpieczający. Odśwież stronę.";
    } elseif (!rate_limit('admin_setup_' . $ip, 5, 900)) {
        http_response_code(429);
        $error = "Zbyt wiele prób konfiguracji. Spróbuj ponownie później.";
    } else {
        $email = strtolower(trim((string)filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL)));
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Podaj prawidłowy adres e-mail administratora.";
        } elseif (strlen($password) < 8) {
            $error = "Hasło musi mieć co najmniej 8 znaków.";
        } elseif ($password !== $confirm) {
            $error = "Hasła nie są identyczne.";
        } else {
            if (!is_dir($data_dir)) {
                @mkdir($data_dir, 0750, true);
            }

            $config = [
                'users' => [
                    $email => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                ],
                'setup_date' => date('Y-m-d H:i:s'),
            ];

            // Atomowy zapis z wyłączną blokadą LOCK_EX i flagą tworzenia tylko jeśli plik nie powstał równolegle
            $fp = @fopen($config_file, 'xb');
            if ($fp !== false) {
                if (@flock($fp, LOCK_EX)) {
                    fwrite($fp, (string)json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    fflush($fp);
                    @flock($fp, LOCK_UN);
                }
                fclose($fp);
                @chmod($config_file, 0600);
                session_regenerate_id(true);
                $success = "Konto administratora utworzone pomyślnie! Przekierowanie do logowania...";
                header("Refresh: 2; url=index.php");
            } else {
                $error = "Błąd zapisu pliku konfiguracyjnego. Sprawdź uprawnienia folderu assets/data.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pierwsza Konfiguracja - Raricart</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="auth-page">
    <div class="login-box">
        <h2 class="auth-heading">Witaj w Raricart Admin</h2>
        <p class="auth-subtext">Pierwsze uruchomienie. Utwórz główne konto administratora.</p>

        <?php if ($error !== ''): ?>
            <div class="auth-msg error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="auth-msg success" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="email" name="email" placeholder="E-mail administratora" required autocomplete="username" class="status-input auth-input btn-block">
                <input type="password" name="password" placeholder="Hasło (min. 8 znaków)" minlength="8" required autocomplete="new-password" class="status-input auth-input btn-block">
                <input type="password" name="confirm" placeholder="Potwierdź Hasło" minlength="8" required autocomplete="new-password" class="status-input auth-input-lg btn-block">
                <button type="submit" class="action-btn btn-block">Utwórz konto</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
