<?php
require_once '../config/database.php';
require_once '../includes/Session.php';

// Inizializza la sessione
$session = new Session($pdo);

// Verifica il coupon
$codice = $_POST['codice'] ?? null;

if (!$codice) {
    http_response_code(400);
    echo json_encode(['error' => 'Codice coupon mancante']);
    exit;
}

// Cerca il coupon nel database
$stmt = $pdo->prepare("
    SELECT * FROM coupon 
    WHERE codice = :codice 
    AND attivo = TRUE 
    AND (data_inizio IS NULL OR data_inizio <= CURDATE()) 
    AND (data_fine IS NULL OR data_fine >= CURDATE())
");
$stmt->execute(['codice' => $codice]);
$coupon = $stmt->fetch();

if (!$coupon) {
    echo json_encode(['valid' => false, 'message' => 'Coupon non valido o scaduto']);
    exit;
}

// Coupon valido
echo json_encode([
    'valid' => true,
    'message' => 'Coupon applicato con successo',
    'discount' => $coupon['percentuale_sconto']
]);
?>