<?php
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Skrypt audytu dostępny tylko z poziomu CLI.\n");
}

$baseDir = dirname(__DIR__);
$secretsPath = $baseDir . '/config/secrets.php';

echo "\n";
echo "=====================================================================\n";
echo "              RARICART - AUDYT INFRASTRUKTURY CLOUDFLARE             \n";
echo "=====================================================================\n";

if (!file_exists($secretsPath)) {
    die("BŁĄD: Brak pliku konfiguracyjnego {$secretsPath}.\nSkopiuj config/secrets.example.php i uzupełnij klucze API.\n");
}
require_once $secretsPath;

$apiToken = defined('CF_API_TOKEN') ? trim((string)CF_API_TOKEN) : '';
$globalKey = defined('CF_GLOBAL_API_KEY') ? trim((string)CF_GLOBAL_API_KEY) : '';
$cfEmail = defined('CF_EMAIL') ? trim((string)CF_EMAIL) : '';

if ($apiToken === '' && ($globalKey === '' || $cfEmail === '')) {
    die("BŁĄD: Wymagany jest CF_API_TOKEN (rekomendowany) lub para CF_GLOBAL_API_KEY + CF_EMAIL w config/secrets.php.\n");
}

function cf_api(string $endpoint, array $options = []): array
{
    global $apiToken, $globalKey, $cfEmail;

    $url = "https://api.cloudflare.com/client/v4/" . ltrim($endpoint, '/');
    $method = $options['method'] ?? 'GET';
    $payload = $options['data'] ?? null;

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    if ($apiToken !== '') {
        $headers[] = "Authorization: Bearer {$apiToken}";
    } else {
        $headers[] = "X-Auth-Key: {$globalKey}";
        $headers[] = "X-Auth-Email: {$cfEmail}";
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $raw = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno !== 0) {
        return ['success' => false, 'errors' => [['message' => "Błąd cURL ({$errno}): {$err}"]], 'http_code' => $code];
    }

    $decoded = json_decode((string)$raw, true);
    if (!is_array($decoded)) {
        return ['success' => false, 'errors' => [['message' => "Nieprawidłowa odpowiedź JSON (HTTP {$code})"]], 'raw' => $raw, 'http_code' => $code];
    }

    $decoded['http_code'] = $code;
    return $decoded;
}

echo "[1/4] Sprawdzanie poprawności poświadczeń API...\n";
$userRes = cf_api('user/tokens/verify');
if ($apiToken !== '') {
    if (!empty($userRes['success'])) {
        $status = $userRes['result']['status'] ?? 'unknown';
        echo "  [OK] API Token jest aktywny (Status: {$status}).\n";
    } else {
        echo "  [UWAGA] Weryfikacja tokena nie powiodła się lub używasz tokena o ograniczonym zasięgu.\n";
    }
} else {
    echo "  [INFO] Używasz Global API Key ($cfEmail).\n";
}

echo "\n[2/4] Pobieranie stref (domen) podpiętych do konta...\n";
$zonesRes = cf_api('zones?per_page=50');

if (empty($zonesRes['success'])) {
    $errMsg = $zonesRes['errors'][0]['message'] ?? 'Nieznany błąd';
    die("\nBŁĄD API CLOUDFLARE: {$errMsg} (HTTP {$zonesRes['http_code']})\nSprawdź uprawnienia tokena (Zone.Zone:Read).\n");
}

$zones = $zonesRes['result'] ?? [];
$zoneCount = count($zones);
echo "  Znaleziono stref: {$zoneCount}\n\n";

if ($zoneCount === 0) {
    echo "Brak przypisanych stref (domen) na tym koncie Cloudflare.\n";
    exit(0);
}

foreach ($zones as $idx => $zone) {
    $num = $idx + 1;
    $zoneId = $zone['id'];
    $zoneName = $zone['name'];
    $status = $zone['status'];
    $plan = $zone['plan']['name'] ?? 'Free';
    $nameServers = implode(', ', $zone['name_servers'] ?? []);
    $paused = !empty($zone['paused']) ? 'TAK (Ruch omija Cloudflare!)' : 'NIE (Aktywna ochrona)';

    echo "---------------------------------------------------------------------\n";
    echo " [$num] DOMENA: {$zoneName}\n";
    echo "---------------------------------------------------------------------\n";
    echo "  ID Strefy    : {$zoneId}\n";
    echo "  Status       : " . strtoupper($status) . ($status === 'active' ? " [OK]" : " [OCZEKUJE NA DELEGACJĘ DNS]") . "\n";
    echo "  Plan         : {$plan}\n";
    echo "  Cloudflare NS: {$nameServers}\n";
    echo "  Wstrzymana   : {$paused}\n";

    // Pobranie ustawień SSL
    $sslRes = cf_api("zones/{$zoneId}/settings/ssl");
    $sslMode = $sslRes['result']['value'] ?? 'nieznany';
    echo "  Tryb SSL/TLS : " . strtoupper($sslMode) . "\n";

    // Pobranie ustawień Always Use HTTPS
    $httpsRes = cf_api("zones/{$zoneId}/settings/always_use_https");
    $httpsVal = $httpsRes['result']['value'] ?? 'off';
    echo "  Wymuś HTTPS  : " . strtoupper($httpsVal) . "\n";

    // Pobranie rekordów DNS
    echo "\n  Rekordy DNS dla {$zoneName}:\n";
    $dnsRes = cf_api("zones/{$zoneId}/dns_records?per_page=100");
    if (!empty($dnsRes['success'])) {
        $records = $dnsRes['result'] ?? [];
        if (empty($records)) {
            echo "    (Brak skonfigurowanych rekordów DNS)\n";
        } else {
            printf("    %-6s | %-30s | %-6s | %s\n", "TYP", "NAZWA", "PROXY", "WARTOŚĆ");
            echo "    " . str_repeat("-", 70) . "\n";
            foreach ($records as $r) {
                $type = $r['type'] ?? '';
                $name = $r['name'] ?? '';
                $proxied = !empty($r['proxied']) ? 'TAK' : 'NIE';
                $content = $r['content'] ?? '';
                if (strlen($content) > 35) {
                    $content = substr($content, 0, 32) . '...';
                }
                printf("    %-6s | %-30s | %-6s | %s\n", $type, $name, $proxied, $content);
            }
        }
    } else {
        echo "    [BŁĄD] Nie udało się pobrać rekordów DNS: " . ($dnsRes['errors'][0]['message'] ?? 'Błąd') . "\n";
    }

    echo "\n";
}

echo "---------------------------------------------------------------------\n";
echo " AUDYT USŁUGI CLOUDFLARE R2 (OBJECT STORAGE)\n";
echo "---------------------------------------------------------------------\n";
$accountId = defined('CF_ACCOUNT_ID') ? (string)CF_ACCOUNT_ID : '';
if ($accountId !== '') {
    $r2Res = cf_api("accounts/{$accountId}/r2/buckets");
    if (!empty($r2Res['success'])) {
        $buckets = $r2Res['result']['buckets'] ?? [];
        echo "  Status R2    : WŁĄCZONE (Aktywne)\n";
        echo "  Liczba bucketów: " . count($buckets) . "\n";
        foreach ($buckets as $b) {
            echo "   * Bucket: " . ($b['name'] ?? 'nieznany') . " (Utworzono: " . ($b['creation_date'] ?? 'N/A') . ")\n";
        }
    } else {
        $msg = $r2Res['errors'][0]['message'] ?? 'Błąd dostępu';
        echo "  Status R2    : NIEAKTYWNE / WYMAGA AKTYWACJI\n";
        echo "  Komunikat    : {$msg}\n";
        echo "  Wskazówka    : Wejdź w Cloudflare Dashboard -> zakładka 'R2' w menu po lewej i kliknij 'Enable R2'.\n";
    }
} else {
    echo "  [INFO] Brak CF_ACCOUNT_ID w config/secrets.php - pomijam audyt R2.\n";
}

echo "\n=====================================================================\n";
echo "Audyt zakończony pomyślnie.\n";
