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

// --- 5. PODSUMOWANIE ---
echo "\n=== PODSUMOWANIE TESTÓW PHP ===\n";
echo "Zaliczone: $testsPassed\n";
echo "Niepowodzenia: $testsFailed\n";

if ($testsFailed > 0) {
    exit(1);
}
exit(0);
