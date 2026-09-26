<?php
require_once 'auth.php';
check_login();

$file = __DIR__ . '/leady.csv';

// Jeśli plik lokalny nie istnieje lub jest pusty, spróbuj pobrać go z prywatnego bucketa R2
if (!file_exists($file) || filesize($file) < 10) {
    require_once __DIR__ . '/../api/PrivateStorageService.php';
    $privateStorage = new \Raricart\Api\PrivateStorageService();
    $privateStorage->restoreLeadsIfMissing($file);
}

if (file_exists($file) && filesize($file) > 0) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="klienci_raricart_' . date('Y-m-d') . '.csv"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    echo "Brak zarejestrowanych leadów w bazie (lokalnie i w prywatnym R2).";
}
?>
