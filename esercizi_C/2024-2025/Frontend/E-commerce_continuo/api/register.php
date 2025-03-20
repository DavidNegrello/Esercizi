<?php
// Includi la configurazione del database
require_once '../config/database.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Verifica che i dati di registrazione siano stati forniti
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tutti i campi sono richiesti']);
    exit;
}

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Validazione base
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'La password deve contenere almeno 6 caratteri']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email non valida']);
    exit;
}

try {
    // Verifica se l'email è già in uso
    $stmt = $pdo->prepare("SELECT id FROM utenti WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email già in uso']);
        exit;
    }
    
    // Hash della password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Inserisci il nuovo utente
    $stmt = $pdo->prepare("
        INSERT INTO utenti (nome, email, password)
        VALUES (:nome, :email, :password)
    ");
    $stmt->execute([
        'nome' => $name,
        'email' => $email,
        'password' => $hashedPassword
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Registrazione completata con successo']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore durante la registrazione: ' . $e->getMessage()]);
}
?>