<?php
require_once __DIR__ . '/auth.php';

$config_file = __DIR__ . '/../assets/data/config.json';

// If already logged in, go to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

// Check if setup is needed
if (!file_exists($config_file)) {
    header("Location: setup.php");
    exit();
}

$error = '';

// Brute-force protection: rate-limit per IP
$attemptsFile = __DIR__ . '/../assets/data/login_attempts.json';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ipKey = md5($ip);
$maxAttempts = 5;
$lockoutSeconds = 900; // 15 min

$attempts = [];
if (file_exists($attemptsFile)) {
    $attempts = json_decode((string)file_get_contents($attemptsFile), true) ?: [];
}

// Clean expired entries (older than lockout period)
$now = time();
foreach ($attempts as $k => $v) {
    if (($v['time'] ?? 0) < $now - $lockoutSeconds) {
        unset($attempts[$k]);
    }
}

$isLocked = isset($attempts[$ipKey]) && ($attempts[$ipKey]['count'] ?? 0) >= $maxAttempts;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (empty($csrfToken) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
        $error = "Nieprawidłowy token zabezpieczający. Odśwież stronę.";
    } elseif ($isLocked) {
        $error = "Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za 15 minut.";
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        // Read config
        $config_data = json_decode(file_get_contents($config_file), true);
        
        if (isset($config_data['users'][$email])) {
            $userData = $config_data['users'][$email];
            $hash = is_array($userData) ? $userData['hash'] : $userData;
            
            if (password_verify($password, $hash)) {
                // Success: clear attempts for this IP
                unset($attempts[$ipKey]);
                file_put_contents($attemptsFile, json_encode($attempts, JSON_PRETTY_PRINT), LOCK_EX);

                session_regenerate_id(true);
                $_SESSION['logged_in'] = true;
                $_SESSION['user_email'] = $email;
                
                // Handle forced password change
                if (is_array($userData) && isset($userData['force_change']) && $userData['force_change'] === true) {
                    $_SESSION['must_change_password'] = true;
                } else {
                    unset($_SESSION['must_change_password']);
                }
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Nieprawidłowe hasło.";
            }
        } else {
            $error = "Nieznany użytkownik.";
        }

        // Record failed attempt
        if ($error) {
            if (!isset($attempts[$ipKey])) {
                $attempts[$ipKey] = ['count' => 0, 'time' => $now];
            }
            $attempts[$ipKey]['count']++;
            $attempts[$ipKey]['time'] = $now;
            file_put_contents($attemptsFile, json_encode($attempts, JSON_PRETTY_PRINT), LOCK_EX);
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
    <link rel="stylesheet" href="admin.css"> <!-- Use admin.css for consistency -->
</head>
<body class="auth-page">
    <div class="login-box">
        <div class="logo">Raricart Admin</div>
        <?php if ($error): ?>
            <div class="auth-msg error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <input type="email" name="email" placeholder="E-mail (np. kontakt@raricart.pl)" required autofocus class="status-input" style="margin-bottom:1rem;">
            <input type="password" name="password" placeholder="Hasło" required class="status-input" style="margin-bottom:1.5rem;">
            <button type="submit" class="action-btn" style="width:100%;">Zaloguj się</button>
        </form>
        <p style="margin-top:1.5rem; font-size:0.9rem;">
            <a href="forgot_password.php" style="color:#666; text-decoration:none;">Zapomniałeś hasła?</a>
        </p>
    </div>
</body>
</html>
