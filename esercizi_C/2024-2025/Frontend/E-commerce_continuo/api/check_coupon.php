<?php
// Includi la configurazione del database
require_once '../config/database.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Verifica che il codice coupon sia stato fornito
if (!isset($_POST['code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Codice coupon mancante']);
    exit;
}

$code = $_POST['code'];

try {
    // Verifica se il coupon esiste ed è valido
    $stmt = $pdo->prepare("
        SELECT id, percentuale_sconto
        FROM coupon
        WHERE codice = :code 
          AND attivo = TRUE
          AND (data_inizio IS NULL OR data_inizio <= CURDATE())
          AND (data_fine IS NULL OR data_fine >= CURDATE())
    ");
    $stmt->execute(['code' => $code]);
    $coupon = $stmt->fetch();
    
    if ($coupon) {
        echo json_encode([
            'valid' => true,
            'discount' => $coupon['percentuale_sconto'],
            'message' => 'Coupon applicato! Hai ricevuto uno sconto del ' . $coupon['percentuale_sconto'] . '%'
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'message' => 'Coupon non valido o scaduto.'
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore nella verifica del coupon: ' . $e->getMessage()]);
}
?>