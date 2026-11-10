<?php
declare(strict_types=1);

// optimize_existing.php - Skrypt do optymalizacji istniejących zdjęć na serwerze
ini_set('display_errors', '0');
error_reporting(E_ALL);

$isPureCli = (PHP_SAPI === 'cli' && !isset($_SERVER['REQUEST_METHOD']));
if (!$isPureCli) {
    require_once __DIR__ . '/auth.php';
    require_post_method(false);
    check_login();
    check_csrf(false);
    @session_write_close();
}

ini_set('memory_limit', '512M');
set_time_limit(300);

$baseDir = __DIR__ . '/../assets/';
$dirsToScan = ['gallery', 'images'];
$log = [];

function logMsg(string $msg): void {
    global $log;
    $log[] = date('H:i:s') . ' - ' . $msg;
}

function processImage(string $filePath): bool {
    if (is_link($filePath) || !is_file($filePath)) {
        return false;
    }

    $ext = strtolower((string)pathinfo($filePath, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
        return false;
    }

    $webpPath = (string)preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $filePath);
    if (file_exists($webpPath)) {
        logMsg("Pominięto (WebP istnieje): " . basename($filePath));
        return false;
    }

    // Strażnik bomby dekompresyjnej (max 8000x8000 px / 36 MP)
    $imgInfo = @getimagesize($filePath);
    if (!is_array($imgInfo) || ($imgInfo[0] ?? 0) <= 0 || ($imgInfo[1] ?? 0) <= 0) {
        logMsg("Pominięto (uszkodzony nagłówek obrazu): " . basename($filePath));
        return false;
    }
    if ($imgInfo[0] > 8000 || $imgInfo[1] > 8000 || ($imgInfo[0] * $imgInfo[1]) > 36000000) {
        logMsg("Odrzucono (przekroczony limit rozdzielczości 8000x8000 / 36 MP): " . basename($filePath));
        return false;
    }

    $image = null;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $image = @imagecreatefromjpeg($filePath);
            break;
        case 'png':
            $image = @imagecreatefrompng($filePath);
            if ($image) {
                @imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
        case 'gif':
            $image = @imagecreatefromgif($filePath);
            break;
    }

    if (!$image) {
        logMsg("BŁĄD: Nie można wczytać pliku: " . basename($filePath));
        return false;
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $maxWidth = 1920;

    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = max(1, (int)floor($height * ($maxWidth / $width)));

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($ext === 'png' || $ext === 'gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $newImage;
        logMsg("Przeskalowano: " . basename($filePath) . " ($width -> $newWidth)");
    }

    if (@imagewebp($image, $webpPath, 85)) {
        logMsg("SUKCES: Utworzono WebP dla: " . basename($filePath));
        imagedestroy($image);
        return true;
    }

    logMsg("BŁĄD: Nie udało się zapisać WebP: " . basename($webpPath));
    imagedestroy($image);
    return false;
}

$processedCount = 0;
foreach ($dirsToScan as $dir) {
    $fullPath = $baseDir . $dir;
    if (!is_dir($fullPath)) {
        continue;
    }

    logMsg("Skanowanie katalogu: $dir...");
    $files = scandir($fullPath) ?: [];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = $fullPath . '/' . $file;
        if (is_file($filePath) && !is_link($filePath)) {
            if (processImage($filePath)) {
                $processedCount++;
            }
        }
    }
}

$jsonFiles = [
    __DIR__ . '/../assets/data/gallery.json',
    __DIR__ . '/../assets/data/content.json'
];

foreach ($jsonFiles as $jsonFile) {
    if (file_exists($jsonFile)) {
        $content = (string)file_get_contents($jsonFile);
        $originalContent = $content;
        $content = (string)preg_replace('/(\.jpg|\.jpeg|\.png|\.gif)("|\?)/i', '.webp$2', $content);

        if ($content !== $originalContent) {
            @copy($jsonFile, $jsonFile . '.bak');
            file_put_contents($jsonFile, $content, LOCK_EX);
            logMsg("Zaktualizowano plik: " . basename($jsonFile));
        }
    }
}

if ($isPureCli) {
    foreach ($log as $line) {
        echo $line . "\n";
    }
    echo "Przetworzono obrazów: {$processedCount}\n";
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optymalizacja Obrazów - Raricart Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Raport Optymalizacji Obrazów</h2>
            <div class="optimize-log">
                <?php foreach ($log as $line): ?>
                    <div><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endforeach; ?>
                <?php if ($processedCount === 0): ?>
                    <div class="optimize-info">Nie znaleziono nowych obrazów do przetworzenia.</div>
                <?php else: ?>
                    <div class="optimize-success">Przetworzono obrazów: <?php echo (int)$processedCount; ?></div>
                <?php endif; ?>
            </div>
            <p class="auth-footer">
                <a href="dashboard.php" class="action-btn btn-inline-link">Wróć do panelu</a>
            </p>
        </div>
    </div>
</body>
</html>
