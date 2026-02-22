<?php
session_start();
$config_file = __DIR__ . '/../assets/data/config.json';
$tokens_file = __DIR__ . '/../assets/data/reset_tokens.json';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    // Validate Email exists
    $config_data = json_decode(file_get_contents($config_file), true);
    
    if (isset($config_data['users'][$email])) {
        // Generate Token
        $token = bin2hex(random_bytes(16));
        $expiry = time() + (30 * 60); // 30 mins
        
        // Load Existing Tokens
        $tokens = file_exists($tokens_file) ? json_decode(file_get_contents($tokens_file), true) : [];
        if(!is_array($tokens)) $tokens = [];
        
        // Save Token
        $tokens[$token] = ['email' => $email, 'expiry' => $expiry];
        file_put_contents($tokens_file, json_encode($tokens));
        
        // Send Email
        $resetLink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
        
        $subject = "Reset Hasła - Raricart Admin";
        $body = "Witaj,\n\nAby zresetować hasło dla konta $email, kliknij w poniższy link:\n\n$resetLink\n\nLink wygaśnie za 30 minut.\n\nJeśli to nie Ty, zignoruj tę wiadomość.";
        $headers = "From: Admin <no-reply@raricart.pl>";
        
        mail($email, $subject, $body, $headers);
        
        $message = "Jeśli podany email istnieje w bazie, wysłaliśmy instrukcje resetowania hasła.";
    } else {
        // Security: Don't reveal if user exists or not, OR just same message
        $message = "Jeśli podany email istnieje w bazie, wysłaliśmy instrukcje resetowania hasła.";
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
    <style>
        body { background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin:0; }
        .box { background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="margin-top:0;">Reset Hasła</h2>
        <p style="color:#666; font-size:0.9rem;">Podaj swój adres e-mail, aby otrzymać link.</p>
        
        <?php if ($message): ?>
            <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:4px; margin-bottom:1rem; font-size:0.9rem;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Twój E-mail" required class="status-input" style="width:100%; box-sizing:border-box; margin-bottom:1rem;">
            <button type="submit" class="action-btn" style="width:100%;">Wyślij Link</button>
        </form>
        
        <p style="margin-top:1.5rem;">
            <a href="index.php" style="color:#666; text-decoration:none;">&larr; Wróć do logowania</a>
        </p>
    </div>
</body>
</html>
