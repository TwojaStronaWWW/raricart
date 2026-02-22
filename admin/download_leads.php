<?php
require_once 'auth.php';
check_login();

$file = __DIR__ . '/leady.csv';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="klienci_raricart.csv"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    echo "Brak leadów do pobrania.";
}
?>
