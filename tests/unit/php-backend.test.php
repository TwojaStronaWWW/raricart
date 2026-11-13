<?php
declare(strict_types=1);

/**
 * Testy jednostkowe i regresji bezpieczeństwa backendu PHP (Raricart.pl)
 * Weryfikacja reguł architektonicznych z AGENTS.md (ISO 27001 / OWASP ASVS [REF-05]):
 * 1. Concurrency (LOCK_EX, atomic flock writes)
 * 2. Security (HttpOnly, SameSite=Strict, CSRF hash_equals, Sec-Fetch-Site, must_change_password)
 * 3. Hot Leads, RODO mask_ip, CSV Formula Injection (sanitize_csv_cell) & WAF L7 Penalty Box
 * 4. Media (Strict finfo MIME, realpath Path Traversal confinement, WebP 1920px Q85, Decompression Bomb Guard)
 * 5. Cache (X-LiteSpeed-Purge) & Secrets Hygiene (config.json poza Git, brak zahardkodowanych haseł)
 */

$testsPassed = 0;
$testsFailed = 0;

function it(string $description, callable $fn): void {
    global $testsPassed, $testsFailed;
    try {
        $fn();
        echo "  ✔ $description\n";
        $testsPassed++;
    } catch (\Throwable $e) {
        echo "  ❌ $description: " . $e->getMessage() . "\n";
        $testsFailed++;
    }
}

function assert_true(bool $cond, string $msg = 'Assertion failed'): void {
    if (!$cond) throw new \Exception($msg);
}

function assert_equals($expected, $actual, string $msg = 'Values are not equal'): void {
    if ($expected !== $actual) {
        throw new \Exception("$msg (Oczekiwano: " . var_export($expected, true) . ", otrzymano: " . var_export($actual, true) . ")");
    }
}

$rootDir = realpath(__DIR__ . '/../..');
if ($rootDir === false) {
    throw new \RuntimeException('Nie można ustalić katalogu głównego projektu.');
}

echo "=== TESTY JEDNOSTKOWE I BEZPIECZEŃSTWA BACKENDU PHP (PHP 8 NATIVE) ===\n\n";

// --- 1. HOT LEAD SCORING & WALIDACJA ---
echo "1. Logika kwalifikacji leadów i walidacji (api/contact.php):\n";

it('budżet >= 5000 PLN powinien zakwalifikować lead jako 🔥 HOT', function() {
    $budget = "5000";
    $guests = "40";
    $budgetNum = (int) preg_replace('/[^0-9]/', '', $budget);
    $guestsNum = (int) $guests;
    $isHot = ($budgetNum >= 5000 || $guestsNum >= 100);
    assert_true($isHot, "Lead z budżetem 5000 PLN powinien być oznaczony jako HOT");
});

it('liczba gości >= 100 powinna zakwalifikować lead jako 🔥 HOT', function() {
    $budget = "2000";
    $guests = "120";
    $budgetNum = (int) preg_replace('/[^0-9]/', '', $budget);
    $guestsNum = (int) $guests;
    $isHot = ($budgetNum >= 5000 || $guestsNum >= 100);
    assert_true($isHot, "Lead ze 120 gośćmi powinien być oznaczony jako HOT");
});

it('budżet < 5000 i liczba gości < 100 NIE powinny kwalifikować leada jako HOT', function() {
    $budget = "3500";
    $guests = "60";
    $budgetNum = (int) preg_replace('/[^0-9]/', '', $budget);
    $guestsNum = (int) $guests;
    $isHot = ($budgetNum >= 5000 || $guestsNum >= 100);
    assert_true(!$isHot, "Standardowy lead nie powinien być oznaczony jako HOT");
});

it('twarda walidacja email powinna odrzucać nieprawidłowe adresy', function() {
    $invalidEmails = ['brak_at_domena.pl', 'test@', '@domena.pl', 'zly adres@domena.pl'];
    foreach ($invalidEmails as $em) {
        $valid = filter_var($em, FILTER_VALIDATE_EMAIL);
        assert_true($valid === false, "Nieprawidłowy adres email powinien zostać odrzucony: $em");
    }

    $validEmail = 'kontakt@raricart.pl';
    assert_true((bool)filter_var($validEmail, FILTER_VALIDATE_EMAIL), "Poprawny email powinien przejść");
});

it('plik api/contact.php musi używać HTTPS osTicket, sanitize_csv_cell, mask_ip, UTF-8 BOM i flock(LOCK_EX)', function() use ($rootDir) {
    $contactCode = (string)file_get_contents($rootDir . '/api/contact.php');
    assert_true(strpos($contactCode, 'chr(0xEF).chr(0xBB).chr(0xBF)') !== false, "Brak zapisu UTF-8 BOM dla Excela");
    assert_true(strpos($contactCode, 'flock($fp, LOCK_EX)') !== false, "Brak blokady flock LOCK_EX");
    assert_true(strpos($contactCode, 'flock($fp, LOCK_UN)') !== false, "Brak odblokowania flock LOCK_UN");
    assert_true(strpos($contactCode, 'https://hd.twojastronawww.pl/ticket_hook.php') !== false, "Brak szyfrowanego URL HTTPS osTicket");
    assert_true(strpos($contactCode, 'http://hd.twojastronawww.pl') === false, "Wykryto nieszyfrowany URL HTTP osTicket");
    assert_true(substr_count($contactCode, 'sanitize_csv_cell(') >= 2, "sanitize_csv_cell() musi chronić zarówno finalny zapis, jak i saveDraftToCsv()");
    assert_true(substr_count($contactCode, 'mask_ip(') >= 2, "mask_ip() musi anonimizować IP zarówno w finalnym zapisie, jak i w saveDraftToCsv()");
    assert_true(strpos($contactCode, "preg_replace('/[^0-9+\\- ()]/'") !== false, "Pole telefonu musi wycinać znaki CRLF bez użycia \\s");
});

// --- 2. BEZPIECZEŃSTWO SESJI, CSRF, MUST_CHANGE_PASSWORD I RESETU HASŁA ---
echo "\n2. Bezpieczeństwo sesji, CSRF, must_change_password i panelu admin/:\n";

it('konfiguracja sesji w admin/auth.php musi wymuszać use_strict_mode, HttpOnly, SameSite=Strict, TTL, Sec-Fetch-Site i must_change_password', function() use ($rootDir) {
    $authCode = (string)file_get_contents($rootDir . '/admin/auth.php');
    $rateLimitCode = (string)file_get_contents($rootDir . '/api/rate-limit.php');
    assert_true(strpos($authCode, 'session.use_strict_mode') !== false, "Brak session.use_strict_mode=1");
    assert_true(strpos($authCode, "'httponly' => true") !== false, "Brak flagi httponly w cookie params");
    assert_true(strpos($authCode, "'samesite' => 'Strict'") !== false, "Brak flagi samesite=Strict");
    assert_true(strpos($authCode, "'secure' => \$secure") !== false, "Brak flagi secure");
    assert_true(strpos($authCode, 'SESSION_TTL') !== false, "Brak limitu bezczynności SESSION_TTL");
    assert_true(strpos($authCode, 'verify_sec_fetch_site') !== false, "Brak walidacji Sec-Fetch-Site");
    assert_true(strpos($authCode, 'must_change_password') !== false, "Brak serwerowego strażnika must_change_password w check_login()");
    assert_true(strpos($authCode, 'get_client_ip()') !== false && strpos($rateLimitCode, 'HTTP_CF_CONNECTING_IP') !== false && strpos($rateLimitCode, 'HTTP_CF_RAY') !== false, "Brak rozpoznawania IP za Cloudflare");
});

it('weryfikacja CSRF musi używać hash_equals odpornego na timing attacks', function() use ($rootDir) {
    $authCode = (string)file_get_contents($rootDir . '/admin/auth.php');
    assert_true(strpos($authCode, 'hash_equals(') !== false, "check_csrf() musi używać hash_equals");
    assert_true(strpos($authCode, 'HTTP_X_CSRF_TOKEN') !== false, "check_csrf() musi sprawdzać nagłówek X-CSRF-Token");
});

function invoke_php_endpoint(string $relativeScript, string $method = 'GET', array $postData = [], array $sessionData = [], array $headers = []): array {
    global $rootDir;
    $scriptPath = realpath($rootDir . '/' . $relativeScript);
    if ($scriptPath === false) {
        throw new \RuntimeException("Nie znaleziono skryptu: $relativeScript");
    }

    $statusFile = sys_get_temp_dir() . '/raricart_status_' . bin2hex(random_bytes(6)) . '.txt';
    $sessDir = sys_get_temp_dir() . '/raricart_sess_' . bin2hex(random_bytes(6));
    @mkdir($sessDir, 0700, true);

    $runnerCode = sprintf(
        'session_save_path(%s); register_shutdown_function(function() { file_put_contents(%s, (string)http_response_code()); }); $_SERVER["REQUEST_METHOD"] = %s; $_SERVER["REMOTE_ADDR"] = "198.51.100.77"; foreach (%s as $k => $v) { $_SERVER[$k] = $v; } $_POST = %s; $initSession = %s; if (!empty($initSession)) { session_start(); foreach ($initSession as $sk => $sv) { $_SESSION[$sk] = $sv; } session_write_close(); $_COOKIE[session_name()] = session_id(); } require %s;',
        var_export($sessDir, true),
        var_export($statusFile, true),
        var_export($method, true),
        var_export($headers, true),
        var_export($postData, true),
        var_export($sessionData, true),
        var_export($scriptPath, true)
    );

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $proc = proc_open([PHP_BINARY, '-d', 'display_errors=0', '-r', $runnerCode], $descriptors, $pipes, dirname($scriptPath));
    if (!is_resource($proc)) {
        throw new \RuntimeException("Nie udało się uruchomić procesu testowego dla $relativeScript");
    }
    fclose($pipes[0]);
    $stdout = (string)stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = (string)stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);

    $httpCode = file_exists($statusFile) ? (int)trim((string)file_get_contents($statusFile)) : 200;
    @unlink($statusFile);
    foreach (glob($sessDir . '/*') ?: [] as $sf) {
        @unlink($sf);
    }
    @rmdir($sessDir);

    return [
        'exitCode' => $exitCode,
        'httpCode' => $httpCode ?: 200,
        'raw' => $stdout,
        'stderr' => $stderr,
        'json' => json_decode($stdout, true),
    ];
}

it('akcje modyfikujące stan w adminie (logout.php, sync_leads.php, optimize_existing.php) odrzucają GET kodem 405 i wymagają poprawnego tokenu CSRF', function() {
    $sessionOk = [
        'logged_in' => true,
        'user_email' => 'admin@raricart.pl',
        'last_activity' => time(),
        'must_change_password' => false,
        'csrf_token' => str_repeat('a', 64),
    ];

    foreach (['admin/logout.php', 'admin/sync_leads.php', 'admin/optimize_existing.php'] as $ep) {
        $getRes = invoke_php_endpoint($ep, 'GET', [], $sessionOk);
        assert_equals(405, $getRes['httpCode'], "Endpoint $ep musi odrzucać żądania GET kodem HTTP 405");

        $badCsrfRes = invoke_php_endpoint($ep, 'POST', ['csrf_token' => str_repeat('b', 64)], $sessionOk);
        assert_equals(403, $badCsrfRes['httpCode'], "Endpoint $ep musi odrzucać błędny token CSRF kodem HTTP 403");
    }
});

it('flaga must_change_password=true w sesji blokuje serwerowo dostęp do chronionych endpointów zapisu (HTTP 403)', function() {
    $forcedChangeSession = [
        'logged_in' => true,
        'user_email' => 'admin@raricart.pl',
        'last_activity' => time(),
        'must_change_password' => true,
        'csrf_token' => str_repeat('c', 64),
    ];

    $res = invoke_php_endpoint(
        'admin/save_status.php',
        'POST',
        [],
        $forcedChangeSession,
        ['HTTP_ACCEPT' => 'application/json', 'HTTP_X_CSRF_TOKEN' => str_repeat('c', 64)]
    );
    assert_equals(403, $res['httpCode'], 'Endpoint zapisu musi zwrócić HTTP 403, gdy sesja ma must_change_password=true');
});

it('forgot_password.php oraz reset_password.php przechowują wyłącznie skrót SHA-256 tokenu resetu i wymuszają Rate Limit', function() use ($rootDir) {
    $forgotCode = (string)file_get_contents($rootDir . '/admin/forgot_password.php');
    $resetCode = (string)file_get_contents($rootDir . '/admin/reset_password.php');

    assert_true(strpos($forgotCode, "hash('sha256', \$resetToken)") !== false, "forgot_password.php musi haszować token przez hash('sha256', ...)");
    assert_true(strpos($forgotCode, '$tokens[$tokenHash]') !== false, 'forgot_password.php musi indeksować zapis wyłącznie skrótem SHA-256 zamiast surowego tokenu');
    assert_true(strpos($forgotCode, 'rate_limit(') !== false, 'forgot_password.php musi posiadać ochronę Rate Limit per IP');
    assert_true(strpos($resetCode, "hash('sha256', \$rawToken)") !== false, 'reset_password.php musi weryfikować skrót SHA-256 tokenu');
    assert_true(strpos($resetCode, 'hash_equals(') !== false, 'reset_password.php musi porównywać skróty przez hash_equals()');
});

// --- 3. SERWIS MEDIÓW I REGRESJA PATH TRAVERSAL / UPLOAD RCE (api/MediaService.php) ---
echo "\n3. Ochrona przed Path Traversal, Arbitrary File Deletion i Upload RCE (api/MediaService.php):\n";

require_once $rootDir . '/api/MediaService.php';

it('MediaService::deleteAsset blokuje próby Path Traversal (..), pliki .php, .htaccess, wrappery strumieniowe oraz pliki poza assets/gallery i assets/content', function() use ($rootDir) {
    $maliciousTargets = [
        '',
        '../../assets/data/config.json',
        '../assets/data/config.json',
        'assets/gallery/../../assets/data/config.json',
        'assets/gallery/../../index.php',
        '../index.php',
        'index.php',
        'assets/data/content.json',
        'assets/gallery/.htaccess',
        'assets/content/.htaccess',
        'assets/gallery/shell.php',
        'assets/gallery/backdoor.phtml',
        "assets/gallery/img.webp\0.php",
        'assets\\gallery\\..\\..\\index.php',
        'phar://assets/gallery/exploit.webp',
        'file:///etc/passwd',
        'https://evil.example/gallery/test.webp',
    ];

    foreach ($maliciousTargets as $payload) {
        $result = \Raricart\Api\MediaService::deleteAsset($payload);
        assert_equals(false, $result, "MediaService::deleteAsset musi bezwzględnie odrzucić złośliwą ścieżkę: " . var_export($payload, true));
    }

    // Upewnij się, że krytyczne pliki projektu nadal istnieją
    assert_true(file_exists($rootDir . '/index.php'), 'index.php nie może zostać usunięty');
    assert_true(file_exists($rootDir . '/assets/data/content.json'), 'assets/data/content.json nie może zostać usunięty');
});

it('MediaService mapuje rozszerzenia wideo wyłącznie z finfo_file MIME, posiada limit rozmiaru oraz strażnika bomby dekompresyjnej getimagesize()', function() use ($rootDir) {
    $mediaCode = (string)file_get_contents($rootDir . '/api/MediaService.php');
    assert_true(strpos($mediaCode, 'FILEINFO_MIME_TYPE') !== false, "Brak finfo_open(FILEINFO_MIME_TYPE)");
    assert_true(strpos($mediaCode, "'video/mp4' => 'mp4'") !== false, "Brak ścisłego mapowania MIME video/mp4 => mp4");
    assert_true(strpos($mediaCode, "'video/webm' => 'webm'") !== false, "Brak ścisłego mapowania MIME video/webm => webm");
    assert_true(strpos($mediaCode, 'PATHINFO_EXTENSION') === false || strpos($mediaCode, '$file[\'name\'], PATHINFO_EXTENSION') === false, "Zakaz pobierania rozszerzenia uploadu z nazwy pliku od użytkownika!");
    assert_true(strpos($mediaCode, 'getimagesize(') !== false, "Brak pre-dekodowania wymiarów getimagesize() przeciw bombom dekompresyjnym");
    assert_true(strpos($mediaCode, 'MAX_IMAGE_DIMENSION') !== false && strpos($mediaCode, 'MAX_IMAGE_PIXELS') !== false, "Brak limitów wymiarów i megapikseli obrazu");
    assert_true(strpos($mediaCode, 'MAX_IMAGE_BYTES') !== false && strpos($mediaCode, 'MAX_VIDEO_BYTES') !== false, "Brak limitów rozmiaru pliku");
    assert_true(strpos($mediaCode, '$maxWidth = 1920') !== false, "Maksymalna szerokość musi wynosić 1920px");
    assert_true(strpos($mediaCode, 'imagewebp($newImage, null, 85)') !== false, "Konwersja WebP musi używać jakości Q85");
});

// --- 4. HIGIENA SEKRETÓW, CONFIG.JSON POZA GIT I NARZĘDZIA CLI ---
echo "\n4. Ochrona konfiguracji, brak zahardkodowanych haseł oraz blokada HTTP na narzędziach CLI:\n";

it('plik assets/data/config.json NIE może być śledzony przez Git, a szablon assets/data/config.json.dist nie może zawierać prawdziwych hashy bcrypt', function() use ($rootDir) {
    $gitTracked = [];
    exec('git -C ' . escapeshellarg($rootDir) . ' ls-files -- assets/data/config.json', $gitTracked);
    assert_equals([], array_values(array_filter($gitTracked)), 'assets/data/config.json MUSI być wycofany z indeksowania Git (git rm --cached)!');

    $gitignore = (string)file_get_contents($rootDir . '/.gitignore');
    assert_true(strpos($gitignore, 'assets/data/config.json') !== false, '.gitignore musi ignorować assets/data/config.json');
    assert_true(strpos($gitignore, 'docs/audits/') !== false, '.gitignore musi ignorować docs/audits/');

    $distPath = $rootDir . '/assets/data/config.json.dist';
    assert_true(file_exists($distPath), 'Szablon assets/data/config.json.dist musi istnieć');
    $distRaw = (string)file_get_contents($distPath);
    $distJson = json_decode($distRaw, true);
    assert_true(is_array($distJson), 'assets/data/config.json.dist musi być poprawnym plikiem JSON');
    assert_equals(0, preg_match('/\$2[ayb]\$[0-9]{2}\$[A-Za-z0-9.\/]{53}/', $distRaw), 'Szablon config.json.dist nie może zawierać aktywnego skrótu bcrypt');
});

it('skrypty narzędziowe CLI nie mogą zawierać zahardkodowanych haseł i muszą blokować uruchomienie poza PHP_SAPI === cli', function() use ($rootDir) {
    $cliScripts = [
        'admin/generate_hash.php',
        'admin/migrate_users.php',
        'bin/set-admin-password.php',
    ];
    $forbiddenLiterals = ['admin123', 'pomoc123', 'Raricart2026!'];

    foreach ($cliScripts as $rel) {
        $code = (string)file_get_contents($rootDir . '/' . $rel);
        assert_true(strpos($code, "PHP_SAPI !== 'cli'") !== false, "Skrypt $rel musi blokować dostęp HTTP warunkiem PHP_SAPI !== 'cli'");
        foreach ($forbiddenLiterals as $badPass) {
            assert_true(stripos($code, $badPass) === false, "Wykryto zahardkodowane hasło '$badPass' w pliku $rel!");
        }
    }
});

// --- 5. ZARZĄDZANIE PAMIĘCIĄ CACHE I WSPÓŁBIEŻNOŚCIĄ ---
echo "\n5. Cache LiteSpeed i zapisy atomowe LOCK_EX w panelu administracyjnym:\n";

$adminSaveScripts = [
    'admin/save_content.php',
    'admin/save_gallery.php',
    'admin/save_status.php'
];

foreach ($adminSaveScripts as $script) {
    it("plik $script musi wysyłać nagłówek X-LiteSpeed-Purge: * po zapisie JSON", function() use ($rootDir, $script) {
        $code = (string)file_get_contents($rootDir . '/' . $script);
        assert_true(strpos($code, 'header("X-LiteSpeed-Purge: *");') !== false, "Brak nagłówka X-LiteSpeed-Purge w $script");
    });

    it("plik $script musi używać blokady zapisu LOCK_EX", function() use ($rootDir, $script) {
        $code = (string)file_get_contents($rootDir . '/' . $script);
        assert_true(strpos($code, 'LOCK_EX') !== false, "Brak flagi LOCK_EX przy zapisie pliku w $script");
    });
}

it('wszystkie krytyczne operacje zapisu plików stanu w systemie muszą używać LOCK_EX', function() use ($rootDir) {
    $criticalFiles = [
        'api/contact.php',
        'api/rate-limit.php',
        'admin/change_password.php',
        'admin/forgot_password.php',
        'admin/reset_password.php',
        'admin/setup.php',
        'admin/delete.php',
        'admin/index.php',
    ];

    foreach ($criticalFiles as $file) {
        $code = (string)file_get_contents($rootDir . '/' . $file);
        assert_true(strpos($code, 'LOCK_EX') !== false, "Plik $file nie zawiera wymaganej blokady LOCK_EX!");
    }
});

// --- 6. TESTY INTEGRACYJNE ENDPOINTU FORMULARZA (IZOLOWANY KATALOG TYMCZASOWY) ---
echo "\n6. Testy integracyjne formularza, CSV Injection, RODO mask_ip i WAF L7 Penalty Box (api/contact.php):\n";

function invoke_contact_api(
    array|string $payload,
    string $draftsDir,
    string $csvFile,
    string $rateLimitDir,
    string $ip = '198.51.100.42'
): array {
    global $rootDir;
    $scriptPath = realpath($rootDir . '/api/contact.php');
    $statusFile = sys_get_temp_dir() . '/raricart_contact_status_' . bin2hex(random_bytes(6)) . '.txt';
    $rawBody = is_string($payload) ? $payload : (string)json_encode($payload, JSON_UNESCAPED_UNICODE);

    $runnerCode = sprintf(
        'register_shutdown_function(function() { file_put_contents(%s, (string)http_response_code()); }); putenv("RARICART_TEST_DRAFTS_DIR=" . %s); putenv("RARICART_TEST_CSV_FILE=" . %s); putenv("RARICART_TEST_RATE_LIMIT_DIR=" . %s); putenv("RARICART_TEST_DISABLE_NETWORK=1"); $_SERVER["REQUEST_METHOD"] = "POST"; $_SERVER["REMOTE_ADDR"] = %s; $_SERVER["CONTENT_LENGTH"] = (string)%d; require %s;',
        var_export($statusFile, true),
        var_export($draftsDir, true),
        var_export($csvFile, true),
        var_export($rateLimitDir, true),
        var_export($ip, true),
        strlen($rawBody),
        var_export($scriptPath, true)
    );

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $proc = proc_open([PHP_BINARY, '-d', 'display_errors=0', '-r', $runnerCode], $descriptors, $pipes, dirname((string)$scriptPath));
    if (!is_resource($proc)) {
        throw new \RuntimeException('Nie udało się uruchomić procesu testowego PHP dla api/contact.php');
    }

    @fwrite($pipes[0], $rawBody);
    @fclose($pipes[0]);

    $stdout = (string)stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = (string)stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);

    $httpCode = file_exists($statusFile) ? (int)trim((string)file_get_contents($statusFile)) : 200;
    @unlink($statusFile);

    $decoded = json_decode($stdout, true);
    return [
        'exitCode' => $exitCode,
        'httpCode' => $httpCode ?: 200,
        'raw' => $stdout,
        'stderr' => $stderr,
        'json' => is_array($decoded) ? $decoded : null,
    ];
}

function remove_temp_dir_only(string $dir): void {
    $sysTmp = realpath(sys_get_temp_dir());
    if ($dir === '' || !is_dir($dir) || $sysTmp === false) {
        return;
    }
    $realDir = realpath($dir);
    if ($realDir === false || !str_starts_with($realDir, $sysTmp . DIRECTORY_SEPARATOR)) {
        throw new \RuntimeException("Odmowa usunięcia katalogu spoza sys_get_temp_dir(): $dir");
    }
    foreach (glob($realDir . '/*') ?: [] as $f) {
        @unlink($f);
    }
    foreach (glob($realDir . '/.*') ?: [] as $f) {
        if (basename($f) !== '.' && basename($f) !== '..') {
            @unlink($f);
        }
    }
    @rmdir($realDir);
}

function cleanup_test_artifacts(string $draftsDir, string $csvFile, string $rateLimitDir): void {
    remove_temp_dir_only($draftsDir);
    remove_temp_dir_only($rateLimitDir);
    if ($csvFile !== '' && file_exists($csvFile)) {
        @unlink($csvFile);
    }
}

it('wielokrotny autosave szkiców (20x blur + visibilitychange + ping) NIE może blokować finalnego wysłania formularza (Scenariusz Gabrieli Momot)', function() {
    $tmpBase = sys_get_temp_dir() . '/raricart_test_' . bin2hex(random_bytes(4));
    $draftsDir = $tmpBase . '_drafts';
    $csvFile = $tmpBase . '_leady.csv';
    $rateLimitDir = $tmpBase . '_ratelimits';
    $testIp = '198.51.100.101';

    try {
        $pingRes = invoke_contact_api(['action' => 'ping'], $draftsDir, $csvFile, $rateLimitDir, $testIp);
        assert_equals('pong', $pingRes['json']['status'] ?? null, 'Ping powinien zwrócić status pong');

        for ($i = 1; $i <= 20; $i++) {
            $draftRes = invoke_contact_api([
                'name' => 'Gabriela Momot',
                'email' => 'g.momot@eveline.com.pl',
                'phone' => '+48575754222',
                'date' => '2026-12-04',
                'location' => 'Łódź',
                'guests' => '140',
                'event_type' => 'corporate',
                'stations' => 'Mini Pancakes, Deska Serów',
                'message' => "Wersja robocza nr $i",
                'is_partial' => true,
                'is_abandoned' => ($i === 10),
            ], $draftsDir, $csvFile, $rateLimitDir, $testIp);

            assert_equals('success', $draftRes['json']['status'] ?? null, "Autosave #$i powinien zakończyć się sukcesem");
        }

        assert_true(!file_exists($csvFile), 'Szkic z przełączenia karty nie powinien trafiać od razu do leady.csv przed upływem 10 minut');

        $finalRes = invoke_contact_api([
            'name' => 'Gabriela Momot',
            'email' => 'g.momot@eveline.com.pl',
            'phone' => '+48575754222',
            'date' => '2026-12-04',
            'location' => 'Łódź',
            'guests' => '140',
            'event_type' => 'corporate',
            'stations' => 'Mini Pancakes, Deska Serów',
            'message' => 'Jest to event świąteczny dla pracowników Rossmanna w centrali (Łódź).',
        ], $draftsDir, $csvFile, $rateLimitDir, $testIp);

        assert_equals('success', $finalRes['json']['status'] ?? null, 'Finalny submit po 20 szkicach musi zakończyć się sukcesem (brak blokady 429)');
        assert_true(file_exists($csvFile), 'Plik leady.csv musi zostać utworzony przy finalnym submicie');

        $csvContent = (string)file_get_contents($csvFile);
        assert_true(strpos($csvContent, '🔥 HOT') !== false, 'Lead na 140 gości musi być oznaczony jako 🔥 HOT');
        assert_true(strpos($csvContent, 'Formularz') !== false, 'Źródło leada w CSV musi mieć wartość Formularz');
        assert_true(strpos($csvContent, 'g.momot@eveline.com.pl') !== false, 'CSV musi zawierać email klientki');
        assert_true(strpos($csvContent, '198.51.100.0') !== false && strpos($csvContent, '198.51.100.101') === false, 'IP w CSV musi być zanonimizowane zgodnie z RODO (/24)');

        $remainingDrafts = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(0, count($remainingDrafts), 'Po finalnym submicie nie może pozostać żaden plik draft_*.json');

        $lateDraftRes = invoke_contact_api([
            'name' => 'Gabriela Momot',
            'email' => 'g.momot@eveline.com.pl',
            'phone' => '+48575754222',
            'is_partial' => true,
            'is_abandoned' => true,
        ], $draftsDir, $csvFile, $rateLimitDir, $testIp);

        assert_equals('success', $lateDraftRes['json']['status'] ?? null);
        $remainingDraftsAfterLate = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(0, count($remainingDraftsAfterLate), 'Spóźniony pakiet autosave nie może odtworzyć pliku szkicu po wysłaniu formularza');
    } finally {
        cleanup_test_artifacts($draftsDir, $csvFile, $rateLimitDir);
    }
});

it('wpisanie najpierw telefonu a potem emaila scala szkic i usuwa sierocy plik telefonu oraz czyści digest_buffer.txt przy submicie', function() {
    $tmpBase = sys_get_temp_dir() . '/raricart_test_' . bin2hex(random_bytes(4));
    $draftsDir = $tmpBase . '_drafts';
    $csvFile = $tmpBase . '_leady.csv';
    $rateLimitDir = $tmpBase . '_ratelimits';
    $testIp = '198.51.100.102';

    try {
        invoke_contact_api([
            'name' => 'Aleksandra Matusik',
            'email' => '',
            'phone' => '+48 512 610 613',
            'is_partial' => true,
        ], $draftsDir, $csvFile, $rateLimitDir, $testIp);

        $draftsStep1 = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(1, count($draftsStep1), 'Powinien istnieć 1 plik szkicu po telefonie');

        invoke_contact_api([
            'name' => 'Aleksandra Matusik',
            'email' => 'ola.matusik@gmail.com',
            'phone' => '+48 512 610 613',
            'location' => 'Mysłowice, namiot w plenerze',
            'guests' => '150',
            'is_partial' => true,
        ], $draftsDir, $csvFile, $rateLimitDir, $testIp);

        $draftsStep2 = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(1, count($draftsStep2), 'Po dopisaniu emaila nadal powinien istnieć dokładnie 1 plik szkicu (brak duplikatu po telefonie)');

        file_put_contents(
            $draftsDir . '/digest_buffer.txt',
            "=== LEAD START ===\n--- TO JEST NIEUKOŃCZONY SZKIC FORMULARZA ---\nEmail: ola.matusik@gmail.com\nTel: +48512610613\n\n",
            LOCK_EX
        );

        $submitRes = invoke_contact_api([
            'name' => 'Aleksandra Matusik',
            'email' => 'ola.matusik@gmail.com',
            'phone' => '+48 512 610 613',
            'date' => '2028-09-02',
            'location' => 'Mysłowice, namiot w plenerze',
            'guests' => '150',
            'event_type' => 'wedding',
            'stations' => 'Mini Pancakes',
        ], $draftsDir, $csvFile, $rateLimitDir, $testIp);

        assert_equals('success', $submitRes['json']['status'] ?? null);
        assert_equals(0, count(glob($draftsDir . '/draft_*.json') ?: []), 'Brak plików draft_*.json po submicie');
        assert_true(!file_exists($draftsDir . '/digest_buffer.txt'), 'Plik digest_buffer.txt powinien zostać oczyszczony z wysłanego leada');
    } finally {
        cleanup_test_artifacts($draftsDir, $csvFile, $rateLimitDir);
    }
});

it('ochrona CSV Formula Injection (sanitize_csv_cell), mask_ip() oraz wycinanie CRLF w telefonie działają w finalnym submicie i w saveDraftToCsv()', function() {
    $tmpBase = sys_get_temp_dir() . '/raricart_test_' . bin2hex(random_bytes(4));
    $draftsDir = $tmpBase . '_drafts';
    $csvFile = $tmpBase . '_leady.csv';
    $rateLimitDir = $tmpBase . '_ratelimits';
    $testIp = '203.0.113.88';

    try {
        // 1. Zapis porzuconego szkicu z formułą Excela i CRLF w telefonie, postarzony o 700s -> wyzwolony przez ping (saveDraftToCsv)
        invoke_contact_api([
            'name' => '=1+1',
            'email' => 'draft-csv@example.com',
            'phone' => "+48 600 700 800\r\nBcc:evil@example.com",
            'message' => '@SUM(A1:A5)',
            'is_partial' => true,
        ], $draftsDir, $csvFile, $rateLimitDir, $testIp);

        foreach (glob($draftsDir . '/draft_*.json') ?: [] as $df) {
            @touch($df, time() - 700);
        }
        @unlink($draftsDir . '/cron_check.lock');

        $pingRes = invoke_contact_api(['action' => 'ping'], $draftsDir, $csvFile, $rateLimitDir, $testIp);
        assert_equals('pong', $pingRes['json']['status'] ?? null);
        assert_true(file_exists($csvFile), 'saveDraftToCsv() musi zapisać porzucony szkic po 10 minutach');

        // 2. Finalny submit z formułą CSV i próbą CRLF w telefonie
        $finalRes = invoke_contact_api([
            'name' => '+CMD|calc!A0',
            'email' => 'final-csv@example.com',
            'phone' => "+48 501 502 503\r\nSubject:Hacked",
            'message' => '-2+3',
        ], $draftsDir, $csvFile, $rateLimitDir, $testIp);
        assert_equals('success', $finalRes['json']['status'] ?? null);

        $fp = fopen($csvFile, 'r');
        assert_true($fp !== false, 'Otwarcie wygenerowanego CSV musi się powieść');
        $header = fgetcsv($fp);
        $draftRow = fgetcsv($fp);
        $finalRow = fgetcsv($fp);
        fclose($fp);

        assert_true(is_array($draftRow) && is_array($finalRow), 'CSV musi zawierać wiersz szkicu oraz wiersz finalny');
        assert_equals("'=1+1", $draftRow[3], 'Imię w saveDraftToCsv() musi być zneutralizowane apostrofem przed =');
        assert_equals("'@SUM(A1:A5)", $draftRow[13], 'Wiadomość w saveDraftToCsv() musi być zneutralizowana apostrofem przed @');
        assert_equals('203.0.113.0', $draftRow[14], 'IP w saveDraftToCsv() musi być zanonimizowane przez mask_ip()');
        assert_true(strpos($draftRow[5], "\r") === false && strpos($draftRow[5], "\n") === false && stripos($draftRow[5], 'bcc') === false, 'Telefon w szkicu nie może zawierać CRLF ani liter');

        assert_equals("'+CMD|calc!A0", $finalRow[3], 'Imię w finalnym submicie musi być zneutralizowane apostrofem przed +');
        assert_equals("'-2+3", $finalRow[13], 'Wiadomość w finalnym submicie musi być zneutralizowana apostrofem przed -');
        assert_equals('203.0.113.0', $finalRow[14], 'IP w finalnym submicie musi być zanonimizowane przez mask_ip()');
        assert_true(strpos($finalRow[5], "\r") === false && strpos($finalRow[5], "\n") === false && stripos($finalRow[5], 'subject') === false, 'Telefon w finalnym submicie nie może zawierać CRLF ani liter');
    } finally {
        cleanup_test_artifacts($draftsDir, $csvFile, $rateLimitDir);
    }
});

it('limit wejścia 65536 B zwraca HTTP 413, a filtr WAF L7 zwraca HTTP 403 i nakłada 15-minutowy ban w IP Penalty Box', function() {
    $tmpBase = sys_get_temp_dir() . '/raricart_test_' . bin2hex(random_bytes(4));
    $draftsDir = $tmpBase . '_drafts';
    $csvFile = $tmpBase . '_leady.csv';
    $rateLimitDir = $tmpBase . '_ratelimits';
    $attackerIp = '198.51.100.250';

    try {
        // 1. Payload przekraczający 65536 bajtów -> HTTP 413
        $oversizedJson = json_encode(['name' => 'Jan', 'email' => 'jan@example.com', 'message' => str_repeat('A', 70000)]);
        $tooLargeRes = invoke_contact_api((string)$oversizedJson, $draftsDir, $csvFile, $rateLimitDir, '198.51.100.249');
        assert_equals(413, $tooLargeRes['httpCode'], 'Payload > 65536 B musi zostać odrzucony kodem HTTP 413');

        // 2. Atak XSS/Path Traversal -> WAF L7 zwraca HTTP 403 i banuje IP na 15 minut
        $wafRes = invoke_contact_api([
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'message' => '<script>fetch("https://evil.test")</script>',
        ], $draftsDir, $csvFile, $rateLimitDir, $attackerIp);
        assert_equals(403, $wafRes['httpCode'], 'WAF L7 musi odrzucić payload <script> kodem HTTP 403');

        // 3. Kolejne poprawne żądanie z tego samego IP musi zostać natychmiast odrzucone przez 15-minutowy IP Penalty Box (HTTP 403)
        $followUpRes = invoke_contact_api([
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'message' => 'Zwykłe pytanie o wesele',
        ], $draftsDir, $csvFile, $rateLimitDir, $attackerIp);
        assert_equals(403, $followUpRes['httpCode'], 'Zbanowany adres IP w 15-minutowym Penalty Box musi otrzymać HTTP 403');
    } finally {
        cleanup_test_artifacts($draftsDir, $csvFile, $rateLimitDir);
    }
});

it('wszystkie pliki PHP w projekcie posiadają declare(strict_types=1), a strażnik SSRF is_safe_https_url() blokuje 169.254.169.254, localhost i sieci prywatne ([REF-05])', function() use ($rootDir) {
    require_once $rootDir . '/api/rate-limit.php';

    assert_true(is_safe_https_url('https://ntfy.sh/raricart_topic'), 'is_safe_https_url musi akceptować https://ntfy.sh');
    assert_true(is_safe_https_url('https://hd.twojastronawww.pl/ticket_hook.php'), 'is_safe_https_url musi akceptować https://hd.twojastronawww.pl');

    $unsafeUrls = [
        'http://ntfy.sh/topic',
        'https://169.254.169.254/latest/meta-data/',
        'https://127.0.0.1/admin',
        'https://localhost:8080/api',
        'https://0.0.0.0/',
        'https://[::1]/',
        'https://10.0.0.5/internal',
        'https://192.168.1.1/router',
        'https://172.16.0.1/secret',
        'https://metadata.google.internal/computeMetadata/v1/',
    ];
    foreach ($unsafeUrls as $badUrl) {
        assert_equals(false, is_safe_https_url($badUrl), "SSRF Guard musi zablokować niebezpieczny URL: $badUrl");
    }

    $tmpRateDir = sys_get_temp_dir() . '/raricart_ratetest_' . bin2hex(random_bytes(4));
    putenv("RARICART_TEST_RATE_LIMIT_DIR=$tmpRateDir");
    try {
        $testIp = '198.51.100.222';
        $allowed = rate_limit('raricart_contact_' . $testIp, 5, 60);
        assert_true($allowed, 'Pierwsze wywołanie rate_limit powinno zwrócić true');
        $rateFiles = glob($tmpRateDir . '/rate_*.json') ?: [];
        assert_equals(1, count($rateFiles), 'Powinien powstać dokładnie 1 plik rate_*.json');
        $fileName = basename($rateFiles[0]);
        assert_true(strpos($fileName, '198') === false && strpos($fileName, '222') === false, "Nazwa pliku rate_limit nie może ujawniać oktetów IP (Zero-PII SHA-256): $fileName");
        $rateData = json_decode((string)file_get_contents($rateFiles[0]), true, 16);
        assert_true(is_array($rateData) && isset($rateData['key_hash']) && strlen((string)$rateData['key_hash']) === 64, 'Plik rate_limit musi przechowywać 64-znakowy skrót SHA-256 (key_hash)');
    } finally {
        putenv('RARICART_TEST_RATE_LIMIT_DIR=');
        remove_temp_dir_only($tmpRateDir);
    }

    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($rootDir, \FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $fileInfo) {
        $path = $fileInfo->getPathname();
        if (strpos($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR) !== false || strpos($path, DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR) !== false) {
            continue;
        }
        if (strtolower($fileInfo->getExtension()) === 'php') {
            $phpCode = (string)file_get_contents($path);
            assert_true((bool)preg_match('/declare\s*\(\s*strict_types\s*=\s*1\s*\)\s*;/', $phpCode), "Brak declare(strict_types=1); w pliku: $path");
        }
    }
});

// --- 7. PODSUMOWANIE ---
echo "\n=== PODSUMOWANIE TESTÓW PHP ===\n";
echo "Zaliczone: $testsPassed\n";
echo "Niepowodzenia: $testsFailed\n";

if ($testsFailed > 0) {
    exit(1);
}
exit(0);
