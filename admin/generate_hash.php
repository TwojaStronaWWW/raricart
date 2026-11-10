<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI only.\n");
}

$password = $argv[1] ?? getenv('RARICART_ADMIN_PASSWORD') ?: '';
if ($password === '' && !posix_isatty_safe()) {
    $password = trim((string)fgets(STDIN));
}

function posix_isatty_safe(): bool {
    return function_exists('stream_isatty') ? @stream_isatty(STDIN) : true;
}

if ($password === '' || strlen($password) < 8) {
    fwrite(STDERR, "Użycie: php admin/generate_hash.php <haslo_min_8_znakow>\n");
    exit(1);
}

echo password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]) . PHP_EOL;
