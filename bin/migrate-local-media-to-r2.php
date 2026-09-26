<?php
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Dostęp tylko z poziomu CLI.\n");
}

require_once __DIR__ . '/../config/config.php';
$secretsPath = __DIR__ . '/../config/secrets.php';
if (file_exists($secretsPath)) {
    require_once $secretsPath;
}
require_once __DIR__ . '/../api/R2Client.php';

use Raricart\Api\R2Client;

echo "=== Raricart R2 Migration Tool ===\n";

$sourceDir = BASE_DIR . '/assets/img/migration_source';
$jsonPath = DATA_DIR . '/gallery.json';

if (!is_dir($sourceDir)) {
    die("BŁĄD: Katalog źródłowy {$sourceDir} nie istnieje. Utwórz go i wrzuć stare zdjęcia.\n");
}

$requiredConstants = ['R2_ACCOUNT_ID', 'R2_ACCESS_KEY_ID', 'R2_SECRET_ACCESS_KEY', 'R2_BUCKET_NAME', 'CDN_BASE_URL'];
foreach ($requiredConstants as $const) {
    if (!defined($const) || empty(constant($const))) {
        die("BŁĄD: Brak definicji stałej {$const} w konfiguracji.\n");
    }
}

$r2 = new R2Client(R2_ACCOUNT_ID, R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET_NAME);
$migratedData = [];
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

$files = scandir($sourceDir);
$imageFiles = array_filter($files, function($file) use ($sourceDir) {
    return is_file($sourceDir . '/' . $file) && !str_starts_with($file, '.');
});

if (empty($imageFiles)) {
    die("INFO: Brak plików do migracji w {$sourceDir}.\n");
}

echo "Znaleziono " . count($imageFiles) . " plików do przetworzenia.\n\n";

foreach ($imageFiles as $filename) {
    $filePath = $sourceDir . '/' . $filename;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($filePath);

    if (!in_array($mimeType, $allowedMimes, true)) {
        echo "[POMINIĘTO] {$filename} (Nieobsługiwany MIME: {$mimeType})\n";
        continue;
    }

    echo "[PRZETWARZANIE] {$filename}...\n";

    if ($mimeType === 'image/webp') {
        $webpPayload = (string)file_get_contents($filePath);
    } else {
        if (!extension_loaded('gd')) {
            echo " -> BŁĄD: Rozszerzenie PHP GD nie jest załadowane (wymagane do konwersji {$mimeType} do WebP).\n";
            continue;
        }

        $imgResource = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($filePath),
            'image/png'  => imagecreatefrompng($filePath),
            default      => false
        };

        if ($imgResource === false) {
            echo " -> BŁĄD: GD nie mogło odczytać pliku.\n";
            continue;
        }

        imagepalettetotruecolor($imgResource);
        imagealphablending($imgResource, true);
        imagesavealpha($imgResource, true);

        ob_start();
        imagewebp($imgResource, null, 82);
        $webpPayload = (string)ob_get_clean();
        imagedestroy($imgResource);
    }

    if ($webpPayload === '') {
        echo " -> BŁĄD: Generowanie WebP zawiodło.\n";
        continue;
    }

    $newFilename = bin2hex(random_bytes(8)) . '_migrated.webp';
    $remoteKey = 'gallery/' . $newFilename;

    $uploaded = $r2->putObject($remoteKey, $webpPayload, 'image/webp');

    if ($uploaded) {
        $publicUrl = rtrim(CDN_BASE_URL, '/') . '/' . $remoteKey;
        $migratedData[] = [
            'id' => bin2hex(random_bytes(8)),
            'url' => $publicUrl,
            'src' => $publicUrl,
            'path' => $remoteKey,
            'created_at' => date('c'),
            'original_name' => $filename
        ];
        echo " -> SUKCES: Zapisano w R2 jako {$remoteKey}\n";
    } else {
        echo " -> BŁĄD: API R2 odrzuciło żądanie.\n";
        if (method_exists($r2, 'getLastError') && $r2->getLastError()) {
            echo "    Szczegóły: " . $r2->getLastError() . "\n";
        }
    }
}

if (empty($migratedData)) {
    die("\nBrak danych do zapisania w bazie JSON.\n");
}

echo "\nKonsolidacja bazy JSON ({$jsonPath})...\n";

if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

$fp = fopen($jsonPath, 'c+');
if ($fp === false) {
    die("BŁĄD: Nie można otworzyć bazy JSON.\n");
}

if (flock($fp, LOCK_EX)) {
    $filesize = filesize($jsonPath);
    $content = $filesize > 0 ? fread($fp, $filesize) : '';
    $existingData = $content ? (json_decode($content, true) ?? []) : [];

    // Nowe (zmigrowane) zdjęcia lądują na początku struktury
    $mergedData = array_merge($migratedData, $existingData);

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($mergedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    if (!headers_sent()) {
        header("X-LiteSpeed-Purge: *");
    }

    echo "Zakończono. Atomowo dopisano " . count($migratedData) . " rekordów do bazy.\n";
    echo "Twoje środowiska lokalne i produkcyjne są teraz w pełni zsynchronizowane przez R2.\n";
} else {
    fclose($fp);
    die("BŁĄD: Zasób JSON jest zablokowany (Race Condition).\n");
}
