<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/MediaService.php';

use Raricart\Api\MediaService;

require_post_method(true);
check_login();
check_csrf(true);
@session_write_close();

if (ob_get_length()) {
    ob_clean();
}
header('Content-Type: application/json; charset=UTF-8');

$raw = file_get_contents('php://input', false, null, 0, 8192);
$input = json_decode((string)$raw, true, 8);
$targetFile = $input['file'] ?? $_POST['file'] ?? null;

if (empty($targetFile) || !is_string($targetFile) || strlen($targetFile) > 512) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Brak lub nieprawidłowy parametr file.']);
    exit;
}

// 1. Bezpieczne usunięcie z magazynu (R2 lub ściśle ograniczony realpath wewnątrz assets/gallery | assets/content)
$deletedFromStorage = MediaService::deleteAsset($targetFile);

// 2. Atomowe usunięcie referencji z assets/data/gallery.json pod wyłączną blokadą LOCK_EX
$galleryFile = dirname(__DIR__) . '/assets/data/gallery.json';
if (file_exists($galleryFile)) {
    $fp = @fopen($galleryFile, 'c+b');
    if ($fp && @flock($fp, LOCK_EX)) {
        rewind($fp);
        $rawGallery = stream_get_contents($fp);
        $galleryData = (is_string($rawGallery) && $rawGallery !== '') ? json_decode($rawGallery, true) : null;
        if (is_array($galleryData)) {
            $initialCount = count($galleryData);
            $updatedData = array_values(array_filter($galleryData, static function ($item) use ($targetFile): bool {
                if (is_string($item)) {
                    return $item !== $targetFile;
                }
                if (is_array($item)) {
                    $url = (string)($item['url'] ?? $item['src'] ?? '');
                    $path = (string)($item['path'] ?? '');
                    return $url !== $targetFile && $path !== $targetFile;
                }
                return true;
            }));

            if (count($updatedData) !== $initialCount) {
                $encoded = json_encode($updatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if ($encoded !== false) {
                    ftruncate($fp, 0);
                    rewind($fp);
                    fwrite($fp, $encoded);
                    fflush($fp);
                }
            }
        }
        @flock($fp, LOCK_UN);
    }
    if ($fp) {
        fclose($fp);
    }
}

header("X-LiteSpeed-Purge: *");

echo json_encode(['status' => 'success', 'deleted' => $deletedFromStorage]);
