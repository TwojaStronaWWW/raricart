<?php
require_once __DIR__ . '/auth.php';
$config_file = __DIR__ . '/../assets/data/config.json';
$tokens_file = __DIR__ . '/../assets/data/reset_tokens.json';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Check Token
$tokens = file_exists($tokens_file) ? json_decode(file_get_contents($tokens_file), true) : [];
if(!is_array($tokens)) $tokens = [];

$email = '';
if (isset($tokens[$token])) {
    if (time() > $tokens[$token]['expiry']) {
        $error = "Link wygasł. Poproś o nowy.";
        unset($tokens[$token]);
        file_put_contents($tokens_file, json_encode($tokens, JSON_PRETTY_PRINT), LOCK_EX);
    } else {
        $email = $tokens[$token]['email'];
    }
} else {
    $error = "Nieprawidłowy link.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    // CSRF protection
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (empty($csrfToken) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
        $error = "Nieprawidłowy token zabezpieczający. Odśwież stronę.";
    } else {
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (strlen($pass) < 8) {
        $error = "Hasło za krótkie (min 8 znaków).";
    } elseif ($pass !== $confirm) {
        $error = "Hasła nie są identyczne.";
    } else {
        // Update Config
        $config = json_decode(file_get_contents($config_file), true);
        $config['users'][$email] = password_hash($pass, PASSWORD_DEFAULT);
        file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT), LOCK_EX);
        
        // Consumed Token
        unset($tokens[$token]);
        file_put_contents($tokens_file, json_encode($tokens, JSON_PRETTY_PRINT), LOCK_EX);
        
        session_regenerate_id(true);
        $success = "Hasło zmienione pomyślnie! <br><a href='index.php'>Zaloguj się teraz</a>";
    }
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
        <h2 style="margin-top:0;">Nowe Hasło</h2>
        <p style="color:#666; font-size:0.9rem;">Dla użytkownika: <strong><?php echo htmlspecialchars($email); ?></strong></p>
        
        <?php if ($error): ?>
            <div class="auth-msg error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="auth-msg success">
                <?php echo $success; // Allow HTML link ?>
            </div>
        <?php elseif ($email): ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="password" name="password" placeholder="Nowe Hasło" required class="status-input" style="width:100%; margin-bottom:1rem;">
                <input type="password" name="confirm" placeholder="Potwierdź Hasło" required class="status-input" style="width:100%; margin-bottom:1.5rem;">
                <button type="submit" class="action-btn" style="width:100%;">Zmień Hasło</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
