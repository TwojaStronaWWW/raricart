<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
check_login();
@session_write_close();

$file = __DIR__ . '/leady.csv';

if (!file_exists($file) || filesize($file) < 10) {
    require_once __DIR__ . '/../api/PrivateStorageService.php';
    $privateStorage = new \Raricart\Api\PrivateStorageService();
    $privateStorage->restoreLeadsIfMissing($file);
}

if (file_exists($file) && filesize($file) > 0) {
    $fp = @fopen($file, 'rb');
    if ($fp && @flock($fp, LOCK_SH)) {
        clearstatcache(true, $file);
        $size = (int)filesize($file);
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="klienci_raricart_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Content-Length: ' . $size);
        fpassthru($fp);
        @flock($fp, LOCK_UN);
        fclose($fp);
        exit;
    }
    if ($fp) {
        fclose($fp);
    }
}

header('Content-Type: text/plain; charset=UTF-8');
echo "Brak zarejestrowanych leadów w bazie (lokalnie i w prywatnym R2).";
