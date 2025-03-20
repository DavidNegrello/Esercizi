<?php
// Includi la configurazione del database e le classi necessarie
require_once '../config/database.php';
require_once '../includes/Session.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Inizializza la sessione
$session = new Session($pdo);

// Verifica se l'utente è loggato
$userId = $session->getUserId();

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

try {
    // Ottieni il limite di ordini da visualizzare (se specificato)
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
    
    // Ottieni il conteggio totale degli ordini dell'utente
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM ordini
        WHERE utente_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);
    $totalCount = $stmt->fetch()['total'];
    
    // Prepara la query per ottenere gli ordini
    $sql = "
        SELECT id, data_ordine, totale, stato
        FROM ordini
        WHERE utente_id = :user_id
        ORDER BY data_ordine DESC
    ";
    
    // Aggiungi il limite se specificato
    if ($limit) {
        $sql .= " LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    
    if ($limit) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $orders = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'total_count' => $totalCount,
        'orders' => $orders
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore nel caricamento degli ordini: ' . $e->getMessage()]);
}
?>