<?php
declare(strict_types=1);

// Wymuszenie uruchomienia z CLI (zabezpieczenie przed wywołaniem z przeglądarki)
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Skrypt diagnostyczny dostępny tylko z poziomu CLI.\n");
}

$baseDir = dirname(__DIR__);
$secretsPath = $baseDir . '/config/secrets.php';
$clientPath = $baseDir . '/api/R2Client.php';

echo "Rozpoczynam diagnostykę połączenia z Cloudflare R2...\n";
echo "=====================================================\n";

if (!file_exists($secretsPath)) {
    die("BŁĄD: Brak pliku z poświadczeniami ({$secretsPath}).\nUtwórz go na podstawie config/secrets.example.php i uzupełnij klucze API.\n");
}
require_once $secretsPath;

if (!file_exists($clientPath)) {
    die("BŁĄD: Brak pliku klienta R2 ({$clientPath}).\n");
}
require_once $clientPath;

use Raricart\Api\R2Client;

// Weryfikacja stałych
$requiredConstants = ['R2_ACCOUNT_ID', 'R2_ACCESS_KEY_ID', 'R2_SECRET_ACCESS_KEY', 'R2_BUCKET_NAME'];
foreach ($requiredConstants as $const) {
    if (!defined($const) || empty(constant($const))) {
        die("BŁĄD: Brak zdefiniowanej stałej lub pusta wartość: {$const}\n");
    }
}

echo "[OK] Poświadczenia załadowane.\n";

$client = new R2Client(R2_ACCOUNT_ID, R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET_NAME);

// Generowanie testowego ładunku
$testContent = "Raricart R2 Connection Test - " . date('Y-m-d H:i:s');
$testKey = 'system/diagnostic-test.txt';

echo "[TRWA] Próba zapisu pliku '{$testKey}' do bucketa '" . R2_BUCKET_NAME . "'...\n";

$startTime = microtime(true);
$result = $client->putObject($testKey, $testContent, 'text/plain');
$duration = round((microtime(true) - $startTime) * 1000);

if ($result) {
    echo "[SUKCES] Plik zapisany pomyślnie. Czas odpowiedzi API: {$duration}ms.\n";
    echo "Infrastruktura R2 (API) jest w pełni operacyjna. Oczekuj na propagację DNS domeny.\n";
} else {
    echo "[BŁĄD] Zapis nie powiódł się. Sprawdź poprawność kluczy API, uprawnienia tokena (Read & Write) i Account ID.\n";
    if (method_exists($client, 'getLastError') && $client->getLastError()) {
        echo "Szczegóły: " . $client->getLastError() . "\n";
    }
}
echo "=====================================================\n";
