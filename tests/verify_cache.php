<?php
/**
 * Test weryfikujący poprawność mechanizmu cache i wersji plików.
 */

$baseUrl = "http://localhost/index.php"; // Zakładamy lokalne środowisko
$errors = [];

echo "--- START TESTU CACHE & BŁĘDÓW ---\n\n";

// 1. Sprawdzenie nagłówka Cache-Control dla index.php (symulacja żądania jeśli możliwe, lub analiza lokalna .htaccess)
echo "[1] Testowanie reguł .htaccess...\n";
$htaccess = file_get_contents(__DIR__ . '/../.htaccess');
if (strpos($htaccess, 'ExpiresByType text/html "access plus 0 seconds"') !== false) {
    echo "✅ Poprawnie skonfigurowano brak cache dla HTML.\n";
} else {
    $errors[] = "Błąd w .htaccess: Brak 'access plus 0 seconds' dla HTML.";
}

// 2. Weryfikacja funkcji get_val w index.php
echo "[2] Testowanie logiki cache-busting w index.php...\n";
ob_start();
include __DIR__ . '/../index.php';
$output = ob_get_clean();

// Sprawdzamy czy ścieżki do obrazów w galerii lub ofercie mają ?v= i czy nie jest to time() który by się zmienił przy ponownym renderowaniu
$matches = [];
preg_match_all('/src="([^"]+\?v=(\d+))"/', $output, $matches);

if (!empty($matches[1])) {
    echo "✅ Znaleziono " . count($matches[1]) . " zasobów z parametrem wersji.\n";
    $firstVer = $matches[2][0];
    
    // Symulacja opóźnienia i ponowne sprawdzenie (czy wersja się nie zmienia co sekundę)
    sleep(1);
    ob_start();
    include __DIR__ . '/../index.php';
    $output2 = ob_get_clean();
    preg_match_all('/src="([^"]+\?v=(\d+))"/', $output2, $matches2);
    
    if (!empty($matches2[2]) && $matches2[2][0] === $firstVer) {
        echo "✅ Parametr wersji jest stabilny (nie zmienia się przy każdym odświeżeniu).\n";
    } else {
        $errors[] = "Parametr wersji zmienia się przy każdym odświeżeniu (nadal używasz time()?).";
    }
} else {
    echo "⚠️ Nie znaleziono obrazów z ?v= w wyrenderowanym HTML. Sprawdź czy content.json istnieje.\n";
}

// 3. Sprawdzenie błędnego znacznika div
echo "[3] Testowanie struktury HTML...\n";
if (strpos($output, '</div>' . "\n" . '                </div>' . "\n" . '                <div class="form-group full-width">') === false) {
    echo "✅ Wygląda na to, że nadmiarowy div został usunięty.\n";
} else {
    $errors[] = "Nadal wykryto nadmiarowy zamykający element </div> w formularzu.";
}

echo "\n--- PODSUMOWANIE ---\n";
if (empty($errors)) {
    echo "🎉 WSZYSTKIE TESTY ZALICZONE!\n";
} else {
    echo "❌ WYKRYTO BŁĘDY:\n";
    foreach ($errors as $err) {
        echo "- $err\n";
    }
    exit(1);
}
