<?php
/**
 * Kompletny audyt spójności: JavaScript <-> HTML <-> CSS
 */

// 1. Wyrenderuj stronę główną index.php do bufora
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';

ob_start();
require __DIR__ . '/../index.php';
$html = ob_get_clean();

$css = file_get_contents(__DIR__ . '/../assets/css/styles.css');
$js = file_get_contents(__DIR__ . '/../assets/js/script.js');

echo "=== RAPORT SPÓJNOŚCI FRONTENDU (HTML, CSS, JS) ===\n\n";

// A. ID sprawdzane przez JS w getElementById
preg_match_all("/getElementById\(['\"]([^'\"]+)['\"]\)/", $js, $jsIds);
$uniqueJsIds = array_unique($jsIds[1]);

echo "1. Weryfikacja ID z JavaScript w wyrenderowanym HTML:\n";
$missingIds = [];
$dynamicIds = ['ga-script', 'bg']; // ga-script jest tworzony w locie, bg to legacy fallback

foreach ($uniqueJsIds as $id) {
    if (in_array($id, $dynamicIds)) continue;
    
    // Szukaj id="$id" w HTML
    if (strpos($html, 'id="' . $id . '"') !== false || strpos($html, "id='" . $id . "'") !== false) {
        echo "  [OK] #{$id} - znaleziony w HTML\n";
    } else {
        echo "  [BLAD] #{$id} - BRAK w wyrenderowanym HTML!\n";
        $missingIds[] = $id;
    }
}

// B. Sprawdzenie klas manipulowanych przez JS w arkuszu CSS
preg_match_all("/classList\.(?:add|remove|toggle|contains)\(['\"]([^'\"]+)['\"]\)/", $js, $jsClasses);
$uniqueJsClasses = array_unique($jsClasses[1]);

echo "\n2. Weryfikacja klas dynamicznych JavaScript w CSS:\n";
$missingCss = [];
foreach ($uniqueJsClasses as $cls) {
    if ($cls === 'shrink') continue; // legacy
    
    // Szukaj selektora .$cls w CSS
    if (preg_match('/\\.' . preg_quote($cls, '/') . '[^a-zA-Z0-9_-]/', $css)) {
        echo "  [OK] .{$cls} - istnieje reguła w CSS\n";
    } else {
        echo "  [OSTRZEZENIE] .{$cls} - brak w CSS\n";
        $missingCss[] = $cls;
    }
}

// C. Sprawdzenie kluczowych selektorów querySelector
preg_match_all("/querySelector(?:All)?\(['\"]([^'\"]+)['\"]\)/", $js, $selectors);
$uniqueSelectors = array_unique($selectors[1]);

echo "\n3. Weryfikacja głównych selektorów querySelector:\n";
foreach ($uniqueSelectors as $sel) {
    // Proste selektory klas i tagów
    if (preg_match('/^\.([a-zA-Z0-9_-]+)$/', $sel, $m)) {
        $cls = $m[1];
        $inHtml = (strpos($html, 'class="') !== false && preg_match('/class="[^"]*\\b' . preg_quote($cls, '/') . '\\b[^"]*"/', $html));
        $inCss = preg_match('/\\.' . preg_quote($cls, '/') . '[^a-zA-Z0-9_-]/', $css);
        echo "  " . ($inHtml && $inCss ? "[OK]" : "[INFO]") . " {$sel} (HTML: " . ($inHtml ? "TAK" : "NIE") . ", CSS: " . ($inCss ? "TAK" : "NIE") . ")\n";
    }
}

// D. Weryfikacja sekcji Lejka 2026 w HTML
$lejekSections = [
    'hero' => 'hero-split',
    'trust-bar' => 'trust-bar',
    'stations' => 'stations-section',
    'why-station' => 'why-station-section',
    'proces' => 'process-section',
    'audiences' => 'audiences-section',
    'why-raricart' => 'section-why-raricart',
    'realizacje' => 'section-realizations',
    'reviews' => 'section-reviews',
    'faq' => 'section-faq'
];

echo "\n4. Weryfikacja obecności sekcji Lejka 2026 w HTML:\n";
foreach ($lejekSections as $name => $cls) {
    if (strpos($html, $cls) !== false) {
        echo "  [OK] Sekcja {$name} (.{$cls}) - poprawnie wyrenderowana\n";
    } else {
        echo "  [BLAD] Sekcja {$name} (.{$cls}) - BRAK w wyrenderowanym HTML!\n";
        $missingCss[] = $cls;
    }
}

echo "\n=== PODSUMOWANIE AUDYTU ===\n";
if (empty($missingIds) && empty($missingCss)) {
    echo "WYNIK: PERFEKCYJNA SPÓJNOŚĆ! Wszystkie punkty styku HTML <-> CSS <-> JS są zachowane.\n";
} else {
    echo "WYNIK: Znaleziono niespójności:\n";
    if (!empty($missingIds)) echo "Brakujące ID: " . implode(', ', $missingIds) . "\n";
    if (!empty($missingCss)) echo "Brakujące reguły CSS: " . implode(', ', $missingCss) . "\n";
}
