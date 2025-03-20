<?php
require_once '../config/database.php';
require_once '../includes/Session.php';

// Inizializza la sessione
$session = new Session($pdo);

// Ottieni l'ID dell'ordine
$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID ordine mancante']);
    exit;
}

// Ottieni i dettagli dell'ordine
$stmt = $pdo->prepare("
    SELECT * FROM ordini 
    WHERE id = :id AND (
        utente_id = :user_id OR 
        sessione_id = :session_id
    )
");
$stmt->execute([
    'id' => $orderId,
    'user_id' => $session->getUserId(),
    'session_id' => $session->getSessionId()
]);
$ordine = $stmt->fetch();

if (!$ordine) {
    http_response_code(404);
    echo json_encode(['error' => 'Ordine non trovato o non autorizzato']);
    exit;
}

// Converti i campi JSON in array PHP
$ordine['prodotti'] = json_decode($ordine['prodotti'], true);

// Invia la risposta come JSON
header('Content-Type: application/json');
echo json_encode($ordine);
?>