<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI only.\n");
}

$configFile = __DIR__ . '/../assets/data/config.json';
if (!file_exists($configFile)) {
    fwrite(STDERR, "BŁĄD: Plik config.json nie istnieje. Użyj php bin/set-admin-password.php.\n");
    exit(1);
}

$config = json_decode((string)file_get_contents($configFile), true);
if (!is_array($config)) {
    fwrite(STDERR, "BŁĄD: Uszkodzony plik config.json.\n");
    exit(1);
}

if (isset($config['users']) && is_array($config['users'])) {
    echo "Konfiguracja korzysta już z formatu wieloużytkownikowego (users).\n";
    exit(0);
}

$primaryEmail = strtolower(trim((string)($argv[1] ?? getenv('RARICART_ADMIN_EMAIL') ?: 'kontakt@raricart.pl')));
$existingHash = (string)($config['password_hash'] ?? '');

if ($existingHash === '' || strpos($existingHash, '$2') !== 0) {
    $cliPass = (string)($argv[2] ?? getenv('RARICART_ADMIN_PASSWORD') ?: '');
    if (strlen($cliPass) < 12) {
        fwrite(STDERR, "BŁĄD: Brak istniejącego hasha w config.json. Podaj silne hasło (min. 12 znaków) jako 2. argument CLI lub w zmiennej RARICART_ADMIN_PASSWORD.\n");
        exit(1);
    }
    $existingHash = password_hash($cliPass, PASSWORD_BCRYPT, ['cost' => 12]);
}

$newConfig = [
    'users' => [
        $primaryEmail => $existingHash,
    ],
];

@copy($configFile, $configFile . '.bak');
if (file_put_contents($configFile, json_encode($newConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX) === false) {
    fwrite(STDERR, "BŁĄD: Nie udało się zapisać config.json.\n");
    exit(1);
}
@chmod($configFile, 0600);

echo "Migracja zakończona pomyślnie dla konta: {$primaryEmail}\n";
