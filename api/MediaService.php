<?php
declare(strict_types=1);

namespace Raricart\Api;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/R2Client.php';

class MediaService
{
    public const MAX_IMAGE_BYTES = 15728640; // 15 MB
    public const MAX_VIDEO_BYTES = 52428800; // 50 MB
    public const MAX_IMAGE_DIMENSION = 8000; // 8000 px
    public const MAX_IMAGE_PIXELS = 36000000; // 36 MP

    private const ALLOWED_ASSET_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'mp4', 'webm'
    ];

    private const VIDEO_MIME_EXTENSION_MAP = [
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
    ];

    private static ?R2Client $r2Client = null;

    public static function getR2Client(): ?R2Client
    {
        if (self::$r2Client !== null) {
            return self::$r2Client;
        }

        if (
            defined('R2_ACCOUNT_ID') && !empty(constant('R2_ACCOUNT_ID')) &&
            defined('R2_ACCESS_KEY_ID') && !empty(constant('R2_ACCESS_KEY_ID')) &&
            defined('R2_SECRET_ACCESS_KEY') && !empty(constant('R2_SECRET_ACCESS_KEY')) &&
            defined('R2_BUCKET_NAME') && !empty(constant('R2_BUCKET_NAME'))
        ) {
            self::$r2Client = new R2Client(
                (string)constant('R2_ACCOUNT_ID'),
                (string)constant('R2_ACCESS_KEY_ID'),
                (string)constant('R2_SECRET_ACCESS_KEY'),
                (string)constant('R2_BUCKET_NAME'),
                'auto'
            );
        }

        return self::$r2Client;
    }

    public static function getCdnBaseUrl(): string
    {
        if (defined('CDN_BASE_URL') && !empty(constant('CDN_BASE_URL'))) {
            return rtrim((string)constant('CDN_BASE_URL'), '/');
        }
        return 'https://media.raricart.pl';
    }

    /**
     * Wyznacza i weryfikuje bezpieczny katalog docelowy przez realpath() (wyłącznie assets/gallery lub assets/content).
     */
    private static function resolveSafeTargetDir(string $target): ?string
    {
        if ($target !== 'gallery' && $target !== 'content') {
            return null;
        }

        $assetsRoot = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets';
        if (!is_dir($assetsRoot) || is_link($assetsRoot)) {
            return null;
        }
        $realAssetsRoot = realpath($assetsRoot);
        if ($realAssetsRoot === false) {
            return null;
        }

        $dirPath = $realAssetsRoot . DIRECTORY_SEPARATOR . $target;
        if (is_link($dirPath)) {
            return null;
        }
        if (!is_dir($dirPath)) {
            if (!@mkdir($dirPath, 0755, true)) {
                return null;
            }
        }

        $htaccessPath = $dirPath . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccessPath)) {
            $htContent = "Options -Indexes\n"
                . "<FilesMatch \"\\.(php|phtml|phar|php[0-9]*|cgi|pl|py|sh|htaccess|env|json|csv|bak|dist|ini)$\">\n"
                . "    <IfModule mod_authz_core.c>\n"
                . "        Require all denied\n"
                . "    </IfModule>\n"
                . "    <IfModule !mod_authz_core.c>\n"
                . "        Order Allow,Deny\n"
                . "        Deny from all\n"
                . "    </IfModule>\n"
                . "</FilesMatch>\n";
            @file_put_contents($htaccessPath, $htContent, LOCK_EX);
        }

        $realTargetDir = realpath($dirPath);
        if ($realTargetDir === false || is_link($realTargetDir)) {
            return null;
        }

        $expectedPrefix = rtrim($realAssetsRoot, "\\/") . DIRECTORY_SEPARATOR;
        if (!str_starts_with($realTargetDir, $expectedPrefix)) {
            return null;
        }

        return $realTargetDir;
    }

    /**
     * Zwraca listę zweryfikowanych przez realpath() dozwolonych katalogów mediów lokalnych.
     *
     * @return string[]
     */
    private static function getAllowedLocalMediaDirs(): array
    {
        $dirs = [];
        foreach (['gallery', 'content'] as $sub) {
            $candidate = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $sub;
            if (is_dir($candidate) && !is_link($candidate)) {
                $real = realpath($candidate);
                if ($real !== false) {
                    $dirs[] = rtrim($real, "\\/");
                }
            }
        }
        return $dirs;
    }

    /**
     * Upload and optimize an image or video, then upload directly to Cloudflare R2.
     *
     * @param string $tmpFilePath Path to temporary file
     * @param string $originalName Original filename (używane wyłącznie informacyjnie po oczyszczeniu, NIGDY do rozszerzeń/ścieżek)
     * @param string $target Subfolder target ('gallery' or 'content')
     * @return array Result array with status, file URL, path, and metadata
     */
    public static function processAndUpload(string $tmpFilePath, string $originalName, string $target = 'gallery'): array
    {
        if (
            $tmpFilePath === '' ||
            str_contains($tmpFilePath, "\0") ||
            !file_exists($tmpFilePath) ||
            !is_file($tmpFilePath) ||
            is_link($tmpFilePath) ||
            !is_readable($tmpFilePath)
        ) {
            return ['status' => 'error', 'message' => 'Plik tymczasowy nie istnieje lub jest nieczytelny.'];
        }

        $fileSize = @filesize($tmpFilePath);
        if ($fileSize === false || $fileSize <= 0) {
            return ['status' => 'error', 'message' => 'Przesłany plik jest pusty lub nieprawidłowy.'];
        }

        $target = ($target === 'content') ? 'content' : 'gallery';

        // 1. Strict MIME verification via finfo_file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return ['status' => 'error', 'message' => 'Błąd inicjalizacji weryfikacji MIME.'];
        }
        $mimeType = (string)finfo_file($finfo, $tmpFilePath);
        finfo_close($finfo);

        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedVideoTypes = array_keys(self::VIDEO_MIME_EXTENSION_MAP);
        $allowedTypes = array_merge($allowedImageTypes, $allowedVideoTypes);

        if (!in_array($mimeType, $allowedTypes, true)) {
            return ['status' => 'error', 'message' => "Nieobsługiwany format pliku ($mimeType). Dozwolone: JPG, PNG, WEBP, GIF, MP4, WEBM."];
        }

        $isImage = in_array($mimeType, $allowedImageTypes, true);

        if ($isImage && $fileSize > self::MAX_IMAGE_BYTES) {
            return ['status' => 'error', 'message' => 'Plik obrazu przekracza maksymalny rozmiar 15 MB.'];
        }
        if (!$isImage && $fileSize > self::MAX_VIDEO_BYTES) {
            return ['status' => 'error', 'message' => 'Plik wideo przekracza maksymalny rozmiar 50 MB.'];
        }

        // Rozszerzenie wyznaczane WYŁĄCZNIE ze zweryfikowanego typu MIME (zero zaufania do nazwy pliku od użytkownika)
        $ext = $isImage ? 'webp' : self::VIDEO_MIME_EXTENSION_MAP[$mimeType];

        $prefix = $isImage ? 'img_' : 'vid_';
        $filename = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $r2Key = "{$target}/{$filename}";

        $payloadData = '';
        $outputMime = $isImage ? 'image/webp' : $mimeType;

        if ($isImage) {
            // Strażnik bomby dekompresyjnej (Pixel Flood / Decompression Bomb Guard) PRZED dekodowaniem GD
            $imgDimensions = @getimagesize($tmpFilePath);
            if (!is_array($imgDimensions) || empty($imgDimensions[0]) || empty($imgDimensions[1])) {
                return ['status' => 'error', 'message' => 'Plik graficzny jest uszkodzony lub ma nieprawidłowy nagłówek.'];
            }
            $origWidth = (int)$imgDimensions[0];
            $origHeight = (int)$imgDimensions[1];
            if (
                $origWidth <= 0 ||
                $origHeight <= 0 ||
                $origWidth > self::MAX_IMAGE_DIMENSION ||
                $origHeight > self::MAX_IMAGE_DIMENSION ||
                ($origWidth * $origHeight) > self::MAX_IMAGE_PIXELS
            ) {
                return [
                    'status' => 'error',
                    'message' => 'Obraz przekracza dopuszczalne wymiary bezpieczeństwa (max 8000x8000 px / 36 MP).'
                ];
            }

            // GD Optimization and WebP conversion (eliminuje również ukryty kod PHP w metadanych EXIF/komentarzach)
            $sourceImage = null;
            switch ($mimeType) {
                case 'image/jpeg':
                    $sourceImage = @imagecreatefromjpeg($tmpFilePath);
                    break;
                case 'image/png':
                    $sourceImage = @imagecreatefrompng($tmpFilePath);
                    if ($sourceImage) {
                        imagepalettetotruecolor($sourceImage);
                        imagealphablending($sourceImage, true);
                        imagesavealpha($sourceImage, true);
                    }
                    break;
                case 'image/gif':
                    $sourceImage = @imagecreatefromgif($tmpFilePath);
                    break;
                case 'image/webp':
                    $sourceImage = @imagecreatefromwebp($tmpFilePath);
                    break;
            }

            if (!$sourceImage) {
                return ['status' => 'error', 'message' => 'Nie udało się bezpiecznie zdekodować pliku graficznego.'];
            }

            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
            $maxWidth = 1920;

            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = max(1, (int)floor($height * ($maxWidth / $width)));
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            if (!$newImage) {
                imagedestroy($sourceImage);
                return ['status' => 'error', 'message' => 'Błąd alokacji bufora graficznego.'];
            }

            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($sourceImage);

            ob_start();
            imagewebp($newImage, null, 85);
            $payloadData = (string)ob_get_clean();
            imagedestroy($newImage);

            if ($payloadData === '') {
                return ['status' => 'error', 'message' => 'Błąd konwersji obrazu do formatu WebP.'];
            }
        } else {
            // Video: blokada polyglotów zawierających znaczniki PHP
            $headSample = (string)@file_get_contents($tmpFilePath, false, null, 0, 8192);
            if (stripos($headSample, '<?php') !== false || str_contains($headSample, '<?=')) {
                return ['status' => 'error', 'message' => 'Wykryto niedozwoloną sygnaturę skryptową w pliku wideo.'];
            }
            $payloadData = (string)file_get_contents($tmpFilePath);
        }

        $safeOriginalName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename(str_replace('\\', '/', $originalName))) ?: 'media';

        // 2. Upload to Cloudflare R2
        $r2 = self::getR2Client();
        $cdnBase = self::getCdnBaseUrl();

        if ($r2 !== null) {
            $uploaded = $r2->putObject($r2Key, $payloadData, $outputMime);
            if ($uploaded) {
                $finalUrl = "{$cdnBase}/{$r2Key}";
                return [
                    'status' => 'success',
                    'file' => $finalUrl,
                    'url' => $finalUrl,
                    'path' => $r2Key,
                    'target' => $target,
                    'type' => $isImage ? 'image' : 'video',
                    'original_name' => $safeOriginalName,
                    'storage' => 'r2'
                ];
            }
        }

        // 3. Fallback: Local storage strictly inside realpath(assets/gallery) or realpath(assets/content)
        $safeTargetDir = self::resolveSafeTargetDir($target);
        if ($safeTargetDir === null) {
            return ['status' => 'error', 'message' => 'Brak bezpiecznego katalogu docelowego dla mediów lokalnych.'];
        }

        if (!preg_match('/^(img|vid)_\d+_[a-f0-9]{8}\.(webp|mp4|webm)$/', $filename)) {
            return ['status' => 'error', 'message' => 'Nieprawidłowa nazwa pliku wyjściowego.'];
        }

        $localDestination = $safeTargetDir . DIRECTORY_SEPARATOR . $filename;
        if (is_link($localDestination)) {
            return ['status' => 'error', 'message' => 'Niedozwolony cel zapisu (symlink).'];
        }

        if (@file_put_contents($localDestination, $payloadData, LOCK_EX) === false) {
            return ['status' => 'error', 'message' => 'Błąd zapisu pliku na dysku lokalnym.'];
        }

        $realWritten = realpath($localDestination);
        if ($realWritten === false || !str_starts_with($realWritten, rtrim($safeTargetDir, "\\/") . DIRECTORY_SEPARATOR)) {
            @unlink($localDestination);
            return ['status' => 'error', 'message' => 'Naruszenie ograniczenia ścieżki zapisu.'];
        }

        $localWebPath = "assets/{$target}/{$filename}";
        return [
            'status' => 'success',
            'file' => $localWebPath,
            'url' => '/' . $localWebPath,
            'path' => $localWebPath,
            'target' => $target,
            'type' => $isImage ? 'image' : 'video',
            'original_name' => $safeOriginalName,
            'storage' => 'local',
            'warning' => 'Zapisano lokalnie (R2 niedostępne).'
        ];
    }

    /**
     * Delete an asset from Cloudflare R2 or local disk with strict Path Traversal & Symlink protection.
     */
    public static function deleteAsset(string $pathOrUrl): bool
    {
        $pathOrUrl = trim($pathOrUrl);
        if (
            $pathOrUrl === '' ||
            str_contains($pathOrUrl, "\0") ||
            str_contains($pathOrUrl, '..') ||
            str_contains($pathOrUrl, '\\')
        ) {
            return false;
        }

        $r2 = self::getR2Client();
        $cdnBase = self::getCdnBaseUrl();

        // 1. Check if it's an R2 CDN URL
        if (str_starts_with($pathOrUrl, $cdnBase . '/')) {
            $key = ltrim(substr($pathOrUrl, strlen($cdnBase)), '/');
            if (!preg_match('/^(gallery|content)\/[a-zA-Z0-9._-]+\.(' . implode('|', self::ALLOWED_ASSET_EXTENSIONS) . ')$/i', $key)) {
                return false;
            }
            if ($r2 !== null) {
                return $r2->deleteObject($key);
            }
            return true;
        }

        // Odrzuć wszelkie inne zewnętrzne URL-e lub wrappery strumieniowe (phar://, php://, file://, http://)
        if (str_contains($pathOrUrl, '://')) {
            return false;
        }

        $normalizedRel = ltrim($pathOrUrl, '/');

        // 2. Check if it's an R2 relative key (e.g. gallery/..., content/...)
        if (str_starts_with($normalizedRel, 'gallery/') || str_starts_with($normalizedRel, 'content/')) {
            if (!preg_match('/^(gallery|content)\/[a-zA-Z0-9._-]+\.(' . implode('|', self::ALLOWED_ASSET_EXTENSIONS) . ')$/i', $normalizedRel)) {
                return false;
            }
            if ($r2 !== null) {
                return $r2->deleteObject($normalizedRel);
            }
            // Jeśli R2 nieaktywne, sprawdź bezpiecznie odpowiednik lokalny w assets/{gallery|content}/
            $normalizedRel = 'assets/' . $normalizedRel;
        }

        // 3. Local file deletion strictly confined to realpath(assets/gallery) and realpath(assets/content)
        if (!preg_match('/^assets\/(gallery|content)\/[a-zA-Z0-9._-]+\.(' . implode('|', self::ALLOWED_ASSET_EXTENSIONS) . ')$/i', $normalizedRel)) {
            return false;
        }

        $baseName = basename($normalizedRel);
        if ($baseName === '' || str_starts_with($baseName, '.')) {
            return false;
        }

        $ext = strtolower((string)pathinfo($baseName, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_ASSET_EXTENSIONS, true)) {
            return false;
        }

        $candidatePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedRel);
        if (is_link($candidatePath) || !file_exists($candidatePath)) {
            return false;
        }

        $realTarget = realpath($candidatePath);
        if ($realTarget === false || !is_file($realTarget) || is_link($realTarget)) {
            return false;
        }

        $allowedDirs = self::getAllowedLocalMediaDirs();
        $isInsideAllowedDir = false;
        foreach ($allowedDirs as $allowedDir) {
            if (str_starts_with($realTarget, $allowedDir . DIRECTORY_SEPARATOR)) {
                $isInsideAllowedDir = true;
                break;
            }
        }

        if (!$isInsideAllowedDir) {
            return false;
        }

        return @unlink($realTarget);
    }
}
