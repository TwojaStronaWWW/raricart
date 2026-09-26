<?php
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Skrypt dostępny tylko z poziomu CLI.\n");
}

$baseDir = dirname(__DIR__);
$secretsPath = $baseDir . '/config/secrets.php';

if (!file_exists($secretsPath)) {
    die("BŁĄD: Brak pliku konfiguracyjnego {$secretsPath}.\n");
}
require_once $secretsPath;

$apiToken = defined('CF_API_TOKEN') ? trim((string)CF_API_TOKEN) : '';
$globalKey = defined('CF_GLOBAL_API_KEY') ? trim((string)CF_GLOBAL_API_KEY) : '';
$cfEmail = defined('CF_EMAIL') ? trim((string)CF_EMAIL) : '';

if ($apiToken === '' && ($globalKey === '' || $cfEmail === '')) {
    die("BŁĄD: Brak poświadczeń w config/secrets.php.\n");
}

function cf_api(string $endpoint, string $method = 'GET', ?array $data = null): array
{
    global $apiToken, $globalKey, $cfEmail;

    $url = "https://api.cloudflare.com/client/v4/" . ltrim($endpoint, '/');
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

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $raw = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno !== 0) {
        return ['success' => false, 'error' => "cURL error ({$errno}): {$err}"];
    }

    $decoded = json_decode((string)$raw, true);
    if (!is_array($decoded)) {
        return ['success' => false, 'error' => "Błędna odpowiedź JSON (HTTP {$code})"];
    }

    return $decoded;
}

echo "\n";
echo "=====================================================================\n";
echo "       CLOUDFLARE SECURITY HARDENING - RARICART ECOSYSTEM            \n";
echo "=====================================================================\n";

$zonesRes = cf_api('zones?per_page=50');
if (empty($zonesRes['success'])) {
    die("BŁĄD API: Nie udało się pobrać listy stref.\n");
}

$zones = $zonesRes['result'] ?? [];
echo "Liczba stref do zabezpieczenia: " . count($zones) . "\n\n";

// Docelowe, optymalne ustawienia bezpieczeństwa
$targetSettings = [
    'always_use_https'         => ['value' => 'on',      'desc' => 'Wymuszenie HTTPS (przekierowanie 301)'],
    'ssl'                      => ['value' => 'strict',  'desc' => 'Tryb SSL/TLS: Full (Strict)'],
    'min_tls_version'          => ['value' => '1.2',     'desc' => 'Minimalna wersja TLS: 1.2'],
    'tls_1_3'                  => ['value' => 'on',      'desc' => 'Obsługa nowoczesnego protokołu TLS 1.3'],
    'automatic_https_rewrites' => ['value' => 'on',      'desc' => 'Automatyczne przepisywanie mixed content na HTTPS'],
    'browser_check'            => ['value' => 'on',      'desc' => 'Browser Integrity Check (Ochrona przed botami)'],
    'security_level'           => ['value' => 'medium',  'desc' => 'Poziom bezpieczeństwa: Medium'],
    'brotli'                   => ['value' => 'on',      'desc' => 'Kompresja Brotli (prędkość i oszczędność transferu)'],
    'early_hints'              => ['value' => 'on',      'desc' => '103 Early Hints (przyspieszenie ładowania stron)'],
    'ip_geolocation'           => ['value' => 'on',      'desc' => 'Przekazywanie nagłówka z krajem użytkownika (CF-IPCountry)'],
];

foreach ($zones as $idx => $zone) {
    $num = $idx + 1;
    $zoneId = $zone['id'];
    $zoneName = $zone['name'];

    echo "---------------------------------------------------------------------\n";
    echo " [$num/" . count($zones) . "] Zabezpieczanie domeny: {$zoneName} ({$zoneId})\n";
    echo "---------------------------------------------------------------------\n";

    foreach ($targetSettings as $settingKey => $config) {
        $desiredValue = $config['value'];
        $desc = $config['desc'];

        // Pobierz aktualną wartość
        $currentRes = cf_api("zones/{$zoneId}/settings/{$settingKey}");
        $currentValue = $currentRes['result']['value'] ?? null;

        if ($currentValue === $desiredValue) {
            echo "  [JUŻ AKTYWNE]  {$desc} (Wartość: '{$currentValue}')\n";
            continue;
        }

        // Zastosuj nowe ustawienie
        $updateRes = cf_api("zones/{$zoneId}/settings/{$settingKey}", 'PATCH', ['value' => $desiredValue]);
        if (!empty($updateRes['success'])) {
            $updatedValue = $updateRes['result']['value'] ?? $desiredValue;
            echo "  [ZAKTUALIZOWANO] {$desc} ('{$currentValue}' -> '{$updatedValue}')\n";
        } else {
            $err = $updateRes['errors'][0]['message'] ?? 'Nieznany błąd';
            echo "  [BŁĄD]          {$desc}: {$err}\n";
        }
    }

    echo "\n";
}

echo "=====================================================================\n";
echo "Proces zabezpieczania zakończony pomyślnie dla wszystkich domen!\n";
