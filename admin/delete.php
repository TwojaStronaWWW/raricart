<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/MediaService.php';

use Raricart\Api\MediaService;

check_login();
check_csrf();

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Niedozwolona metoda żądania.']);
    exit;
}

$input = json_decode((string)file_get_contents('php://input'), true);
$targetFile = $input['file'] ?? $_POST['file'] ?? null;

if (empty($targetFile) || !is_string($targetFile)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Brak parametru file.']);
    exit;
}

// 1. Delete from storage (R2 or local)
$deletedFromStorage = MediaService::deleteAsset($targetFile);

// 2. Also remove from assets/data/gallery.json if present
$galleryFile = dirname(__DIR__) . '/assets/data/gallery.json';
if (file_exists($galleryFile)) {
    $galleryData = json_decode((string)file_get_contents($galleryFile), true);
    if (is_array($galleryData)) {
        $initialCount = count($galleryData);
        $updatedData = array_values(array_filter($galleryData, function ($item) use ($targetFile) {
            if (is_string($item)) {
                return $item !== $targetFile;
            }
            if (is_array($item)) {
                $url = $item['url'] ?? $item['src'] ?? '';
                $path = $item['path'] ?? '';
                return $url !== $targetFile && $path !== $targetFile;
            }
            return true;
        }));

        if (count($updatedData) !== $initialCount) {
            file_put_contents($galleryFile, json_encode($updatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
        }
    }
}

// Purge LiteSpeed edge cache
header("X-LiteSpeed-Purge: *");

echo json_encode(['status' => 'success', 'deleted' => $deletedFromStorage]);
