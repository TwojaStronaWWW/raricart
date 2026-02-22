<?php
// optimize_existing.php - Skrypt do optymalizacji istniejących zdjęć na serwerze
// Tworzy kopie w formacie WebP i skaluje do max 1920px
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '512M'); // Zwiększamy limit pamięci na czas operacji
set_time_limit(300); // 5 minut na wykonanie

$baseDir = __DIR__ . '/../assets/';
$dirsToScan = ['gallery', 'images'];
$log = [];

function logMsg($msg) {
    global $log;
    $log[] = date('H:i:s') . ' - ' . $msg;
}

function processImage($filePath) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        return false;
    }

    // Sprawdź czy wersja WebP już istnieje
    $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $filePath);
    if (file_exists($webpPath)) {
        logMsg("Pominięto (WebP istnieje): " . basename($filePath));
        return false;
    }

    // Wczytaj obraz
    $image = null;
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $image = @imagecreatefromjpeg($filePath);
            break;
        case 'png':
            $image = @imagecreatefrompng($filePath);
            // Zachowaj przezroczystość dla PNG
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'gif':
            $image = @imagecreatefromgif($filePath);
            break;
    }

    if (!$image) {
        logMsg("BŁĄD: Nie można wczytać pliku: " . basename($filePath));
        return false;
    }

    // Skalowanie (jeśli większe niż 1920px)
    $width = imagesx($image);
    $height = imagesy($image);
    $maxWidth = 1920;

    if ($width > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = floor($height * ($maxWidth / $width));
        
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Obsługa przezroczystości przy skalowaniu
        if ($ext == 'png' || $ext == 'gif') {
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

    // Zapisz jako WebP
    if (imagewebp($image, $webpPath, 85)) {
        logMsg("SUKCES: Utworzono WebP dla: " . basename($filePath));
        imagedestroy($image);
        return true;
    } else {
        logMsg("BŁĄD: Nie udało się zapisać WebP: " . basename($webpPath));
        imagedestroy($image);
        return false;
    }
}

// 1. Przetwarzanie plików
$processedCount = 0;
foreach ($dirsToScan as $dir) {
    $fullPath = $baseDir . $dir;
    if (!is_dir($fullPath)) continue;

    logMsg("Skanowanie katalogu: $dir...");
    $files = scandir($fullPath);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $filePath = $fullPath . '/' . $file;
        if (is_file($filePath)) {
            if (processImage($filePath)) {
                $processedCount++;
            }
        }
    }
}

// 2. Aktualizacja JSON (jeśli istnieje)
$jsonFiles = [
    __DIR__ . '/../assets/data/gallery.json',
    __DIR__ . '/../assets/data/content.json'
];

foreach ($jsonFiles as $jsonFile) {
    if (file_exists($jsonFile)) {
        $content = file_get_contents($jsonFile);
        $originalContent = $content;
        
        // Prosta zamiana rozszerzeń w JSON
        // Zamieniamy .jpg", .png" na .webp"
        // Regex szuka rozszerzeń w cudzysłowach, żeby nie popsuć treści
        $content = preg_replace('/(\.jpg|\.jpeg|\.png|\.gif)("|\?)/i', '.webp$2', $content);
        
        if ($content !== $originalContent) {
            // Zrób kopię zapasową
            copy($jsonFile, $jsonFile . '.bak');
            file_put_contents($jsonFile, $content);
            logMsg("Zaktualizowano plik: " . basename($jsonFile));
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Optymalizacja Obrazów Raricart</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f0f0f0; }
        .log { background: #fff; padding: 20px; border: 1px solid #ccc; height: 80vh; overflow-y: scroll; }
        .success { color: green; font-weight: bold; }
        .info { color: blue; }
        .btn { padding: 10px 20px; background: #333; color: #fff; text-decoration: none; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Raport Optymalizacji</h1>
    <div class="log">
        <?php foreach ($log as $line): ?>
            <div><?php echo htmlspecialchars($line); ?></div>
        <?php endforeach; ?>
        <?php if ($processedCount == 0): ?>
            <div class="info">Nie znaleziono nowych obrazów do przetworzenia.</div>
        <?php else: ?>
            <div class="success">Przetworzono obrazów: <?php echo $processedCount; ?></div>
        <?php endif; ?>
    </div>
    <a href="/" class="btn">Wróć na stronę główną</a>
</body>
</html>
