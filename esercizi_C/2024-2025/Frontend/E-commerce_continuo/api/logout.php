<?php
// Includi la configurazione del database e le classi necessarie
require_once '../config/database.php';
require_once '../includes/Session.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Inizializza la sessione
$session = new Session($pdo);

// Rimuovi l'ID utente dalla sessione
$session->clearUserId();

echo json_encode(['success' => true, 'message' => 'Logout effettuato con successo']);
?>