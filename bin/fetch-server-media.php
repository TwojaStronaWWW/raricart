<?php
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Dostęp tylko z poziomu CLI.\n");
}

$baseDir = dirname(__DIR__);
$targetDir = $baseDir . '/assets/img/migration_source';
$serverHost = 'https://raricart.pl';

echo "\n";
echo "=====================================================================\n";
echo "       POBIERANIE ZDJĘĆ I ZASOBÓW Z SERWERA PRODUKCYJNEGO            \n";
echo "=====================================================================\n";
echo "Serwer źródłowy: {$serverHost}\n";
echo "Katalog docelowy : {$targetDir}\n\n";

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

function download_file(string $url, string $destPath): bool
{
    $ch = curl_init($url);
    $fp = fopen($destPath, 'wb');
    if ($fp === false) {
        return false;
    }

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $success = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if (!$success || $httpCode !== 200 || filesize($destPath) === 0) {
        if (file_exists($destPath)) {
            @unlink($destPath);
        }
        return false;
    }

    return true;
}

// 1. Pobranie bazy galerii z serwera
echo "[1/2] Odpytywanie bazy galerii na serwerze ({$serverHost}/assets/data/gallery.json)...\n";
$galleryJsonRaw = @file_get_contents("{$serverHost}/assets/data/gallery.json");
$galleryItems = [];

if ($galleryJsonRaw !== false) {
    $galleryItems = json_decode($galleryJsonRaw, true) ?? [];
}

echo "Znaleziono w galerii produkcyjnej: " . count($galleryItems) . " zdjęć.\n\n";

$downloadedCount = 0;
$skippedCount = 0;

foreach ($galleryItems as $idx => $relPath) {
    $num = $idx + 1;
    $filename = basename($relPath);
    $destFile = $targetDir . '/' . $filename;
    $fileUrl = $serverHost . '/' . ltrim($relPath, '/');

    if (file_exists($destFile) && filesize($destFile) > 0) {
        echo " [{$num}/" . count($galleryItems) . "] [POMINIĘTO] {$filename} (plik już istnieje na dysku)\n";
        $skippedCount++;
        continue;
    }

    echo " [{$num}/" . count($galleryItems) . "] [POBIERANIE] {$filename}...";
    $ok = download_file($fileUrl, $destFile);
    if ($ok) {
        $sizeKb = round(filesize($destFile) / 1024, 1);
        echo " OK ({$sizeKb} KB)\n";
        $downloadedCount++;
    } else {
        echo " BŁĄD!\n";
    }
}

// 2. Pobranie zasobów z content.json (zdjęcia oferty, tła)
echo "\n[2/2] Odpytywanie zasobów treści ({$serverHost}/assets/data/content.json)...\n";
$contentJsonRaw = @file_get_contents("{$serverHost}/assets/data/content.json");
$contentImages = [];

if ($contentJsonRaw !== false) {
    $contentData = json_decode($contentJsonRaw, true) ?? [];
    
    // Rekurencyjne wyciągnięcie ścieżek do zdjęć
    array_walk_recursive($contentData, function($val) use (&$contentImages) {
        if (is_string($val) && preg_match('/\.(webp|jpg|jpeg|png)$/i', $val)) {
            $contentImages[] = $val;
        }
    });
}

$contentImages = array_unique($contentImages);
echo "Znaleziono w treści strony: " . count($contentImages) . " dodatkowych zdjęć (karty, tła, o nas).\n\n";

foreach ($contentImages as $idx => $relPath) {
    $num = $idx + 1;
    $filename = basename($relPath);
    $destFile = $targetDir . '/' . $filename;
    $fileUrl = $serverHost . '/' . ltrim($relPath, '/');

    if (file_exists($destFile) && filesize($destFile) > 0) {
        echo " [{$num}/" . count($contentImages) . "] [POMINIĘTO] {$filename} (plik już istnieje)\n";
        $skippedCount++;
        continue;
    }

    echo " [{$num}/" . count($contentImages) . "] [POBIERANIE] {$filename}...";
    $ok = download_file($fileUrl, $destFile);
    if ($ok) {
        $sizeKb = round(filesize($destFile) / 1024, 1);
        echo " OK ({$sizeKb} KB)\n";
        $downloadedCount++;
    } else {
        echo " BŁĄD!\n";
    }
}

echo "\n=====================================================================\n";
echo "PODSUMOWANIE POBIERANIA:\n";
echo "  Pobrano nowych plików : {$downloadedCount}\n";
echo "  Pominięto (istniejące): {$skippedCount}\n";
echo "  Katalog ze zdjęciami  : {$targetDir}\n";
echo "Wszystkie zdjęcia są gotowe do migracji do Cloudflare R2!\n";
echo "=====================================================================\n";
