<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Read config
    $config_data = json_decode(file_get_contents($config_file), true);
    
    if (isset($config_data['users'][$email])) {
        $userData = $config_data['users'][$email];
        $hash = is_array($userData) ? $userData['hash'] : $userData;
        
        if (password_verify($password, $hash)) {
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
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Raricart Admin</title>
    <link rel="stylesheet" href="admin.css"> <!-- Use admin.css for consistency -->
    <style>
        body { background: #f4f7f6; }
        .login-box { margin: auto; padding: 3rem; max-width: 400px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .logo { font-family: 'Playfair Display', serif; font-size: 2rem; color: #3d4a3a; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="login-box" style="text-align: center;">
        <div class="logo">Raricart Admin</div>
        <?php if ($error): ?>
            <div class="error" style="color:red; margin-bottom:1rem;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="E-mail (np. kontakt@raricart.pl)" required autofocus class="status-input" style="width:100%; box-sizing:border-box; margin-bottom:1rem;">
            <input type="password" name="password" placeholder="Hasło" required class="status-input" style="width:100%; box-sizing:border-box; margin-bottom:1.5rem;">
            <button type="submit" class="action-btn" style="width:100%;">Zaloguj się</button>
        </form>
        <p style="margin-top:1.5rem; font-size:0.9rem;">
            <a href="forgot_password.php" style="color:#666; text-decoration:none;">Zapomniałeś hasła?</a>
        </p>
    </div>
</body>
</html>
