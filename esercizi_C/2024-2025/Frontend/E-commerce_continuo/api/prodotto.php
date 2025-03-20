<?php
require_once '../config/database.php';
require_once '../includes/Session.php';

// Inizializza la sessione
$session = new Session($pdo);

// Ottieni l'ID del prodotto
$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID prodotto mancante']);
    exit;
}

// Ottieni i dettagli del prodotto
$stmt = $pdo->prepare("SELECT * FROM prodotti WHERE id = :id");
$stmt->execute(['id' => $id]);
$prodotto = $stmt->fetch();

if (!$prodotto) {
    http_response_code(404);
    echo json_encode(['error' => 'Prodotto non trovato']);
    exit;
}

// Converti i campi JSON in array PHP
$prodotto['immagini'] = json_decode($prodotto['immagini'], true);
$prodotto['specifiche'] = json_decode($prodotto['specifiche'], true);
$prodotto['specifiche_dettagliate'] = json_decode($prodotto['specifiche_dettagliate'], true);
$prodotto['varianti'] = json_decode($prodotto['varianti'], true);

// Invia la risposta come JSON
header('Content-Type: application/json');
echo json_encode($prodotto);
?>