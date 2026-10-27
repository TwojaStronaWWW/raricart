<?php
// contact.php

// --- KONFIGURACJA ---
$toEmail = "kontakt@raricart.pl"; // Poprawione: czysty adres w cudzysłowie
$subjectPrefix = "[Formularz Raricart]"; 
// ---------------------

header("Content-Type: application/json; charset=UTF-8");

// CORS: Tylko raricart.pl (nie wildcard)
$allowedOrigins = ['https://raricart.pl', 'https://www.raricart.pl', 'https://test.raricart.pl'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://raricart.pl");
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Wczesne zwolnienie sesji PHP (jeśli aktywna)
if (session_status() === PHP_SESSION_ACTIVE) {
    @session_write_close();
}

// [REF-05] Zabezpieczenie OWASP Fetch Metadata (Sec-Fetch-Site) przed żądaniami Cross-Site
if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && strtolower(trim((string)$_SERVER['HTTP_SEC_FETCH_SITE'])) === 'cross-site') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Niedozwolone żądanie międzywitrynowe (Cross-Site Request Blocked)."]);
    exit;
}

$input = file_get_contents("php://input");
if (($input === false || $input === '') && PHP_SAPI === 'cli') {
    $input = (string)stream_get_contents(STDIN);
}
$data = json_decode((string)$input, true);

require_once __DIR__ . '/rate-limit.php';
require_once __DIR__ . '/leads-store.php';

// --- DRAFTS DIR ---
$draftsDir = getenv('RARICART_TEST_DRAFTS_DIR') ?: (__DIR__ . '/drafts');
if (!is_dir($draftsDir)) {
    @mkdir($draftsDir, 0777, true);
    @file_put_contents($draftsDir . '/.htaccess', "Deny from all"); 
}

$csvTargetFile = getenv('RARICART_TEST_CSV_FILE') ?: (__DIR__ . '/../admin/leady.csv');

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// --- POOR MAN'S CRON TRIGGER (nie zużywa puli rate-limitera formularza; posiada własny lock 600s) ---
if (($data['action'] ?? '') === 'ping') {
    processDraftQueue($draftsDir, $toEmail, $csvTargetFile);
    echo json_encode(["status" => "pong"]);
    exit;
}

// Anti-Spam (Honeypot)
if (!empty($data['website_check'])) {
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

// Sanityzacja
$name = htmlspecialchars(strip_tags((string)($data['name'] ?? '')));
$email = filter_var((string)($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(strip_tags((string)($data['phone'] ?? '')));
$date = htmlspecialchars(strip_tags((string)($data['date'] ?? '')));
$location = htmlspecialchars(strip_tags((string)($data['location'] ?? '')));
$guests = htmlspecialchars(strip_tags((string)($data['guests'] ?? '')));
$budget = htmlspecialchars(strip_tags((string)($data['budget'] ?? '')));
$event_type = htmlspecialchars(strip_tags((string)($data['event_type'] ?? '')));
$stations = htmlspecialchars(strip_tags((string)($data['stations'] ?? '')));
$contact_hours = htmlspecialchars(strip_tags((string)($data['contact_hours'] ?? '')));
$message = htmlspecialchars(strip_tags((string)($data['message'] ?? '')));

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

// Jeśli partial, ale brak kontaktu, też odrzuć po cichu (żeby nie słać pustych)
if ($isPartial && empty($email) && empty($phone)) {
    // Nie traktujemy tego jako błąd 400, tylko success bez wysyłki (silent ignore)
    echo json_encode(["status" => "success", "message" => "Ignored empty draft"]);
    exit;
}
// --- HOT LEAD SCORING ---
$isHot = false;
if (!$isPartial) {
    // Pola guests i budget są teraz input (nie select) — parsujemy jako int
    $budgetNum = (int) preg_replace('/[^0-9]/', '', $budget);
    $guestsNum = (int) $guests;
    // Hot: budżet >= 5000 PLN lub >= 100 gości
    if ($budgetNum >= 5000 || $guestsNum >= 100) {
        $isHot = true;
    }
}

$hotLabel = $isHot ? '🔥 HOT ' : '';
$rawSubject = $isPartial ? "⚠️ SZKIC (Porzucony): $name" : "{$hotLabel}{$subjectPrefix} Nowe zapytanie od: $name";
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
             "🔥 Interesujące stacje: $stations\n\n" .
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
    // Ochrona przed wyścigiem sieciowym (Race Condition): jeśli finalny submit już przeszedł, zignoruj spóźniony szkic z blur/visibilitychange
    foreach ($candidateHashes as $h) {
        $submittedLock = $draftsDir . '/submitted_' . $h . '.lock';
        if (file_exists($submittedLock) && (time() - (int)@filemtime($submittedLock) < 900)) {
            echo json_encode(["status" => "success", "message" => "Draft ignored (already submitted)."]);
            exit;
        }
    }

    $draftFile = $draftsDir . '/draft_' . $userId . '.json';
    
    // Oblicz "wynik" wypełnienia (jakość, nie ilość)
    $currentScore = 0;
    foreach ([$name, $email, $phone, $date, $location, $guests, $event_type, $stations, $contact_hours] as $field) {
        if (!empty(trim($field))) $currentScore++;
    }
    // Message liczy się tylko jeśli ma sensowną długość
    if (strlen(trim($message)) >= 3) $currentScore++;
    // Budżet liczy się tylko jeśli nie pusty/zero
    if (!empty(trim($budget)) && $budget !== '0' && $budget !== '-') $currentScore++;

    $shouldSave = true;

    // Sprawdź czy mamy już lepszy szkic (w pliku docelowym lub wcześniejszym pliku po samym telefonie/emailu)
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
        $payloadToSave = [
            'data' => $data, // Zapisz surowe dane
            'score' => $currentScore,
            'timestamp' => time(),
            'formattedBody' => $emailBody,
            'subject' => $emailSubject
        ];
        file_put_contents($draftFile, json_encode($payloadToSave), LOCK_EX);
    }

    echo json_encode(["status" => "success", "message" => "Draft saved/updated."]);
} 
// --- NORMALNA WYSYŁKA (FINAL SUBMIT) ---
else {
    @session_write_close();

    // 1. Zablokuj późniejsze nadpisanie przez spóźniony autosave (blur/visibilitychange) i usuń wszelkie szkice tego klienta
    foreach ($candidateHashes as $h) {
        $submittedLock = $draftsDir . '/submitted_' . $h . '.lock';
        @file_put_contents($submittedLock, (string)time(), LOCK_EX);
        $df = $draftsDir . '/draft_' . $h . '.json';
        if (file_exists($df)) {
            @unlink($df);
        }
    }
    cleanupMatchingDraftsAndDigest($draftsDir, $emailNorm, $phoneNorm);

    // 2. Zapisz od razu do CSV (Excel) z blokadą LOCK_EX (Zero Data Loss — niezależnie od statusu lokalnego MTA/mail)
    $csvFile = $csvTargetFile;
    $isNew = !file_exists($csvFile) || filesize($csvFile) === 0;
    
    if ($fp = @fopen($csvFile, 'a')) {
        // Zamknij plik dla innych procesów (Race Condition Fix)
        if (flock($fp, LOCK_EX)) {
            // Jeśli plik nowy, dodaj nagłówek (UTF-8 BOM dla Excela)
            if ($isNew) {
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
                fputcsv($fp, ['Data zgłoszenia', 'Status', 'Źródło', 'Imię', 'Email', 'Telefon', 'Godz. kontaktu', 'Data wydarzenia', 'Lokalizacja', 'Goście', 'Budżet', 'Typ', 'Stacje', 'Wiadomość']);
            }
            
            // Dodaj wiersz
            fputcsv($fp, [
                date('Y-m-d H:i:s'),
                $isHot ? '🔥 HOT' : '✅ WYSŁANY',
                'Formularz',
                $name,
                $email,
                $phone,
                $contact_hours,
                $date,
                $location,
                $guests,
                $budget,
                $event_type,
                $stations,
                $message
            ]);
            
            // Odblokuj
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

    // 3. Rejestracja zgłoszenia w centralnym systemie ticketowym osTicket (Dual-Endpoint Failover) + Ntfy 1-Klik
    $osTicketApiKey = get_env_secret('OSTICKET_API_KEY');
    $ticketCreated = false;
    $ticketNum = 'BRAK';
    if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1' && !empty($osTicketApiKey) && function_exists('curl_init')) {
        $ticketPayload = [
            'name' => html_entity_decode($name, ENT_QUOTES, 'UTF-8') ?: 'Klient Raricart',
            'email' => $email ?: 'kontakt@raricart.pl',
            'phone' => $phone,
            'subject' => $rawSubject,
            'message' => html_entity_decode($emailBody, ENT_QUOTES, 'UTF-8'),
            'ip' => $ip,
            'source' => 'Web',
            'autorespond' => false,
            'send_email' => false,
        ];
        $endpoints = [
            ['url' => 'http://hd.twojastronawww.pl:8080/ticket_hook.php', 'resolve' => ['hd.twojastronawww.pl:8080:127.0.0.1']],
            ['url' => 'https://hd.twojastronawww.pl/ticket_hook.php', 'resolve' => []],
        ];
        foreach ($endpoints as $ep) {
            $ch = curl_init($ep['url']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-API-Key: ' . $osTicketApiKey,
                'Content-Type: application/json',
                'Expect:'
            ]);
            if (!empty($ep['resolve'])) {
                curl_setopt($ch, CURLOPT_RESOLVE, $ep['resolve']);
            }
            curl_setopt($ch, CURLOPT_USERAGENT, 'Raricart-HelpdeskClient/2.0');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ticketPayload, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
            if (defined('CURLPROTO_HTTPS') && defined('CURLPROTO_HTTP')) {
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS | CURLPROTO_HTTP);
            }
            $apiResponse = @curl_exec($ch);
            if (is_string($apiResponse) && strlen($apiResponse) > 65536) {
                $apiResponse = substr($apiResponse, 0, 65536);
            }
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $resArr = is_string($apiResponse) ? json_decode($apiResponse, true) : null;
            if ($httpCode === 201 && !empty($resArr['success'])) {
                $ticketCreated = true;
                $ticketNum = (string)($resArr['number'] ?? 'OK');
                break;
            }
        }
    }

    $ntfyTopic = get_env_secret('NTFY_TOPIC');
    if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1' && $ntfyTopic !== '' && function_exists('curl_init')) {
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
        $chNtfy = curl_init("https://ntfy.sh/{$ntfyTopic}");
        curl_setopt_array($chNtfy, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $emailBody,
            CURLOPT_HTTPHEADER => [
                'Title: ' . ($isHot ? 'HOT LEAD (Raricart) - ' : 'Formularz (Raricart) - ') . $name,
                'Priority: high',
                'Tags: incoming_envelope,pancakes',
                'Actions: ' . implode('; ', array_slice($ntfyActions, 0, 3))
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0
        ]);
        if (defined('CURLPROTO_HTTPS')) {
            curl_setopt($chNtfy, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        }
        @curl_exec($chNtfy);
        curl_close($chNtfy);
    }

    // 4. Wyślij normalnego maila (z kompletnymi nagłówkami MIME RFC 2045 + fallback bez Reply-To w razie restrykcji MTA)
    $cleanReplyTo = str_replace(["\r", "\n", "\0"], '', $email);
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: Formularz WWW <kontakt@raricart.pl>\r\n";
    if ($cleanReplyTo !== '') {
        $headers .= "Reply-To: $cleanReplyTo\r\n";
    }
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";

    $mailSent = false;
    if (getenv('RARICART_TEST_DISABLE_NETWORK') !== '1') {
        $mailSent = @mail($toEmail, $emailSubject, $emailBody, $headers);
        if (!$mailSent) {
            $fallbackHeaders = "MIME-Version: 1.0\r\n";
            $fallbackHeaders .= "From: Formularz WWW <kontakt@raricart.pl>\r\n";
            $fallbackHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $fallbackHeaders .= "Content-Transfer-Encoding: 8bit\r\n";
            $mailSent = @mail($toEmail, $emailSubject, $emailBody, $fallbackHeaders);
        }
    } else {
        $mailSent = true;
    }

    echo json_encode(["status" => "success", "message" => "Wiadomość wysłana!"]);
}


// Na samym końcu skryptu, po próbie wysyłki normalnej:
processDraftQueue($draftsDir, $toEmail, $csvTargetFile);

// --- FUNKCJA CRON: DAILY DIGEST (DEFINICJA) ---
function processDraftQueue($draftsDir, $toEmail, $csvTargetFile = null) {
    if (!$draftsDir || !is_dir($draftsDir)) return;
    if (!$csvTargetFile) {
        $csvTargetFile = __DIR__ . '/../admin/leady.csv';
    }
    
    $lockFile = $draftsDir . '/last_run.txt';
    $lastRun = file_exists($lockFile) ? (int)file_get_contents($lockFile) : 0;
    $r2SyncLock = $draftsDir . '/r2_synced.lock';
    $needsR2Sync = !file_exists($r2SyncLock) || (is_file($csvTargetFile) && (int)@filemtime($csvTargetFile) > (int)@filemtime($r2SyncLock));

    // Sprawdzaj co 10 min (lub przy pierwszej synchronizacji R2)
    if ($needsR2Sync || (time() - $lastRun > 600)) {
        file_put_contents($lockFile, (string)time(), LOCK_EX);

        $files = glob($draftsDir . '/draft_*.json');
        if ($files) {
            foreach ($files as $file) {
                $mtime = filemtime($file);
                if (!$mtime || (time() - $mtime < 600)) continue; // Jeszcze za świeży

                $content = json_decode((string)@file_get_contents($file), true);
                if (!$content) { @unlink($file); continue; }

                $d = $content['data'] ?? [];
                saveDraftToCsv($d, $mtime, $csvTargetFile);
                appendDraftToDigest($draftsDir, $content['formattedBody'] ?? '');

                @unlink($file);
            }
        }

        // Czyść przedawnione pliki blokad rate limitera i znaczników submitted (starsze niż 1h)
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

    // Automatyczna synchronizacja / self-healing backupu CSV do prywatnego R2 (jeśli plik lokalny jest nowszy niż ostatni backup R2)
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

    // Sprawdzaj wysyłkę digestu raz na 24h
    $digestLockFile = $draftsDir . '/last_digest.txt';
    $lastDigest = file_exists($digestLockFile) ? (int)file_get_contents($digestLockFile) : 0;
    
    if (time() - $lastDigest > 86400) {
        $digestBufferFile = $draftsDir . '/digest_buffer.txt';
        if (file_exists($digestBufferFile)) {
            $bufferContent = (string)@file_get_contents($digestBufferFile);
            if (!empty(trim($bufferContent))) {
                // Policz ile leadów jest w buforze
                $count = substr_count($bufferContent, "=== LEAD START ===");
                
                $rawDigestSubject = "⚠️ [Raricart] Dziś porzucono {$count} formularzy";
                $digestSubject = '=?UTF-8?B?' . base64_encode($rawDigestSubject) . '?=';
                $digestBody = "=== DAILY DIGEST: PORZUCONE FORMULARZE ===\n";
                $digestBody .= "Liczba: {$count}\n";
                $digestBody .= "Data: " . date('Y-m-d H:i') . "\n";
                $digestBody .= str_repeat('=', 50) . "\n\n";
                
                // Konwertuj format bufora na czysty tekst
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
            file_put_contents($digestLockFile, (string)time(), LOCK_EX); // brak leadów, aktualizuj czas
        }
    }
}

function cleanupMatchingDraftsAndDigest($draftsDir, $emailNorm, $phoneNorm) {
    if (!$draftsDir || !is_dir($draftsDir)) return;

    $draftFiles = glob($draftsDir . '/draft_*.json') ?: [];
    foreach ($draftFiles as $df) {
        $content = json_decode((string)@file_get_contents($df), true);
        if (!is_array($content)) continue;
        $d = $content['data'] ?? [];
        $dEmail = strtolower(trim((string)($d['email'] ?? '')));
        $dPhone = preg_replace('/\D+/', '', (string)($d['phone'] ?? '')) ?: '';
        if (($emailNorm !== '' && $dEmail === $emailNorm) || ($phoneNorm !== '' && strlen($phoneNorm) >= 7 && $dPhone === $phoneNorm)) {
            @unlink($df);
        }
    }

    $digestBufferFile = $draftsDir . '/digest_buffer.txt';
    if (!file_exists($digestBufferFile)) return;

    $bufferContent = (string)@file_get_contents($digestBufferFile);
    if (trim($bufferContent) === '') return;

    $parts = explode("=== LEAD START ===\n", $bufferContent);
    $kept = [];
    $modified = false;
    foreach ($parts as $part) {
        if (trim($part) === '') continue;
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

function saveDraftToCsv($d, $timestamp, $csvTargetFile = null) {
    $csvFile = $csvTargetFile ?: (__DIR__ . '/../admin/leady.csv');
    $isNew = !file_exists($csvFile) || filesize($csvFile) === 0;
    
    if ($fp = @fopen($csvFile, 'a')) {
        if (flock($fp, LOCK_EX)) {
            if ($isNew) {
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
                fputcsv($fp, ['Data zgłoszenia', 'Status', 'Źródło', 'Imię', 'Email', 'Telefon', 'Godz. kontaktu', 'Data wydarzenia', 'Lokalizacja', 'Goście', 'Budżet', 'Typ', 'Stacje', 'Wiadomość']);
            }
            
            $draftBudget = (int)preg_replace('/[^0-9]/', '', (string)($d['budget'] ?? ''));
            $draftGuests = (int)($d['guests'] ?? 0);
            $draftStatus = ($draftBudget >= 5000 || $draftGuests >= 100) ? '🔥 HOT (SZKIC)' : '⚠️ PORZUCONY';
            
            fputcsv($fp, [
                date('Y-m-d H:i:s', $timestamp),
                $draftStatus,
                'Autosave',
                $d['name'] ?? '',
                $d['email'] ?? '',
                $d['phone'] ?? '',
                $d['contact_hours'] ?? '',
                $d['date'] ?? '',
                $d['location'] ?? '',
                $d['guests'] ?? '',
                $d['budget'] ?? '',
                $d['event_type'] ?? '',
                $d['stations'] ?? '',
                $d['message'] ?? ''
            ]);
            
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

function appendDraftToDigest($draftsDir, $body) {
    if (empty(trim($body))) return;
    $digestBufferFile = $draftsDir . '/digest_buffer.txt';
    $entry = "=== LEAD START ===\n" . $body . "\n\n";
    file_put_contents($digestBufferFile, $entry, FILE_APPEND | LOCK_EX);
}
?>