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
header("Access-Control-Allow-Methods: POST");

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
$guests = htmlspecialchars(strip_tags($data['guests'] ?? ''));
$budget = htmlspecialchars(strip_tags($data['budget'] ?? ''));
$event_type = htmlspecialchars(strip_tags($data['event_type'] ?? ''));
$stations = htmlspecialchars(strip_tags($data['stations'] ?? ''));
$contact_hours = htmlspecialchars(strip_tags($data['contact_hours'] ?? ''));
$message = htmlspecialchars(strip_tags($data['message'] ?? ''));
$isPartial = $data['is_partial'] ?? false;

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
    foreach ([$name, $email, $phone, $date, $guests, $event_type, $stations, $contact_hours] as $field) {
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
                    fputcsv($fp, ['Data zgłoszenia', 'Status', 'Źródło', 'Imię', 'Email', 'Telefon', 'Godz. kontaktu', 'Data wydarzenia', 'Goście', 'Budżet', 'Typ', 'Stacje', 'Wiadomość']);
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

    // Sprawdzaj co 10 min, ale mail digest raz na 24h
    if (time() - $lastRun > 600) {
        file_put_contents($lockFile, time());

        $files = glob($draftsDir . '/draft_*.json');
        if (!$files) return;

        $digestParts = []; // Zbierz treści do digestu
        $digestLockFile = $draftsDir . '/last_digest.txt';
        $lastDigest = file_exists($digestLockFile) ? (int)file_get_contents($digestLockFile) : 0;
        $shouldSendDigest = (time() - $lastDigest > 86400); // 24h

        foreach ($files as $file) {
            $mtime = filemtime($file);
            if (!$mtime || (time() - $mtime < 600)) continue; // Jeszcze za świeży

            $content = json_decode(file_get_contents($file), true);
            if (!$content) { @unlink($file); continue; }

            // --- ZAWSZE: ZAPIS DO CSV ---
            $d = $content['data'] ?? [];
            $csvFile = __DIR__ . '/../admin/leady.csv';
            $isNew = !file_exists($csvFile);
            
            if ($fp = @fopen($csvFile, 'a')) {
                if (flock($fp, LOCK_EX)) {
                    if ($isNew) {
                        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
                        fputcsv($fp, ['Data zgłoszenia', 'Status', 'Źródło', 'Imię', 'Email', 'Telefon', 'Godz. kontaktu', 'Data wydarzenia', 'Goście', 'Budżet', 'Typ', 'Stacje', 'Wiadomość']);
                    }
                    
                    fputcsv($fp, [
                        date('Y-m-d H:i:s', $mtime),
                        '⚠️ PORZUCONY',
                        'Autosave',
                        $d['name'] ?? '',
                        $d['email'] ?? '',
                        $d['phone'] ?? '',
                        $d['contact_hours'] ?? '',
                        $d['date'] ?? '',
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

            // --- ZBIERAJ DO DIGESTU (nie wysyłaj osobno) ---
            if ($shouldSendDigest) {
                $digestParts[] = $content['formattedBody'] ?? '';
            }

            @unlink($file);
        }

        // --- WYŚLIJ 1 ZBIORCZY MAIL (max raz na 24h) ---
        if ($shouldSendDigest && !empty($digestParts)) {
            $count = count($digestParts);
            $digestSubject = "⚠️ [Raricart] Dziś porzucono {$count} formularzy";
            $digestBody = "=== DAILY DIGEST: PORZUCONE FORMULARZE ===\n";
            $digestBody .= "Liczba: {$count}\n";
            $digestBody .= "Data: " . date('Y-m-d H:i') . "\n";
            $digestBody .= str_repeat('=', 50) . "\n\n";

            foreach ($digestParts as $i => $part) {
                $digestBody .= "--- Lead #" . ($i + 1) . " ---\n";
                $digestBody .= $part . "\n\n";
            }

            $cronHeaders = "From: Formularz WWW (Digest) <kontakt@raricart.pl>\r\n";
            $cronHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            mail($toEmail, $digestSubject, $digestBody, $cronHeaders);
            file_put_contents($digestLockFile, time());
        }
    }
}
?>