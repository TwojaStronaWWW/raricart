<?php
session_start();
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
        file_put_contents($tokens_file, json_encode($tokens));
    } else {
        $email = $tokens[$token]['email'];
    }
} else {
    $error = "Nieprawidłowy link.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (strlen($pass) < 5) {
        $error = "Hasło za krótkie (min 5 znaków).";
    } elseif ($pass !== $confirm) {
        $error = "Hasła nie są identyczne.";
    } else {
        // Update Config
        $config = json_decode(file_get_contents($config_file), true);
        $config['users'][$email] = password_hash($pass, PASSWORD_DEFAULT);
        file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
        
        // Consumed Token
        unset($tokens[$token]);
        file_put_contents($tokens_file, json_encode($tokens));
        
        $success = "Hasło zmienione pomyślnie! <br><a href='index.php'>Zaloguj się teraz</a>";
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
    <style>
        body { background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin:0; }
        .box { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="margin-top:0;">Nowe Hasło</h2>
        <p style="color:#666; font-size:0.9rem;">Dla użytkownika: <strong><?php echo htmlspecialchars($email); ?></strong></p>
        
        <?php if ($error): ?>
            <div style="background:#ffebee; color:#c62828; padding:10px; border-radius:4px; margin-bottom:1rem; font-size:0.9rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:4px; margin-bottom:1rem; font-size:0.9rem;">
                <?php echo $success; // Allow HTML link ?>
            </div>
        <?php elseif ($email): ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Nowe Hasło" required class="status-input" style="width:100%; box-sizing:border-box; margin-bottom:1rem;">
                <input type="password" name="confirm" placeholder="Potwierdź Hasło" required class="status-input" style="width:100%; box-sizing:border-box; margin-bottom:1.5rem;">
                <button type="submit" class="action-btn" style="width:100%;">Zmień Hasło</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
