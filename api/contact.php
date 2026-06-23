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

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Anti-Spam
if (!empty($data['website_check'])) {
    echo json_encode(["status" => "success", "message" => "Wiadomość została wysłana!"]);
    exit;
}

// --- DRAFTS DIR (musi być PRZED pingiem) ---
$draftsDir = __DIR__ . '/drafts';
if (!is_dir($draftsDir)) {
    @mkdir($draftsDir, 0777, true);
    @file_put_contents($draftsDir . '/.htaccess', "Deny from all"); 
}

// --- POOR MAN'S CRON TRIGGER ---
if (($data['action'] ?? '') === 'ping') {
    processDraftQueue($draftsDir, $toEmail);
    echo json_encode(["status" => "pong"]);
    exit;
}

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Brak danych."]);
    exit;
}

// Sanityzacja
$name = htmlspecialchars(strip_tags($data['name'] ?? ''));
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(strip_tags($data['phone'] ?? ''));
$date = htmlspecialchars(strip_tags($data['date'] ?? ''));
$location = htmlspecialchars(strip_tags($data['location'] ?? ''));
$guests = htmlspecialchars(strip_tags($data['guests'] ?? ''));
$budget = htmlspecialchars(strip_tags($data['budget'] ?? ''));
$event_type = htmlspecialchars(strip_tags($data['event_type'] ?? ''));
$stations = htmlspecialchars(strip_tags($data['stations'] ?? ''));
$contact_hours = htmlspecialchars(strip_tags($data['contact_hours'] ?? ''));
$message = htmlspecialchars(strip_tags($data['message'] ?? ''));
$isPartial = $data['is_partial'] ?? false;
$isAbandoned = $data['is_abandoned'] ?? false;

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
$emailSubject = $isPartial ? "⚠️ SZKIC (Porzucony): $name" : "{$hotLabel}{$subjectPrefix} Nowe zapytanie od: $name";
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
// $draftsDir już zdefiniowany wyżej (przed pingiem)

// Unikalny identyfikator użytkownika (Email lub Telefon)
$userId = $email ? md5($email) : ($phone ? md5($phone) : null);

if ($isPartial && $userId) {
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

    // Sprawdź czy mamy już lepszy szkic
    if (file_exists($draftFile)) {
        $savedData = json_decode(file_get_contents($draftFile), true);
        if (($savedData['score'] ?? 0) > $currentScore) {
            $shouldSave = false;
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
        file_put_contents($draftFile, json_encode($payloadToSave));
    }

    if ($isAbandoned) {
        // Zapisz od razu do CSV i do bufora digestu
        saveDraftToCsv($data, time());
        appendDraftToDigest($draftsDir, $emailBody);
        @unlink($draftFile);
    }

    echo json_encode(["status" => "success", "message" => "Draft saved/updated."]);
} 
// --- NORMALNA WYSYŁKA (FINAL SUBMIT) ---
else {
    // 1. Wyślij normalnego maila
    $headers = "From: Formularz WWW <kontakt@raricart.pl>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($toEmail, $emailSubject, $emailBody, $headers)) {
        echo json_encode(["status" => "success", "message" => "Wiadomość wysłana!"]);

        // 2. Jeśli użytkownik wysłał formularz, usuń jego szkic (nie potrzebujemy go już)
        if ($userId) {
            $draftFile = $draftsDir . '/draft_' . $userId . '.json';
            if (file_exists($draftFile)) @unlink($draftFile);
        }

        // --- 3. ZAPIS DO CSV (Excel) ---
        $csvFile = __DIR__ . '/../admin/leady.csv';
        $isNew = !file_exists($csvFile);
        
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
                    '✅ WYSŁANY',
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
        }
    }
}


// Na samym końcu skryptu, po próbie wysyłki normalnej:
processDraftQueue($draftsDir, $toEmail);

// --- FUNKCJA CRON: DAILY DIGEST (DEFINICJA) ---
function processDraftQueue($draftsDir, $toEmail) {
    if (!$draftsDir || !is_dir($draftsDir)) return;
    
    $lockFile = $draftsDir . '/last_run.txt';
    $lastRun = file_exists($lockFile) ? (int)file_get_contents($lockFile) : 0;

    // Sprawdzaj co 10 min
    if (time() - $lastRun > 600) {
        file_put_contents($lockFile, time());

        $files = glob($draftsDir . '/draft_*.json');
        if ($files) {
            foreach ($files as $file) {
                $mtime = filemtime($file);
                if (!$mtime || (time() - $mtime < 600)) continue; // Jeszcze za świeży

                $content = json_decode(file_get_contents($file), true);
                if (!$content) { @unlink($file); continue; }

                $d = $content['data'] ?? [];
                saveDraftToCsv($d, $mtime);
                appendDraftToDigest($draftsDir, $content['formattedBody'] ?? '');

                @unlink($file);
            }
        }
    }

    // Sprawdzaj wysyłkę digestu raz na 24h
    $digestLockFile = $draftsDir . '/last_digest.txt';
    $lastDigest = file_exists($digestLockFile) ? (int)file_get_contents($digestLockFile) : 0;
    
    if (time() - $lastDigest > 86400) {
        $digestBufferFile = $draftsDir . '/digest_buffer.txt';
        if (file_exists($digestBufferFile)) {
            $bufferContent = file_get_contents($digestBufferFile);
            if (!empty(trim($bufferContent))) {
                // Policz ile leadów jest w buforze
                $count = substr_count($bufferContent, "=== LEAD START ===");
                
                $digestSubject = "⚠️ [Raricart] Dziś porzucono {$count} formularzy";
                $digestBody = "=== DAILY DIGEST: PORZUCONE FORMULARZE ===\n";
                $digestBody .= "Liczba: {$count}\n";
                $digestBody .= "Data: " . date('Y-m-d H:i') . "\n";
                $digestBody .= str_repeat('=', 50) . "\n\n";
                
                // Konwertuj format bufora na czysty tekst
                $digestBody .= str_replace("=== LEAD START ===\n", "--- Lead ---\n", $bufferContent);

                $cronHeaders = "From: Formularz WWW (Digest) <kontakt@raricart.pl>\r\n";
                $cronHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
                
                if (mail($toEmail, $digestSubject, $digestBody, $cronHeaders)) {
                    @unlink($digestBufferFile);
                    file_put_contents($digestLockFile, time());
                }
            }
        } else {
            file_put_contents($digestLockFile, time()); // brak leadów, aktualizuj czas
        }
    }
}

function saveDraftToCsv($d, $timestamp) {
    $csvFile = __DIR__ . '/../admin/leady.csv';
    $isNew = !file_exists($csvFile);
    
    if ($fp = @fopen($csvFile, 'a')) {
        if (flock($fp, LOCK_EX)) {
            if ($isNew) {
                fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
                fputcsv($fp, ['Data zgłoszenia', 'Status', 'Źródło', 'Imię', 'Email', 'Telefon', 'Godz. kontaktu', 'Data wydarzenia', 'Lokalizacja', 'Goście', 'Budżet', 'Typ', 'Stacje', 'Wiadomość']);
            }
            
            fputcsv($fp, [
                date('Y-m-d H:i:s', $timestamp),
                '⚠️ PORZUCONY',
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
    }
}

function appendDraftToDigest($draftsDir, $body) {
    if (empty(trim($body))) return;
    $digestBufferFile = $draftsDir . '/digest_buffer.txt';
    $entry = "=== LEAD START ===\n" . $body . "\n\n";
    file_put_contents($digestBufferFile, $entry, FILE_APPEND | LOCK_EX);
}
?>