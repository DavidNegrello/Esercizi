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
    // Ottieni lo stato del questionario dell'utente
    $stmt = $pdo->prepare("
        SELECT id, domanda_1, risposta_1, domanda_2, risposta_2, domanda_3, risposta_3, profilo_risultante
        FROM questionario
        WHERE utente_id = :user_id
        ORDER BY data_compilazione DESC
        LIMIT 1
    ");
    $stmt->execute(['user_id' => $userId]);
    $questionario = $stmt->fetch();
    
    // Calcola lo stato di completamento
    $completed = false;
    $profile = null;
    $progressPercentage = 0;
    
    if ($questionario) {
        // Conta quante domande sono state compilate
        $risposteCompilate = 0;
        if (!empty($questionario['risposta_1'])) $risposteCompilate++;
        if (!empty($questionario['risposta_2'])) $risposteCompilate++;
        if (!empty($questionario['risposta_3'])) $risposteCompilate++;
        
        // Calcola la percentuale di completamento
        $progressPercentage = ($risposteCompilate / 3) * 100;
        
        // Verifica se il questionario è completo
        $completed = ($risposteCompilate === 3);
        
        // Ottieni il profilo risultante
        $profile = $questionario['profilo_risultante'];
    }
    
    echo json_encode([
        'success' => true,
        'completed' => $completed,
        'progress_percentage' => (int)$progressPercentage,
        'profile' => $profile
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore nel caricamento dello stato del questionario: ' . $e->getMessage()]);
}
?>