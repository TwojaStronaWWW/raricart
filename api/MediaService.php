<?php
declare(strict_types=1);

namespace Raricart\Api;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/R2Client.php';

class MediaService
{
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
     * Upload and optimize an image or video, then upload directly to Cloudflare R2.
     *
     * @param string $tmpFilePath Path to temporary file
     * @param string $originalName Original filename
     * @param string $target Subfolder target ('gallery' or 'content')
     * @return array Result array with status, file URL, path, and metadata
     */
    public static function processAndUpload(string $tmpFilePath, string $originalName, string $target = 'gallery'): array
    {
        if (!file_exists($tmpFilePath) || !is_readable($tmpFilePath)) {
            return ['status' => 'error', 'message' => 'Plik tymczasowy nie istnieje lub jest nieczytelny.'];
        }

        $target = ($target === 'content') ? 'content' : 'gallery';

        // 1. Strict MIME verification
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpFilePath);
        finfo_close($finfo);

        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        $allowedTypes = array_merge($allowedImageTypes, $allowedVideoTypes);

        if (!in_array($mimeType, $allowedTypes, true)) {
            return ['status' => 'error', 'message' => "Nieobsługiwany format pliku ($mimeType). Dozwolone: JPG, PNG, WEBP, MP4."];
        }

        $isImage = in_array($mimeType, $allowedImageTypes, true);
        $ext = $isImage ? 'webp' : strtolower((string)pathinfo($originalName, PATHINFO_EXTENSION));
        if (!$isImage && empty($ext)) {
            $ext = 'mp4';
        }

        $prefix = $isImage ? 'img_' : 'vid_';
        $filename = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $r2Key = "{$target}/{$filename}";

        $payloadData = '';
        $outputMime = $isImage ? 'image/webp' : $mimeType;

        if ($isImage) {
            // GD Optimization and WebP conversion
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

            if ($sourceImage) {
                $width = imagesx($sourceImage);
                $height = imagesy($sourceImage);
                $maxWidth = 1920;

                if ($width > $maxWidth) {
                    $newWidth = $maxWidth;
                    $newHeight = (int)floor($height * ($maxWidth / $width));
                } else {
                    $newWidth = $width;
                    $newHeight = $height;
                }

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
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
            } else {
                $payloadData = (string)file_get_contents($tmpFilePath);
            }
        } else {
            // Video: direct binary payload
            $payloadData = (string)file_get_contents($tmpFilePath);
        }

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
                    'original_name' => $originalName,
                    'storage' => 'r2'
                ];
            }
        }

        // 3. Fallback: Local storage if R2 is unavailable
        $localDir = dirname(__DIR__) . "/assets/{$target}/";
        if (!is_dir($localDir)) {
            mkdir($localDir, 0755, true);
        }
        $localDestination = $localDir . $filename;
        file_put_contents($localDestination, $payloadData, LOCK_EX);

        $localWebPath = "assets/{$target}/{$filename}";
        return [
            'status' => 'success',
            'file' => $localWebPath,
            'url' => '/' . $localWebPath,
            'path' => $localWebPath,
            'target' => $target,
            'type' => $isImage ? 'image' : 'video',
            'original_name' => $originalName,
            'storage' => 'local',
            'warning' => 'Zapisano lokalnie (R2 niedostępne).'
        ];
    }

    /**
     * Delete an asset from Cloudflare R2 or local disk.
     */
    public static function deleteAsset(string $pathOrUrl): bool
    {
        $r2 = self::getR2Client();
        $cdnBase = self::getCdnBaseUrl();

        // Check if it's an R2 URL
        if (str_starts_with($pathOrUrl, $cdnBase)) {
            $key = ltrim(substr($pathOrUrl, strlen($cdnBase)), '/');
            if ($r2 !== null && !empty($key)) {
                return $r2->deleteObject($key);
            }
            return true;
        }

        // Check if it's an R2 relative key (e.g. gallery/..., content/...)
        if (str_starts_with($pathOrUrl, 'gallery/') || str_starts_with($pathOrUrl, 'content/')) {
            if ($r2 !== null) {
                return $r2->deleteObject($pathOrUrl);
            }
            return true;
        }

        // Local file fallback
        $localPath = dirname(__DIR__) . '/' . ltrim($pathOrUrl, '/');
        if (file_exists($localPath) && is_file($localPath)) {
            return @unlink($localPath);
        }

        return true;
    }
}
