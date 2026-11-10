<?php
declare(strict_types=1);

// --- KONFIGURACJA ---
$toEmail = "kontakt@raricart.pl";
$subjectPrefix = "[Formularz Raricart]";
const MAX_CONTACT_PAYLOAD_BYTES = 65536;
// ---------------------

header("Content-Type: application/json; charset=UTF-8");

// CORS: Tylko domeny raricart.pl (zakaz wildcardu)
$allowedOrigins = ['https://raricart.pl', 'https://www.raricart.pl', 'https://test.raricart.pl'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Vary: Origin");
} else {
    header("Access-Control-Allow-Origin: https://raricart.pl");
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$requestMethod = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($requestMethod === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($requestMethod !== 'POST' && PHP_SAPI !== 'cli') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Niedozwolona metoda HTTP."]);
    exit;
}

// Wczesne zwolnienie sesji PHP przed operacjami I/O i cURL ([REF-05])
if (session_status() === PHP_SESSION_ACTIVE) {
    @session_write_close();
}

// [REF-05] Zabezpieczenie OWASP Fetch Metadata (Sec-Fetch-Site) przed żądaniami Cross-Site
if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && strtolower(trim((string)$_SERVER['HTTP_SEC_FETCH_SITE'])) === 'cross-site') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Niedozwolone żądanie międzywitrynowe (Cross-Site Request Blocked)."]);
    exit;
}

require_once __DIR__ . '/rate-limit.php';
require_once __DIR__ . '/leads-store.php';

$ip = get_client_ip();
$maskedIp = mask_ip($ip);

// Sprawdzenie 15-minutowego IP Penalty Box przed parsowaniem treści
if (is_ip_banned($ip)) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Dostęp tymczasowo zablokowany ze względów bezpieczeństwa (15 min)."]);
    exit;
}

// Twardy limit rozmiaru wejścia 64 KB (65536 B -> HTTP 413 Payload Too Large)
$contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($contentLength > MAX_CONTACT_PAYLOAD_BYTES) {
    http_response_code(413);
    echo json_encode(["status" => "error", "message" => "Przekroczono maksymalny rozmiar zapytania (64 KB)."]);
    exit;
}

$input = file_get_contents("php://input", false, null, 0, MAX_CONTACT_PAYLOAD_BYTES + 1);
if (($input === false || $input === '') && PHP_SAPI === 'cli') {
    $input = (string)stream_get_contents(STDIN, MAX_CONTACT_PAYLOAD_BYTES + 1);
}
if (is_string($input) && strlen($input) > MAX_CONTACT_PAYLOAD_BYTES) {
    http_response_code(413);
    echo json_encode(["status" => "error", "message" => "Przekroczono maksymalny rozmiar zapytania (64 KB)."]);
    exit;
}

$data = json_decode((string)$input, true, 16);

/**
 * Filtr WAF Warstwy 7 wykrywający próby XSS, SQLi, Path Traversal / Wrapper RCE oraz Null-Byte.
 */
function contains_waf_l7_attack(mixed $value): bool {
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            if (contains_waf_l7_attack((string)$k) || contains_waf_l7_attack($v)) {
                return true;
            }
        }
        return false;
    }
    if (!is_string($value) || $value === '') {
        return false;
    }
    if (strpos($value, "\0") !== false) {
        return true;
    }
    $patterns = [
        '/<\s*script\b/i',
        '/\b(javascript|vbscript)\s*:/i',
        '/\bon(error|load|click|mouseover|focus|toggle|animationstart)\s*=/i',
        '/<\s*(iframe|object|embed)\b/i',
        '/\b(union\s+(all\s+)?select|drop\s+table|information_schema|sleep\s*\(\s*\d+\s*\)|benchmark\s*\()\b/i',
        '/(\.\.\/|\.\.\\\\|\/etc\/passwd|\/proc\/self|(php|phar|expect|file):\/\/|<\?(php|=))/i',
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $value) === 1) {
            return true;
        }
    }
    return false;
}

if (is_array($data) && contains_waf_l7_attack($data)) {
    ban_client_ip($ip, 900, 'waf_l7_contact');
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Zablokowano niebezpieczną zawartość formularza (WAF Penalty Box 15 min)."]);
    exit;
}

// --- DRAFTS DIR ---
$draftsDir = getenv('RARICART_TEST_DRAFTS_DIR') ?: (__DIR__ . '/drafts');
if (!is_dir($draftsDir)) {
    @mkdir($draftsDir, 0750, true);
    @file_put_contents(
        $draftsDir . '/.htaccess',
        "Options -Indexes\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order allow,deny\n    Deny from all\n</IfModule>\n"
    );
}

$csvTargetFile = getenv('RARICART_TEST_CSV_FILE') ?: (__DIR__ . '/../admin/leady.csv');

// --- POOR MAN'S CRON TRIGGER (posiada własny lock 600s) ---
if (is_array($data) && ($data['action'] ?? '') === 'ping') {
    processDraftQueue($draftsDir, $toEmail, $csvTargetFile);
    echo json_encode(["status" => "pong"]);
    exit;
}

// Anti-Spam (Honeypot)
if (is_array($data) && !empty($data['website_check'])) {
    echo json_encode(["status" => "success", "message" => "Wiadomość została wysłana!"]);
    exit;
}

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Brak danych."]);
    exit;
}

$isPartial = !empty($data['is_partial']);
$isAbandoned = !empty($data['is_abandoned']);

// Rozdzielony Rate Limiting: autosave szkiców (is_partial) ma osobną pulę i nigdy nie blokuje finalnego submitu!
if ($isPartial) {
    if (!rate_limit('raricart_draft_' . $ip, 60, 600)) {
        echo json_encode(["status" => "success", "message" => "Draft throttled"]);
        exit;
    }
} else {
    if (!rate_limit('raricart_contact_' . $ip, 15, 600)) {
        http_response_code(429);
        echo json_encode(["status" => "error", "message" => "Zbyt wiele zapytań. Spróbuj ponownie za kilka minut."]);
        exit;
    }
}

/**
 * Czyści jednowierszowe pole tekstowe z CRLF / znaków sterujących i tagów HTML.
 */
function clean_single_line_field(mixed $raw, int $maxLen = 200): string {
    $str = str_replace(["\r", "\n", "\0"], ' ', strip_tags((string)$raw));
    $str = trim((string)preg_replace('/[ \t]+/', ' ', $str));
    if (strlen($str) > $maxLen) {
        $str = substr($str, 0, $maxLen);
    }
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Sanityzacja (w tym naprawa CRLF w telefonie bez \s!)
$name = clean_single_line_field($data['name'] ?? '', 120);
$rawEmail = str_replace(["\r", "\n", "\0", " "], '', (string)($data['email'] ?? ''));
$email = substr((string)filter_var($rawEmail, FILTER_SANITIZE_EMAIL), 0, 160);
$phone = substr(trim((string)preg_replace('/[^0-9+\- ()]/', '', strip_tags((string)($data['phone'] ?? '')))), 0, 32);
$date = clean_single_line_field($data['date'] ?? '', 64);
$location = clean_single_line_field($data['location'] ?? '', 160);
$guests = clean_single_line_field($data['guests'] ?? '', 32);
$budget = clean_single_line_field($data['budget'] ?? '', 32);
$event_type = clean_single_line_field($data['event_type'] ?? '', 80);
$stations = clean_single_line_field($data['stations'] ?? '', 250);
$contact_hours = clean_single_line_field($data['contact_hours'] ?? '', 80);
$rawMessage = str_replace("\0", '', strip_tags((string)($data['message'] ?? '')));
if (strlen($rawMessage) > 4000) {
    $rawMessage = substr($rawMessage, 0, 4000);
}
$message = htmlspecialchars(trim($rawMessage), ENT_QUOTES, 'UTF-8');

// Walidacja
if (!$isPartial && (empty($name) || empty($email))) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Wypełnij wymagane pola."]);
    exit;
}

// Twarda walidacja email (backend MUSI walidować niezależnie od frontendu)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Nieprawidłowy adres email."]);
    exit;
}

// Jeśli partial, ale brak kontaktu, odrzuć po cichu
if ($isPartial && empty($email) && empty($phone)) {
    echo json_encode(["status" => "success", "message" => "Ignored empty draft"]);
    exit;
}

// --- HOT LEAD SCORING ---
$isHot = false;
if (!$isPartial) {
    $budgetNum = (int)preg_replace('/[^0-9]/', '', $budget);
    $guestsNum = (int)$guests;
    if ($budgetNum >= 5000 || $guestsNum >= 100) {
        $isHot = true;
    }
}

$hotLabel = $isHot ? '🔥 HOT ' : '';
$cleanNameForHeader = str_replace(["\r", "\n", "\0"], '', html_entity_decode($name, ENT_QUOTES, 'UTF-8'));
$rawSubject = $isPartial
    ? "⚠️ SZKIC (Porzucony): {$cleanNameForHeader}"
    : "{$hotLabel}{$subjectPrefix} Nowe zapytanie od: {$cleanNameForHeader}";
$emailSubject = '=?UTF-8?B?' . base64_encode($rawSubject) . '?=';

$emailBody = ($isPartial ? "--- TO JEST NIEUKOŃCZONY SZKIC FORMULARZA ---\n\n" : "Nowe zapytanie ze strony:\n\n") .
             "👤 Imię: $name\n" .
             "📧 Email: $email\n" .
             "📞 Tel: $phone\n" .
             "🕐 Preferowane godziny kontaktu: $contact_hours\n\n" .
             "📅 Data wydarzenia: $date\n" .
             "📍 Lokalizacja: $location\n" .
             "👥 Liczba gości: $guests\n" .
             "💰 Budżet: $budget PLN\n" .
             "🎉 Rodzaj wydarzenia: $event_type\n" .
             "🔥 Interesujące stacje: $stations\n" .
             "🛡️ IP (RODO): $maskedIp\n\n" .
             "💬 Wiadomość:\n$message";

// --- LOGIKA SKŁADOWANIA SZKICÓW (DRAFT) ---
$emailNorm = strtolower(trim($email));
$phoneNorm = preg_replace('/\D+/', '', $phone) ?: '';
$emailHash = $emailNorm !== '' ? md5($emailNorm) : null;
$phoneHash = $phoneNorm !== '' ? md5($phoneNorm) : null;
$userId = $emailHash ?: ($phoneHash ?: ($email ? md5($email) : ($phone ? md5($phone) : null)));

$candidateHashes = array_values(array_unique(array_filter([
    $userId,
    $emailHash,
    $phoneHash,
    $email ? md5($email) : null,
    $phone ? md5($phone) : null,
])));

if ($isPartial && $userId) {
    foreach ($candidateHashes as $h) {
        $submittedLock = $draftsDir . '/submitted_' . $h . '.lock';
        if (file_exists($submittedLock) && (time() - (int)@filemtime($submittedLock) < 900)) {
            echo json_encode(["status" => "success", "message" => "Draft ignored (already submitted)."]);
            exit;
        }
    }

    $draftFile = $draftsDir . '/draft_' . $userId . '.json';

    $currentScore = 0;
    foreach ([$name, $email, $phone, $date, $location, $guests, $event_type, $stations, $contact_hours] as $field) {
        if (!empty(trim($field))) {
            $currentScore++;
        }
    }
    if (strlen(trim($message)) >= 3) {
        $currentScore++;
    }
    if (!empty(trim($budget)) && $budget !== '0' && $budget !== '-') {
        $currentScore++;
    }

    $shouldSave = true;

    foreach ($candidateHashes as $h) {
        $candidateFile = $draftsDir . '/draft_' . $h . '.json';
        if (file_exists($candidateFile)) {
            $savedData = json_decode((string)@file_get_contents($candidateFile), true);
            if (($savedData['score'] ?? 0) > $currentScore) {
                $shouldSave = false;
            }
            if ($candidateFile !== $draftFile) {
                @unlink($candidateFile);
            }
        }
    }

    if ($shouldSave) {
        $sanitizedDraftFields = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'contact_hours' => $contact_hours,
            'date' => $date,
            'location' => $location,
            'guests' => $guests,
            'budget' => $budget,
            'event_type' => $event_type,
            'stations' => $stations,
            'message' => $message,
            'ip_masked' => $maskedIp,
        ];
        $payloadToSave = [
            'data' => $sanitizedDraftFields,
            'score' => $currentScore,
            'timestamp' => time(),
            'formattedBody' => $emailBody,
            'subject' => $emailSubject
        ];
        file_put_contents($draftFile, json_encode($payloadToSave, JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    echo json_encode(["status" => "success", "message" => "Draft saved/updated."]);
} else {
    // --- NORMALNA WYSYŁKA (FINAL SUBMIT) ---
    @session_write_close();

    // 1. Zablokuj późniejsze nadpisanie przez spóźniony autosave i usuń szkice tego klienta
    foreach ($candidateHashes as $h) {
        $submittedLock = $draftsDir . '/submitted_' . $h . '.lock';
        @file_put_contents($submittedLock, (string)time(), LOCK_EX);
        $df = $draftsDir . '/draft_' . $h . '.json';
        if (file_exists($df)) {
            @unlink($df);
        }
    }
    cleanupMatchingDraftsAndDigest($draftsDir, $emailNorm, $phoneNorm);

    // 2. Zapisz od razu do CSV z blokadą LOCK_EX, sanitize_csv_cell() (ochrona CSV Formula Injection) i mask_ip() (RODO)
    $csvFile = $csvTargetFile;
    if ($fp = @fopen($csvFile, 'a')) {
        if (flock($fp, LOCK_EX)) {
            clearstatcache(true, $csvFile);
            $stat = fstat($fp);
            $isNew = ($stat === false || ($stat['size'] ?? 0) === 0);
            if ($isNew) {
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM dla Excela
                fputcsv($fp, [
                    'Data zgłoszenia', 'Status', 'Źródło', 'Imię', 'Email', 'Telefon',
                    'Godz. kontaktu', 'Data wydarzenia', 'Lokalizacja', 'Goście',
                    'Budżet', 'Typ', 'Stacje', 'Wiadomość', 'IP (RODO)'
                ]);
            }

            $csvRow = array_map(static function ($cell): string {
                return sanitize_csv_cell((string)$cell);
            }, [
                date('Y-m-d H:i:s'),
                $isHot ? '🔥 HOT' : '✅ WYSŁANY',
                'Formularz',
                html_entity_decode($name, ENT_QUOTES, 'UTF-8'),
                $email,
                $phone,
                html_entity_decode($contact_hours, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($date, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($location, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($guests, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($budget, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($event_type, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($stations, ENT_QUOTES, 'UTF-8'),
                html_entity_decode($message, ENT_QUOTES, 'UTF-8'),
                $maskedIp
            ]);

            fputcsv($fp, $csvRow);
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        // Cichy, bezpieczny backup do prywatnego bucketa Cloudflare R2
        if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1') {
            try {
                require_once __DIR__ . '/PrivateStorageService.php';
                $privateStorage = new \Raricart\Api\PrivateStorageService();
                $privateStorage->backupLeads($csvFile);
            } catch (\Throwable $e) {
                error_log("Błąd backupu leadów do R2: " . $e->getMessage());
            }
        }
    }

    // 3. Rejestracja zgłoszenia w centralnym systemie ticketowym osTicket (wyłącznie HTTPS!) + Ntfy 1-Klik
    $osTicketApiKey = get_env_secret('OSTICKET_API_KEY');
    if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1' && !empty($osTicketApiKey) && function_exists('curl_init')) {
        $ticketPayload = [
            'name' => $cleanNameForHeader ?: 'Klient Raricart',
            'email' => $email ?: 'kontakt@raricart.pl',
            'phone' => $phone,
            'subject' => $rawSubject,
            'message' => html_entity_decode($emailBody, ENT_QUOTES, 'UTF-8'),
            'ip' => $maskedIp,
            'source' => 'Web',
            'autorespond' => false,
            'send_email' => false,
        ];
        $ch = curl_init('https://hd.twojastronawww.pl/ticket_hook.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-Key: ' . str_replace(["\r", "\n", "\0"], '', $osTicketApiKey),
            'Content-Type: application/json',
            'Expect:'
        ]);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Raricart-HelpdeskClient/2.1');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ticketPayload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
        if (defined('CURLPROTO_HTTPS')) {
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS);
        }
        @curl_exec($ch);
        curl_close($ch);
    }

    $ntfyTopic = preg_replace('/[^a-zA-Z0-9_-]/', '', get_env_secret('NTFY_TOPIC')) ?: '';
    if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1' && $ntfyTopic !== '' && function_exists('curl_init')) {
        $ntfyBaseUrl = rtrim(get_env_secret('NTFY_BASE_URL', 'https://ntfy.sh'), '/');
        if (stripos($ntfyBaseUrl, 'https://') !== 0) {
            $ntfyBaseUrl = 'https://ntfy.sh';
        }
        $ntfyToken = str_replace(["\r", "\n", "\0"], '', get_env_secret('NTFY_TOKEN'));

        $ntfyActions = [];
        if (!empty($phone)) {
            $digits = preg_replace('/\D+/', '', $phone) ?: '';
            $dial = (strlen($digits) === 9) ? '+48' . $digits : '+' . $digits;
            $ntfyActions[] = "view, Zadzwon ({$phone}), tel:{$dial}";
        }
        if (!empty($email)) {
            $ntfyActions[] = "view, Napisz e-mail ({$email}), mailto:{$email}";
        }
        $ntfyActions[] = "view, Otworz Helpdesk, https://hd.twojastronawww.pl/scp/";

        $ntfyHeaders = [
            'Title: ' . ($isHot ? 'HOT LEAD (Raricart) - ' : 'Formularz (Raricart) - ') . $cleanNameForHeader,
            'Priority: high',
            'Tags: incoming_envelope,pancakes',
            'Actions: ' . implode('; ', array_slice($ntfyActions, 0, 3))
        ];
        if ($ntfyToken !== '') {
            $ntfyHeaders[] = 'Authorization: Bearer ' . $ntfyToken;
        }

        $chNtfy = curl_init("{$ntfyBaseUrl}/{$ntfyTopic}");
        curl_setopt_array($chNtfy, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => html_entity_decode($emailBody, ENT_QUOTES, 'UTF-8'),
            CURLOPT_HTTPHEADER => $ntfyHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0
        ]);
        if (defined('CURLPROTO_HTTPS')) {
            curl_setopt($chNtfy, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
            curl_setopt($chNtfy, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS);
        }
        @curl_exec($chNtfy);
        curl_close($chNtfy);
    }

    // 4. Wyślij maila z nagłówkami MIME RFC 2045 + RFC 2047 Subject
    $cleanReplyTo = str_replace(["\r", "\n", "\0"], '', $email);
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: Formularz WWW <kontakt@raricart.pl>\r\n";
    if ($cleanReplyTo !== '') {
        $headers .= "Reply-To: $cleanReplyTo\r\n";
    }
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";

    if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1') {
        $mailSent = @mail($toEmail, $emailSubject, html_entity_decode($emailBody, ENT_QUOTES, 'UTF-8'), $headers);
        if (!$mailSent) {
            $fallbackHeaders = "MIME-Version: 1.0\r\n";
            $fallbackHeaders .= "From: Formularz WWW <kontakt@raricart.pl>\r\n";
            $fallbackHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $fallbackHeaders .= "Content-Transfer-Encoding: 8bit\r\n";
            @mail($toEmail, $emailSubject, html_entity_decode($emailBody, ENT_QUOTES, 'UTF-8'), $fallbackHeaders);
        }
    }

    echo json_encode(["status" => "success", "message" => "Wiadomość wysłana!"]);
}

processDraftQueue($draftsDir, $toEmail, $csvTargetFile);

// --- FUNKCJA CRON: DAILY DIGEST ---
function processDraftQueue(string $draftsDir, string $toEmail, ?string $csvTargetFile = null): void {
    if ($draftsDir === '' || !is_dir($draftsDir)) {
        return;
    }
    if (!$csvTargetFile) {
        $csvTargetFile = __DIR__ . '/../admin/leady.csv';
    }

    $lockFile = $draftsDir . '/last_run.txt';
    $lastRun = file_exists($lockFile) ? (int)file_get_contents($lockFile) : 0;
    $r2SyncLock = $draftsDir . '/r2_synced.lock';
    $needsR2Sync = !file_exists($r2SyncLock) || (is_file($csvTargetFile) && (int)@filemtime($csvTargetFile) > (int)@filemtime($r2SyncLock));

    if ($needsR2Sync || (time() - $lastRun > 600)) {
        file_put_contents($lockFile, (string)time(), LOCK_EX);

        $files = glob($draftsDir . '/draft_*.json');
        if ($files) {
            foreach ($files as $file) {
                $mtime = filemtime($file);
                if (!$mtime || (time() - $mtime < 600)) {
                    continue;
                }

                $content = json_decode((string)@file_get_contents($file), true);
                if (!is_array($content)) {
                    @unlink($file);
                    continue;
                }

                $d = is_array($content['data'] ?? null) ? $content['data'] : [];
                saveDraftToCsv($d, (int)$mtime, $csvTargetFile);
                appendDraftToDigest($draftsDir, (string)($content['formattedBody'] ?? ''));

                @unlink($file);
            }
        }

        $cleanupFiles = array_merge(
            glob($draftsDir . '/rate_*.json') ?: [],
            glob($draftsDir . '/submitted_*.lock') ?: []
        );
        foreach ($cleanupFiles as $rf) {
            if (file_exists($rf) && (time() - (int)@filemtime($rf) > 3600)) {
                @unlink($rf);
            }
        }
    }

    if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1' && is_file($csvTargetFile) && filesize($csvTargetFile) > 0) {
        if (!file_exists($r2SyncLock) || (int)@filemtime($csvTargetFile) > (int)@filemtime($r2SyncLock)) {
            try {
                require_once __DIR__ . '/PrivateStorageService.php';
                $privateStorage = new \Raricart\Api\PrivateStorageService();
                if ($privateStorage->backupLeads($csvTargetFile)) {
                    $digestBufferFile = $draftsDir . '/digest_buffer.txt';
                    if (is_file($digestBufferFile) && filesize($digestBufferFile) > 0) {
                        $privateStorage->putPrivate('leads/digest_buffer_latest.txt', (string)@file_get_contents($digestBufferFile), 'text/plain; charset=UTF-8');
                    }
                    file_put_contents($r2SyncLock, (string)time(), LOCK_EX);
                }
            } catch (\Throwable $e) {
                error_log("Błąd synchronizacji R2 w cronie: " . $e->getMessage());
            }
        }
    }

    $digestLockFile = $draftsDir . '/last_digest.txt';
    $lastDigest = file_exists($digestLockFile) ? (int)file_get_contents($digestLockFile) : 0;

    if (time() - $lastDigest > 86400) {
        $digestBufferFile = $draftsDir . '/digest_buffer.txt';
        if (file_exists($digestBufferFile)) {
            $bufferContent = (string)@file_get_contents($digestBufferFile);
            if (!empty(trim($bufferContent))) {
                $count = substr_count($bufferContent, "=== LEAD START ===");

                $rawDigestSubject = "⚠️ [Raricart] Dziś porzucono {$count} formularzy";
                $digestSubject = '=?UTF-8?B?' . base64_encode($rawDigestSubject) . '?=';
                $digestBody = "=== DAILY DIGEST: PORZUCONE FORMULARZE ===\n";
                $digestBody .= "Liczba: {$count}\n";
                $digestBody .= "Data: " . date('Y-m-d H:i') . "\n";
                $digestBody .= str_repeat('=', 50) . "\n\n";
                $digestBody .= str_replace("=== LEAD START ===\n", "--- Lead ---\n", $bufferContent);

                $cronHeaders = "MIME-Version: 1.0\r\n";
                $cronHeaders .= "From: Formularz WWW (Digest) <kontakt@raricart.pl>\r\n";
                $cronHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $cronHeaders .= "Content-Transfer-Encoding: 8bit\r\n";

                $digestSent = (getenv('RARICART_TEST_DISABLE_NETWORK') === '1')
                    ? true
                    : @mail($toEmail, $digestSubject, $digestBody, $cronHeaders);
                if ($digestSent) {
                    @unlink($digestBufferFile);
                    file_put_contents($digestLockFile, (string)time(), LOCK_EX);
                }
            }
        } else {
            file_put_contents($digestLockFile, (string)time(), LOCK_EX);
        }
    }
}

function cleanupMatchingDraftsAndDigest(string $draftsDir, string $emailNorm, string $phoneNorm): void {
    if ($draftsDir === '' || !is_dir($draftsDir)) {
        return;
    }

    $draftFiles = glob($draftsDir . '/draft_*.json') ?: [];
    foreach ($draftFiles as $df) {
        $content = json_decode((string)@file_get_contents($df), true);
        if (!is_array($content)) {
            continue;
        }
        $d = $content['data'] ?? [];
        $dEmail = strtolower(trim((string)($d['email'] ?? '')));
        $dPhone = preg_replace('/\D+/', '', (string)($d['phone'] ?? '')) ?: '';
        if (($emailNorm !== '' && $dEmail === $emailNorm) || ($phoneNorm !== '' && strlen($phoneNorm) >= 7 && $dPhone === $phoneNorm)) {
            @unlink($df);
        }
    }

    $digestBufferFile = $draftsDir . '/digest_buffer.txt';
    if (!file_exists($digestBufferFile)) {
        return;
    }

    $bufferContent = (string)@file_get_contents($digestBufferFile);
    if (trim($bufferContent) === '') {
        return;
    }

    $parts = explode("=== LEAD START ===\n", $bufferContent);
    $kept = [];
    $modified = false;
    foreach ($parts as $part) {
        if (trim($part) === '') {
            continue;
        }
        $partLower = strtolower($part);
        $matchesEmail = ($emailNorm !== '' && strpos($partLower, $emailNorm) !== false);
        $matchesPhone = ($phoneNorm !== '' && strlen($phoneNorm) >= 7 && strpos(preg_replace('/\D+/', '', $part) ?: '', $phoneNorm) !== false);
        if ($matchesEmail || $matchesPhone) {
            $modified = true;
            continue;
        }
        $kept[] = "=== LEAD START ===\n" . ltrim($part);
    }

    if ($modified) {
        if (empty($kept)) {
            @unlink($digestBufferFile);
        } else {
            file_put_contents($digestBufferFile, implode("", $kept), LOCK_EX);
        }
    }
}

/**
 * Zapisuje porzucony szkic do pliku CSV z ochroną CSV Formula Injection (sanitize_csv_cell) i anonimizacją IP (mask_ip).
 */
function saveDraftToCsv(array $d, int $timestamp, ?string $csvTargetFile = null): void {
    $csvFile = $csvTargetFile ?: (__DIR__ . '/../admin/leady.csv');

    if ($fp = @fopen($csvFile, 'a')) {
        if (flock($fp, LOCK_EX)) {
            clearstatcache(true, $csvFile);
            $stat = fstat($fp);
            $isNew = ($stat === false || ($stat['size'] ?? 0) === 0);
            if ($isNew) {
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM dla Excela
                fputcsv($fp, [
                    'Data zgłoszenia', 'Status', 'Źródło', 'Imię', 'Email', 'Telefon',
                    'Godz. kontaktu', 'Data wydarzenia', 'Lokalizacja', 'Goście',
                    'Budżet', 'Typ', 'Stacje', 'Wiadomość', 'IP (RODO)'
                ]);
            }

            $draftBudget = (int)preg_replace('/[^0-9]/', '', (string)($d['budget'] ?? ''));
            $draftGuests = (int)($d['guests'] ?? 0);
            $draftStatus = ($draftBudget >= 5000 || $draftGuests >= 100) ? '🔥 HOT (SZKIC)' : '⚠️ PORZUCONY';
            $cleanPhone = substr(trim((string)preg_replace('/[^0-9+\- ()]/', '', strip_tags((string)($d['phone'] ?? '')))), 0, 32);
            $rawIpOrMasked = (string)($d['ip_masked'] ?? $d['ip'] ?? '0.0.0.0');
            $draftMaskedIp = mask_ip($rawIpOrMasked);

            $draftRow = array_map(static function ($cell): string {
                return sanitize_csv_cell((string)$cell);
            }, [
                date('Y-m-d H:i:s', $timestamp),
                $draftStatus,
                'Autosave',
                html_entity_decode((string)($d['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
                (string)($d['email'] ?? ''),
                $cleanPhone,
                html_entity_decode((string)($d['contact_hours'] ?? ''), ENT_QUOTES, 'UTF-8'),
                html_entity_decode((string)($d['date'] ?? ''), ENT_QUOTES, 'UTF-8'),
                html_entity_decode((string)($d['location'] ?? ''), ENT_QUOTES, 'UTF-8'),
                html_entity_decode((string)($d['guests'] ?? ''), ENT_QUOTES, 'UTF-8'),
                html_entity_decode((string)($d['budget'] ?? ''), ENT_QUOTES, 'UTF-8'),
                html_entity_decode((string)($d['event_type'] ?? ''), ENT_QUOTES, 'UTF-8'),
                html_entity_decode((string)($d['stations'] ?? ''), ENT_QUOTES, 'UTF-8'),
                html_entity_decode((string)($d['message'] ?? ''), ENT_QUOTES, 'UTF-8'),
                $draftMaskedIp
            ]);

            fputcsv($fp, $draftRow);
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1') {
            try {
                require_once __DIR__ . '/PrivateStorageService.php';
                $privateStorage = new \Raricart\Api\PrivateStorageService();
                $privateStorage->backupLeads($csvFile);
            } catch (\Throwable $e) {
                error_log("Błąd backupu szkicu do R2: " . $e->getMessage());
            }
        }
    }
}

function appendDraftToDigest(string $draftsDir, string $body): void {
    if (empty(trim($body))) {
        return;
    }
    $digestBufferFile = $draftsDir . '/digest_buffer.txt';
    $entry = "=== LEAD START ===\n" . $body . "\n\n";
    file_put_contents($digestBufferFile, $entry, FILE_APPEND | LOCK_EX);
}
