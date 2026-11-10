<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_post_method(false);
check_login();
check_csrf(false);
@session_write_close();

require_once __DIR__ . '/../api/PrivateStorageService.php';

$file = __DIR__ . '/leady.csv';
$storage = new \Raricart\Api\PrivateStorageService();

$msg = 'Błąd synchronizacji';
if ($storage->isAvailable()) {
    if (file_exists($file) && filesize($file) > 10) {
        $ok = $storage->backupLeads($file);
        $msg = $ok
            ? 'Kopia zapasowa bazy leadów została pomyślnie zaktualizowana w prywatnym Cloudflare R2.'
            : 'Nie udało się wysłać kopii do R2.';
    } else {
        $ok = $storage->restoreLeadsIfMissing($file);
        $msg = $ok
            ? 'Pobrano najnowszą bazę leadów z prywatnego Cloudflare R2.'
            : 'Brak danych w prywatnym R2 do przywrócenia.';
    }
} else {
    $msg = 'Brak konfiguracji prywatnego magazynu R2.';
}

header('Location: dashboard.php?msg=' . urlencode($msg));
exit;
