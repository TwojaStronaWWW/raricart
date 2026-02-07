<?php
// contact.php

// --- KONFIGURACJA ---
$toEmail = "pomoc@raricart.pl"; // Poprawione: czysty adres w cudzysłowie
$subjectPrefix = "[Formularz Raricart]"; 
// ---------------------

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Anti-Spam
if (!empty($data['website_check'])) {
    echo json_encode(["status" => "success", "message" => "Wiadomość została wysłana!"]);
    exit;
}

// --- POOR MAN'S CRON TRIGGER ---
// Pozwala "obudzić" skrypt samym wejściem na stronę
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
$message = htmlspecialchars(strip_tags($data['message'] ?? ''));
$isPartial = $data['is_partial'] ?? false;

// Walidacja
if (!$isPartial && (empty($name) || empty($email))) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Wypełnij wymagane pola."]);
    exit;
}

// Jeśli partial, ale brak kontaktu, też odrzuć po cichu (żeby nie słać pustych)
if ($isPartial && empty($email) && empty($phone)) {
    // Nie traktujemy tego jako błąd 400, tylko success bez wysyłki (silent ignore)
    echo json_encode(["status" => "success", "message" => "Ignored empty draft"]);
    exit;
}

$emailSubject = $isPartial ? "⚠️ SZKIC (Porzucony): $name" : "$subjectPrefix Nowe zapytanie od: $name";
$emailBody = ($isPartial ? "--- TO JEST NIEUKOŃCZONY SZKIC FORMULARZA ---\n\n" : "Nowe zapytanie ze strony:\n\n") .
             "👤 Imię: $name\n" .
             "📧 Email: $email\n" .
             "📞 Tel: $phone\n\n" .
             "📅 Data wydarzenia: $date\n" .
             "👥 Liczba gości: $guests\n" .
             "💰 Budżet: $budget\n" .
             "🎉 Rodzaj wydarzenia: $event_type\n" .
             "🔥 Interesujące stacje: $stations\n\n" .
             "💬 Wiadomość:\n$message";

// --- LOGIKA SKŁADOWANIA SZKICÓW (DRAFT) ---
$draftsDir = __DIR__ . '/drafts';
if (!is_dir($draftsDir)) {
    @mkdir($draftsDir, 0777, true);
    // Zabezpiecz katalog przed podglądem
    @file_put_contents($draftsDir . '/.htaccess', "Deny from all"); 
}

// Unikalny identyfikator użytkownika (Email lub Telefon)
$userId = $email ? md5($email) : ($phone ? md5($phone) : null);

if ($isPartial && $userId) {
    $draftFile = $draftsDir . '/draft_' . $userId . '.json';
    
    // Oblicz "wynik" wypełnienia (liczba pól)
    $currentScore = 0;
    foreach ([$name, $email, $phone, $date, $guests, $budget, $event_type, $stations, $message] as $field) {
        if (!empty(trim($field))) $currentScore++;
    }

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
                    fputcsv($fp, ['Data zgłoszenia', 'Imię', 'Email', 'Telefon', 'Data wydarzenia', 'Goście', 'Budżet', 'Typ', 'Stacje', 'Wiadomość']);
                }
                
                // Dodaj wiersz
                fputcsv($fp, [
                    date('Y-m-d H:i:s'),
                    $name,
                    $email,
                    $phone,
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

// ... (Reszta skryptu: Walidacja, Wysyłka itp.) ...

// Na samym końcu skryptu, po próbie wysyłki normalnej:
processDraftQueue($draftsDir, $toEmail);

// --- FUNKCJA CRON (DEFINICJA) ---
function processDraftQueue($draftsDir, $toEmail) {
    $lockFile = $draftsDir . '/last_run.txt';
    $lastRun = file_exists($lockFile) ? (int)file_get_contents($lockFile) : 0;

    if (time() - $lastRun > 600) { // 600 sekund = 10 minut
        // Zaktualizuj czas (blokada)
        file_put_contents($lockFile, time());

        $files = glob($draftsDir . '/draft_*.json');
        if ($files) {
            foreach ($files as $file) {
                $mtime = filemtime($file);
                // Upewnij się, że mtime jest poprawny (nie false) i minęło 10 minut
                if ($mtime && (time() - $mtime > 600)) {
                    $content = json_decode(file_get_contents($file), true);
                    
                    if ($content) {
                        $dSubject = $content['subject'];
                        $dBody = $content['formattedBody'];
                        
                        $cronHeaders = "From: Formularz WWW (Szkic) <kontakt@raricart.pl>\r\n";
                        $cronHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
                        
                        mail($toEmail, $dSubject, $dBody, $cronHeaders);
                    }
                    @unlink($file);
                }
            }
        }
    }
}
?>