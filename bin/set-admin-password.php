<?php
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Skrypt dostępny tylko z poziomu CLI.\n");
}

$configFile = dirname(__DIR__) . '/assets/data/config.json';
if (!file_exists($configFile)) {
    die("BŁĄD: Nie znaleziono pliku $configFile\n");
}

$email = $argv[1] ?? 'pomoc@raricart.pl';
$newPassword = $argv[2] ?? 'Raricart2026!';

$config = json_decode((string)file_get_contents($configFile), true);
if (!is_array($config)) {
    $config = ['users' => []];
}
if (!isset($config['users']) || !is_array($config['users'])) {
    $config['users'] = [];
}

$hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
$config['users'][$email] = $hash;

$saved = file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

if ($saved !== false) {
    echo "========================================\n";
    echo "POMYŚLNIE ZAKTUALIZOWANO HASŁO ADMINA:\n";
    echo "E-mail:   $email\n";
    echo "Hasło:    $newPassword\n";
    echo "========================================\n";
} else {
    echo "BŁĄD: Nie udało się zapisać pliku config.json.\n";
    exit(1);
}
