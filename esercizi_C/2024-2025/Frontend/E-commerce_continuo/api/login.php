<?php
// Includi la configurazione del database e le classi necessarie
require_once '../config/database.php';
require_once '../includes/Session.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Verifica che i dati di login siano stati forniti
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email e password sono richiesti']);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];
$rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === 'true';

try {
    // Cerca l'utente nel database
    $stmt = $pdo->prepare("SELECT id, nome, email, password FROM utenti WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    // Verifica se l'utente esiste e la password è corretta
    if ($user && password_verify($password, $user['password'])) {
        // Inizializza la sessione
        $session = new Session($pdo);
        
        // Imposta l'ID utente nella sessione
        $session->setUserId($user['id']);
        
        // Trasferisci il carrello dalla sessione all'utente
        $session->transferCartFromSessionToUser();
        
        // Restituisci una risposta di successo
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'name' => $user['nome'],
                'email' => $user['email']
            ]
        ]);
    } else {
        // Credenziali non valide
        echo json_encode(['success' => false, 'message' => 'Email o password non validi']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore durante l\'accesso: ' . $e->getMessage()]);
}
?>