<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: Skrypt dostępny wyłącznie z poziomu CLI.\n");
}

$configFile = dirname(__DIR__) . '/assets/data/config.json';
$dataDir = dirname($configFile);
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0750, true);
}

$email = strtolower(trim((string)($argv[1] ?? getenv('RARICART_ADMIN_EMAIL') ?: '')));
$newPassword = (string)($argv[2] ?? getenv('RARICART_ADMIN_PASSWORD') ?: '');

if ($newPassword === '' && function_exists('stream_isatty') && !@stream_isatty(STDIN)) {
    $newPassword = trim((string)fgets(STDIN));
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Użycie: php bin/set-admin-password.php <email> <haslo_min_12_znakow> [--force-change]\n");
    fwrite(STDERR, "Alternatywnie przekaż RARICART_ADMIN_EMAIL oraz RARICART_ADMIN_PASSWORD w środowisku lub hasło na STDIN.\n");
    exit(1);
}

if (strlen($newPassword) < 12) {
    fwrite(STDERR, "BŁĄD: Hasło administratora musi mieć co najmniej 12 znaków.\n");
    exit(1);
}

$forceChange = in_array('--force-change', $argv, true);

$fp = @fopen($configFile, 'c+b');
if (!$fp || !@flock($fp, LOCK_EX)) {
    if ($fp) {
        fclose($fp);
    }
    fwrite(STDERR, "BŁĄD: Nie można uzyskać blokady LOCK_EX na pliku config.json.\n");
    exit(1);
}

rewind($fp);
$raw = stream_get_contents($fp);
$config = (is_string($raw) && $raw !== '') ? json_decode($raw, true) : [];
if (!is_array($config)) {
    $config = ['users' => []];
}
if (!isset($config['users']) || !is_array($config['users'])) {
    $config['users'] = [];
}

$hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
$config['users'][$email] = $forceChange
    ? ['hash' => $hash, 'force_change' => true]
    : $hash;

$encoded = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
ftruncate($fp, 0);
rewind($fp);
$saved = fwrite($fp, (string)$encoded);
fflush($fp);
@flock($fp, LOCK_UN);
fclose($fp);
@chmod($configFile, 0600);

if ($saved !== false) {
    echo "========================================\n";
    echo "POMYŚLNIE ZAKTUALIZOWANO HASŁO ADMINA\n";
    echo "Konto: {$email}\n";
    echo "Wymuszenie zmiany przy logowaniu: " . ($forceChange ? 'TAK' : 'NIE') . "\n";
    echo "========================================\n";
    exit(0);
}

fwrite(STDERR, "BŁĄD: Nie udało się zapisać pliku config.json.\n");
exit(1);
