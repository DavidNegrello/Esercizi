<?php
require_once '../config/database.php';
require_once 'session.php';

// Funzione per ottenere o creare un carrello per la sessione corrente
function getOrCreateCart() {
    global $pdo;
    $sessionId = getSessionId();
    $userId = getCurrentUserId();
    
    // Cerca un carrello attivo per questa sessione o utente
    $stmt = $pdo->prepare("SELECT id FROM carrello WHERE (sessione_id = ? OR utente_id = ?) AND attivo = TRUE ORDER BY id DESC LIMIT 1");
    $stmt->execute([$sessionId, $userId]);
    $cart = $stmt->fetch();
    
    if ($cart) {
        return $cart['id'];
    }
    
    // Se non esiste, crea un nuovo carrello
    $stmt = $pdo->prepare("INSERT INTO carrello (utente_id, sessione_id) VALUES (?, ?)");
    $stmt->execute([$userId, $sessionId]);
    
    return $pdo->lastInsertId();
}

// Funzione per aggiungere un prodotto al carrello
function addToCart($prodottoId, $prezzo, $varianti = null, $personalizzazioni = null, $tipo = 'catalogo') {
    global $pdo;
    $cartId = getOrCreateCart();
    
    // Converti gli array in JSON per il salvataggio
    $variantiJson = $varianti ? json_encode($varianti) : null;
    $personalizzazioniJson = $personalizzazioni ? json_encode($personalizzazioni) : null;
    
    // Inserisci il prodotto nel carrello
    $stmt = $pdo->prepare("INSERT INTO carrello_prodotti 
                          (carrello_id, prodotto_id, prezzo_unitario, varianti_selezionate, personalizzazioni, tipo) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cartId, $prodottoId, $prezzo, $variantiJson, $personalizzazioniJson, $tipo]);
    
    return $pdo->lastInsertId();
}

// Funzione per rimuovere un prodotto dal carrello (disattivazione)
function removeFromCart($prodottoCarrelloId) {
    global $pdo;
    $sessionId = getSessionId();
    $userId = getCurrentUserId();
    
    // Verifica che il prodotto appartenga al carrello dell'utente corrente
    $stmt = $pdo->prepare("UPDATE carrello_prodotti SET attivo = FALSE 
                          WHERE id = ? AND carrello_id IN 
                          (SELECT id FROM carrello WHERE (sessione_id = ? OR utente_id = ?) AND attivo = TRUE)");
    $stmt->execute([$prodottoCarrelloId, $sessionId, $userId]);
    
    return $stmt->rowCount() > 0;
}

// Funzione per ottenere il contenuto del carrello
function getCartContents() {
    global $pdo;
    $sessionId = getSessionId();
    $userId = getCurrentUserId();
    
    $stmt = $pdo->prepare("
        SELECT cp.id, cp.prodotto_id, p.nome, p.immagine, cp.prezzo_unitario as prezzo, 
               cp.varianti_selezionate, cp.personalizzazioni, cp.tipo
        FROM carrello_prodotti cp
        JOIN carrello c ON cp.carrello_id = c.id
        JOIN prodotti p ON cp.prodotto_id = p.id
        WHERE (c.sessione_id = ? OR c.utente_id = ?) 
        AND c.attivo = TRUE 
        AND cp.attivo = TRUE
        ORDER BY cp.id DESC
    ");
    $stmt->execute([$sessionId, $userId]);
    
    $results = $stmt->fetchAll();
    
    // Converti i campi JSON in array PHP
    foreach ($results as &$item) {
        if ($item['varianti_selezionate']) {
            $item['varianti'] = json_decode($item['varianti_selezionate'], true);
            unset($item['varianti_selezionate']);
        }
        
        if ($item['personalizzazioni']) {
            $item['personalizzazioni'] = json_decode($item['personalizzazioni'], true);
        }
    }
    
    return $results;
}

// Funzione per svuotare il carrello (disattivazione)
function emptyCart() {
    global $pdo;
    $sessionId = getSessionId();
    $userId = getCurrentUserId();
    
    // Disattiva tutti i prodotti nel carrello
    $stmt = $pdo->prepare("
        UPDATE carrello_prodotti SET attivo = FALSE
        WHERE carrello_id IN (
            SELECT id FROM carrello 
            WHERE (sessione_id = ? OR utente_id = ?) AND attivo = TRUE
        )
    ");
    $stmt->execute([$sessionId, $userId]);
    
    // Disattiva il carrello stesso
    $stmt = $pdo->prepare("
        UPDATE carrello SET attivo = FALSE
        WHERE (sessione_id = ? OR utente_id = ?) AND attivo = TRUE
    ");
    $stmt->execute([$sessionId, $userId]);
    
    return true;
}

// Gestione delle richieste API
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    
    switch ($_GET['action'] ?? '') {
        case 'add':
            $result = addToCart(
                $data['prodotto_id'], 
                $data['prezzo'], 
                $data['varianti'] ?? null, 
                $data['personalizzazioni'] ?? null,
                $data['tipo'] ?? 'catalogo'
            );
            echo json_encode(['success' => true, 'id' => $result]);
            break;
            
        case 'remove':
            $result = removeFromCart($data['id']);
            echo json_encode(['success' => $result]);
            break;
            
        case 'empty':
            $result = emptyCart();
            echo json_encode(['success' => $result]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Azione non valida']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    switch ($_GET['action'] ?? '') {
        case 'get':
            $cart = getCartContents();
            echo json_encode(['success' => true, 'prodotti' => $cart]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Azione non valida']);
    }
}
?>