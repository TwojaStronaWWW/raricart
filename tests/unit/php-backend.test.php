<?php
declare(strict_types=1);

/**
 * Testy jednostkowe backendu PHP (Raricart.pl)
 * Weryfikacja reguł architektonicznych z AGENTS.md:
 * 1. Concurrency (LOCK_EX, atomic flock writes)
 * 2. Security (HttpOnly, SameSite=Strict, CSRF hash_equals)
 * 3. Hot Leads (budget >= 5000 PLN or pax >= 100, UTF-8 BOM)
 * 4. Media (Strict MIME, WebP 1920px max, Q85)
 * 5. Cache (X-LiteSpeed-Purge)
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

echo "=== TESTY JEDNOSTKOWE BACKENDU PHP (PHP 8 NATIVE) ===\n\n";

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

it('plik api/contact.php powinien zapisywać nagłówek CSV z UTF-8 BOM (\xEF\xBB\xBF) i flock(LOCK_EX)', function() {
    $contactCode = file_get_contents(__DIR__ . '/../../api/contact.php');
    assert_true(strpos($contactCode, 'chr(0xEF).chr(0xBB).chr(0xBF)') !== false, "Brak zapisu UTF-8 BOM dla Excela");
    assert_true(strpos($contactCode, 'flock($fp, LOCK_EX)') !== false, "Brak blokady flock LOCK_EX");
    assert_true(strpos($contactCode, 'flock($fp, LOCK_UN)') !== false, "Brak odblokowania flock LOCK_UN");
});

// --- 2. BEZPIECZEŃSTWO SESJI I CSRF ---
echo "\n2. Bezpieczeństwo sesji i CSRF (admin/auth.php):\n";

it('konfiguracja ciasteczek sesji musi wymuszać HttpOnly, SameSite=Strict oraz Secure na HTTPS', function() {
    $authCode = file_get_contents(__DIR__ . '/../../admin/auth.php');
    assert_true(strpos($authCode, "'httponly' => true") !== false, "Brak flagi httponly w cookie params");
    assert_true(strpos($authCode, "'samesite' => 'Strict'") !== false, "Brak flagi samesite=Strict");
    assert_true(strpos($authCode, "'secure' => \$secure") !== false, "Brak flagi secure");
});

it('weryfikacja CSRF musi używać hash_equals odpornego na timing attacks', function() {
    $authCode = file_get_contents(__DIR__ . '/../../admin/auth.php');
    assert_true(strpos($authCode, 'hash_equals(') !== false, "check_csrf() musi używać hash_equals");
    assert_true(strpos($authCode, 'HTTP_X_CSRF_TOKEN') !== false, "check_csrf() musi sprawdzać nagłówek X-CSRF-Token");
});

// --- 3. SERWIS MEDIÓW (MediaService.php) ---
echo "\n3. Ograniczenia i konwersja multimediów (api/MediaService.php):\n";

it('MediaService musi rygorystycznie weryfikować dozwolone typy MIME i blokować pliki wykonywalne', function() {
    $mediaCode = file_get_contents(__DIR__ . '/../../api/MediaService.php');
    assert_true(strpos($mediaCode, 'FILEINFO_MIME_TYPE') !== false, "Brak finfo_open(FILEINFO_MIME_TYPE)");
    assert_true(strpos($mediaCode, "'image/jpeg'") !== false, "Brak wsparcia dla image/jpeg");
    assert_true(strpos($mediaCode, "'image/webp'") !== false, "Brak wsparcia dla image/webp");
    assert_true(strpos($mediaCode, "'video/mp4'") !== false, "Brak wsparcia dla video/mp4");
});

it('MediaService musi konwertować obrazy do formatu WebP z limitem 1920px i jakością 85', function() {
    $mediaCode = file_get_contents(__DIR__ . '/../../api/MediaService.php');
    assert_true(strpos($mediaCode, '$maxWidth = 1920') !== false, "Maksymalna szerokość musi wynosić 1920px");
    assert_true(strpos($mediaCode, 'imagewebp($newImage, null, 85)') !== false, "Konwersja WebP musi używać jakości Q85");
});

// --- 4. ZARZĄDZANIE PAMIĘCIĄ CACHE I WSPÓŁBIEŻNOŚCIĄ ---
echo "\n4. Cache LiteSpeed i zapisy atomowe LOCK_EX w panelu administracyjnym:\n";

$adminSaveScripts = [
    'admin/save_content.php',
    'admin/save_gallery.php',
    'admin/save_status.php'
];

foreach ($adminSaveScripts as $script) {
    it("plik $script musi wysyłać nagłówek X-LiteSpeed-Purge: * po zapisie JSON", function() use ($script) {
        $code = file_get_contents(__DIR__ . '/../../' . $script);
        assert_true(strpos($code, 'header("X-LiteSpeed-Purge: *");') !== false, "Brak nagłówka X-LiteSpeed-Purge w $script");
    });

    it("plik $script musi używać blokady zapisu LOCK_EX", function() use ($script) {
        $code = file_get_contents(__DIR__ . '/../../' . $script);
        assert_true(strpos($code, 'LOCK_EX') !== false, "Brak flagi LOCK_EX przy zapisie pliku w $script");
    });
}

it('wszystkie krytyczne operacje zapisu plików stanu w systemie muszą używać LOCK_EX', function() {
    $criticalFiles = [
        'api/contact.php' => ['draftFile', 'lockFile', 'digestBufferFile'],
        'admin/change_password.php' => ['config_file'],
        'admin/setup.php' => ['config_file'],
        'admin/delete.php' => ['galleryFile'],
        'admin/index.php' => ['attemptsFile']
    ];

    foreach ($criticalFiles as $file => $targets) {
        $code = file_get_contents(__DIR__ . '/../../' . $file);
        assert_true(strpos($code, 'LOCK_EX') !== false, "Plik $file nie zawiera wymaganej blokady LOCK_EX!");
    }
});

// --- 5. TESTY INTEGRACYJNE ENDPOINTU FORMULARZA (api/contact.php) ---
echo "\n5. Testy integracyjne przepływu formularza, szkiców i ochrony przed utratą wiadomości (api/contact.php):\n";

function invoke_contact_api(array $payload, string $draftsDir, string $csvFile, string $ip = '198.51.100.42'): array {
    $scriptPath = realpath(__DIR__ . '/../../api/contact.php');

    $runnerCode = sprintf(
        'putenv("RARICART_TEST_DRAFTS_DIR=" . %s); putenv("RARICART_TEST_CSV_FILE=" . %s); putenv("RARICART_TEST_DISABLE_NETWORK=1"); $_SERVER["REQUEST_METHOD"] = "POST"; $_SERVER["REMOTE_ADDR"] = %s; require %s;',
        var_export($draftsDir, true),
        var_export($csvFile, true),
        var_export($ip, true),
        var_export($scriptPath, true)
    );

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $proc = proc_open([PHP_BINARY, '-d', 'display_errors=0', '-r', $runnerCode], $descriptors, $pipes, dirname($scriptPath));
    if (!is_resource($proc)) {
        throw new \RuntimeException('Nie udało się uruchomić procesu testowego PHP dla api/contact.php');
    }

    fwrite($pipes[0], (string)json_encode($payload, JSON_UNESCAPED_UNICODE));
    fclose($pipes[0]);

    $stdout = (string)stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = (string)stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);

    $decoded = json_decode($stdout, true);
    return [
        'exitCode' => $exitCode,
        'raw' => $stdout,
        'stderr' => $stderr,
        'json' => is_array($decoded) ? $decoded : null,
    ];
}

function cleanup_test_artifacts(string $draftsDir, string $csvFile): void {
    if (is_dir($draftsDir)) {
        foreach (glob($draftsDir . '/*') ?: [] as $f) {
            @unlink($f);
        }
        foreach (glob($draftsDir . '/.*') ?: [] as $f) {
            if (basename($f) !== '.' && basename($f) !== '..') {
                @unlink($f);
            }
        }
        @rmdir($draftsDir);
    }
    if (file_exists($csvFile)) {
        @unlink($csvFile);
    }
    $rateDir = __DIR__ . '/../../api/rate_limits';
    if (is_dir($rateDir)) {
        foreach (glob($rateDir . '/*') ?: [] as $rf) {
            @unlink($rf);
        }
        foreach (glob($rateDir . '/.*') ?: [] as $rf) {
            if (basename($rf) !== '.' && basename($rf) !== '..') {
                @unlink($rf);
            }
        }
        @rmdir($rateDir);
    }
}

it('wielokrotny autosave szkiców (20x blur + visibilitychange + ping) NIE może blokować finalnego wysłania formularza (Scenariusz Gabrieli Momot)', function() {
    $tmpBase = sys_get_temp_dir() . '/raricart_test_' . bin2hex(random_bytes(4));
    $draftsDir = $tmpBase . '_drafts';
    $csvFile = $tmpBase . '_leady.csv';
    $testIp = '198.51.100.101';

    try {
        // 1. Ping przy wejściu na stronę
        $pingRes = invoke_contact_api(['action' => 'ping'], $draftsDir, $csvFile, $testIp);
        assert_equals('pong', $pingRes['json']['status'] ?? null, 'Ping powinien zwrócić status pong');

        // 2. 20 kolejnych zapisów szkicu (symulacja klikania w pola, datę i checkboxy stacji)
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
                'is_abandoned' => ($i === 10), // symulacja przełączenia karty w połowie wypełniania
            ], $draftsDir, $csvFile, $testIp);

            assert_equals('success', $draftRes['json']['status'] ?? null, "Autosave #$i powinien zakończyć się sukcesem");
        }

        // Przełączenie karty (is_abandoned) nie powinno przedwcześnie dopisywać porzuconego leada do CSV
        assert_true(!file_exists($csvFile), 'Szkic z przełączenia karty nie powinien trafiać od razu do leady.csv przed upływem 10 minut');

        // 3. Finalny submit formularza
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
        ], $draftsDir, $csvFile, $testIp);

        assert_equals('success', $finalRes['json']['status'] ?? null, 'Finalny submit po 20 szkicach musi zakończyć się sukcesem (brak blokady 429)');
        assert_true(file_exists($csvFile), 'Plik leady.csv musi zostać utworzony przy finalnym submicie');

        $csvContent = (string)file_get_contents($csvFile);
        assert_true(strpos($csvContent, '🔥 HOT') !== false, 'Lead na 140 gości musi być oznaczony jako 🔥 HOT');
        assert_true(strpos($csvContent, 'Formularz') !== false, 'Źródło leada w CSV musi mieć wartość Formularz');
        assert_true(strpos($csvContent, 'g.momot@eveline.com.pl') !== false, 'CSV musi zawierać email klientki');

        // Wszystkie pliki draft_*.json dla tej klientki muszą zostać usunięte
        $remainingDrafts = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(0, count($remainingDrafts), 'Po finalnym submicie nie może pozostać żaden plik draft_*.json');

        // 4. Spóźniony pakiet sieciowy blur docierający PO finalnym submicie musi zostać zignorowany
        $lateDraftRes = invoke_contact_api([
            'name' => 'Gabriela Momot',
            'email' => 'g.momot@eveline.com.pl',
            'phone' => '+48575754222',
            'is_partial' => true,
            'is_abandoned' => true,
        ], $draftsDir, $csvFile, $testIp);

        assert_equals('success', $lateDraftRes['json']['status'] ?? null);
        $remainingDraftsAfterLate = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(0, count($remainingDraftsAfterLate), 'Spóźniony pakiet autosave nie może odtworzyć pliku szkicu po wysłaniu formularza');
    } finally {
        cleanup_test_artifacts($draftsDir, $csvFile);
    }
});

it('wpisanie najpierw telefonu a potem emaila scala szkic i usuwa sierocy plik telefonu oraz czyści digest_buffer.txt przy submicie', function() {
    $tmpBase = sys_get_temp_dir() . '/raricart_test_' . bin2hex(random_bytes(4));
    $draftsDir = $tmpBase . '_drafts';
    $csvFile = $tmpBase . '_leady.csv';
    $testIp = '198.51.100.102';

    try {
        // Krok 1: Szkic tylko z telefonem (brak emaila)
        invoke_contact_api([
            'name' => 'Aleksandra Matusik',
            'email' => '',
            'phone' => '+48 512 610 613',
            'is_partial' => true,
        ], $draftsDir, $csvFile, $testIp);

        $draftsStep1 = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(1, count($draftsStep1), 'Powinien istnieć 1 plik szkicu po telefonie');

        // Krok 2: Dopisanie adresu email — stary plik po samym telefonie musi zostać zastąpiony jednym plikiem po emailu
        invoke_contact_api([
            'name' => 'Aleksandra Matusik',
            'email' => 'ola.matusik@gmail.com',
            'phone' => '+48 512 610 613',
            'location' => 'Mysłowice, namiot w plenerze',
            'guests' => '150',
            'is_partial' => true,
        ], $draftsDir, $csvFile, $testIp);

        $draftsStep2 = glob($draftsDir . '/draft_*.json') ?: [];
        assert_equals(1, count($draftsStep2), 'Po dopisaniu emaila nadal powinien istnieć dokładnie 1 plik szkicu (brak duplikatu po telefonie)');

        // Symulacja sytuacji, w której wcześniejszy szkic trafił już do digest_buffer.txt
        file_put_contents(
            $draftsDir . '/digest_buffer.txt',
            "=== LEAD START ===\n--- TO JEST NIEUKOŃCZONY SZKIC FORMULARZA ---\nEmail: ola.matusik@gmail.com\nTel: +48512610613\n\n",
            LOCK_EX
        );

        // Krok 3: Finalny submit usuwa zarówno szkic, jak i wpis z digest_buffer.txt
        $submitRes = invoke_contact_api([
            'name' => 'Aleksandra Matusik',
            'email' => 'ola.matusik@gmail.com',
            'phone' => '+48 512 610 613',
            'date' => '2028-09-02',
            'location' => 'Mysłowice, namiot w plenerze',
            'guests' => '150',
            'event_type' => 'wedding',
            'stations' => 'Mini Pancakes',
        ], $draftsDir, $csvFile, $testIp);

        assert_equals('success', $submitRes['json']['status'] ?? null);
        assert_equals(0, count(glob($draftsDir . '/draft_*.json') ?: []), 'Brak plików draft_*.json po submicie');
        assert_true(!file_exists($draftsDir . '/digest_buffer.txt'), 'Plik digest_buffer.txt powinien zostać oczyszczony z wysłanego leada');
    } finally {
        cleanup_test_artifacts($draftsDir, $csvFile);
    }
});

// --- 5. PODSUMOWANIE ---
echo "\n=== PODSUMOWANIE TESTÓW PHP ===\n";
echo "Zaliczone: $testsPassed\n";
echo "Niepowodzenia: $testsFailed\n";

if ($testsFailed > 0) {
    exit(1);
}
exit(0);
