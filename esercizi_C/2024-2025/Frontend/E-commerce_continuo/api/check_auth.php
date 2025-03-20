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

if ($userId) {
    // Ottieni i dati dell'utente
    $stmt = $pdo->prepare("SELECT id, nome, email FROM utenti WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo json_encode([
            'logged_in' => true,
            'user' => [
                'id' => $user['id'],
                'name' => $user['nome'],
                'email' => $user['email']
            ]
        ]);
    } else {
        // L'utente non esiste più nel database
        $session->clearUserId();
        echo json_encode(['logged_in' => false]);
    }
} else {
    echo json_encode(['logged_in' => false]);
}
?>