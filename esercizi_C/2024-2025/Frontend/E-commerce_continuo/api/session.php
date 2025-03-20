<?php
require_once '../config/database.php';

// Inizia o recupera la sessione
session_start();

// Funzione per ottenere o creare l'ID sessione
function getSessionId() {
    if (!isset($_SESSION['session_id'])) {
        $_SESSION['session_id'] = session_id();
        
        // Salva la sessione nel database
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO sessioni (session_id, data_scadenza) VALUES (?, DATE_ADD(NOW(), INTERVAL 30 DAY))");
        $stmt->execute([$_SESSION['session_id']]);
    }
    
    return $_SESSION['session_id'];
}

// Funzione per aggiornare l'ID utente nella sessione dopo il login
function updateSessionUser($userId) {
    global $pdo;
    $sessionId = getSessionId();
    
    $stmt = $pdo->prepare("UPDATE sessioni SET utente_id = ? WHERE session_id = ?");
    $stmt->execute([$userId, $sessionId]);
    
    $_SESSION['user_id'] = $userId;
}

// Funzione per ottenere l'ID utente corrente (se loggato)
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Funzione per verificare se l'utente è loggato
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Funzione per il logout
function logout() {
    global $pdo;
    $sessionId = getSessionId();
    
    // Aggiorna la sessione nel database
    $stmt = $pdo->prepare("UPDATE sessioni SET utente_id = NULL WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    
    // Rimuovi l'ID utente dalla sessione
    unset($_SESSION['user_id']);
}
?>