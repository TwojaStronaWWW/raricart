<?php
session_start();

$config_file = __DIR__ . '/../assets/data/config.json';
$data_dir = __DIR__ . '/../assets/data';

// If config exists, forbid access to setup (Security)
if (file_exists($config_file)) {
    die("Setup został już wykonany. Jeśli chcesz zresetować hasło, usuń plik assets/data/config.json ręcznie z serwera.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (strlen($password) < 5) {
        $error = "Hasło musi mieć co najmniej 5 znaków.";
    } elseif ($password !== $confirm) {
        $error = "Hasła nie są identyczne.";
    } else {
        // Create Data Directory if not exists
        if (!file_exists($data_dir)) {
            mkdir($data_dir, 0755, true);
        }

        // Save Hash
        $config = [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'setup_date' => date('Y-m-d H:i:s')
        ];
        
        if (file_put_contents($config_file, json_encode($config))) {
            $success = "Hasło ustawione pomyślnie! Przekierowanie do logowania...";
            header("Refresh: 2; url=index.php");
        } else {
            $error = "Błąd zapisu pliku konfiguracyjnego. Sprawdź uprawnienia folderu assets/data.";
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
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #e0e0e0; margin: 0; }
        .setup-box { background: white; padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        h2 { color: #2d3a2a; margin-bottom: 0.5rem; }
        p { color: #666; margin-bottom: 2rem; font-size: 0.9rem; }
        input { width: 100%; padding: 12px; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { background: #2d3a2a; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-size: 1rem; width: 100%; }
        button:hover { opacity: 0.9; }
        .error { color: #d9534f; margin-bottom: 1rem; background: #f9d6d5; padding: 10px; border-radius: 4px; }
        .success { color: #5cb85c; margin-bottom: 1rem; background: #dff0d8; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="setup-box">
        <h2>Witaj w Raricart Admin</h2>
        <p>To jest pierwsze uruchomienie. Ustaw hasło administratora.</p>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php else: ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Nowe Hasło" required>
            <input type="password" name="confirm" placeholder="Potwierdź Hasło" required>
            <button type="submit">Ustaw Hasło</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
