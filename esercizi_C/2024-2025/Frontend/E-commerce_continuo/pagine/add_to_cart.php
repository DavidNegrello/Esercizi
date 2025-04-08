<?php
/**
 * Script per l'aggiunta di prodotti al carrello
 *
 * Gestisce l'aggiunta di prodotti nel database, creando un carrello se necessario
 * e aggiungendo il prodotto selezionato con le sue varianti
 */

// Avvia la sessione utente
session_start();

// Inclusione della configurazione del database
require_once '../conf/db_config.php';

// Controlla che la richiesta sia di tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit;
}

// Legge il corpo della richiesta JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verifica che i dati necessari siano presenti
if (!isset($input['productId']) || !isset($input['quantity']) || !isset($input['price'])) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit;
}

// Estrazione dei dati dalla richiesta
$prodottoId = (int)$input['productId'];
$quantita = (int)$input['quantity'];
$prezzo = (float)$input['price'];
$coloreId = isset($input['colorId']) ? (int)$input['colorId'] : null;
$tagliaId = isset($input['sizeId']) ? (int)$input['sizeId'] : null;
$capacitaId = isset($input['capacityId']) ? (int)$input['capacityId'] : null;

// 1. Verifica se l'utente è loggato o è una sessione anonima
$utenteId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$sessionId = session_id();

// Connessione al database
$conn = getConnection();

// Inizia una transazione
$conn->begin_transaction();

try {
    // 2. Verifica se esiste già un carrello attivo per l'utente o la sessione
    if ($utenteId) {
        $sql = "SELECT id FROM carrello WHERE utente_id = ? AND stato = 'attivo'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $utenteId);
    } else {
        $sql = "SELECT id FROM carrello WHERE session_id = ? AND stato = 'attivo'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $sessionId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Carrello esistente
        $carrello = $result->fetch_assoc();
        $carrelloId = $carrello['id'];
    } else {
        // Crea un nuovo carrello
        if ($utenteId) {
            $sql = "INSERT INTO carrello (utente_id, stato) VALUES (?, 'attivo')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $utenteId);
        } else {
            $sql = "INSERT INTO carrello (session_id, stato) VALUES (?, 'attivo')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $sessionId);
        }

        $stmt->execute();
        $carrelloId = $conn->insert_id;
    }

    // 3. Verifica se il prodotto è già nel carrello con le stesse varianti
    $sql = "SELECT id, quantita FROM carrello_prodotti 
            WHERE carrello_id = ? AND prodotto_id = ? 
            AND (colore_id IS NULL OR colore_id = ?) 
            AND (taglia_id IS NULL OR taglia_id = ?) 
            AND (capacita_id IS NULL OR capacita_id = ?)";

    $stmt = $conn->prepare($sql);

    // Gestione dei parametri NULL per le varianti
    $nullColoreId = $coloreId ?? null;
    $nullTagliaId = $tagliaId ?? null;
    $nullCapacitaId = $capacitaId ?? null;

    $stmt->bind_param('iiiii', $carrelloId, $prodottoId, $nullColoreId, $nullTagliaId, $nullCapacitaId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Il prodotto esiste già nel carrello, aggiorna la quantità
        $prodottoCarrello = $result->fetch_assoc();
        $nuovaQuantita = $prodottoCarrello['quantita'] + $quantita;

        $sql = "UPDATE carrello_prodotti SET quantita = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $nuovaQuantita, $prodottoCarrello['id']);
        $stmt->execute();
    } else {
        // Aggiungi il nuovo prodotto al carrello
        $sql = "INSERT INTO carrello_prodotti (carrello_id, prodotto_id, quantita, colore_id, taglia_id, capacita_id, prezzo_unitario) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiiiiiid', $carrelloId, $prodottoId, $quantita, $nullColoreId, $nullTagliaId, $nullCapacitaId, $prezzo);
        $stmt->execute();
    }

    // 4. Ottieni il conteggio totale degli articoli nel carrello
    $sql = "SELECT SUM(quantita) as total FROM carrello_prodotti WHERE carrello_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $carrelloId);
    $stmt->execute();
    $result = $stmt->get_result();
    $totale = $result->fetch_assoc()['total'];

    // Aggiorna lo stato della transazione e aggiorna i dati della sessione
    $conn->commit();

    // Aggiorna il conteggio nel carrello della sessione
    $_SESSION['cart_count'] = $totale;

    echo json_encode([
        'success' => true,
        'message' => 'Prodotto aggiunto al carrello',
        'cartCount' => $totale
    ]);

} catch (Exception $e) {
    // Annulla la transazione in caso di errore
    $conn->rollback();

    echo json_encode([
        'success' => false,
        'message' => 'Errore durante l\'aggiunta al carrello: ' . $e->getMessage()
    ]);
}

// Chiudi la connessione
$conn->close();
