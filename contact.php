<?php
// contact.php — DEPRECATED
// Ten plik NIE jest już używany. Cała logika została przeniesiona do api/contact.php.
// Pozostawiony jako redirect dla bezpieczeństwa (gdyby ktoś miał stary URL).

header("Content-Type: application/json; charset=UTF-8");
http_response_code(301);

// Przekieruj POST requesty do nowego endpointu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    
    // Forward do prawidłowego endpointu
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://' . $_SERVER['HTTP_HOST'] . '/api/contact.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo $response;
    exit;
}

echo json_encode(["status" => "error", "message" => "Ten endpoint jest przestarzały. Użyj /api/contact.php"]);
?>