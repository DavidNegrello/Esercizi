<?php
// Includi la configurazione del database e le classi necessarie
require_once '../config/database.php';
require_once '../includes/Session.php';
require_once '../includes/Cart.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Inizializza la sessione
$session = new Session($pdo);
$cart = new Cart($pdo, $session->getSessionId(), $session->getUserId());

// Verifica che i dati dell'ordine siano stati forniti
if (!isset($_POST['nome']) || !isset($_POST['indirizzo']) || !isset($_POST['email']) || !isset($_POST['metodo_pagamento'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dati mancanti per l\'ordine']);
    exit;
}

// Ottieni i dati dell'ordine
$nome = $_POST['nome'];
$indirizzo = $_POST['indirizzo'];
$email = $_POST['email'];
$metodoPagamento = $_POST['metodo_pagamento'];
$couponCode = $_POST['coupon'] ?? null;

try {
    // Inizia una transazione
    $pdo->beginTransaction();
    
    // Ottieni il contenuto del carrello
    $items = $cart->getCartContents();
    $total = $cart->getCartTotal();
    
    // Se non ci sono elementi nel carrello, restituisci un errore
    if (empty($items)) {
        http_response_code(400);
        echo json_encode(['error' => 'Il carrello è vuoto']);
        $pdo->rollBack();
        exit;
    }
    
    // Verifica e applica il coupon se fornito
    $couponId = null;
    $discountAmount = 0;
    
    if ($couponCode) {
        $stmt = $pdo->prepare("
            SELECT id, percentuale_sconto
            FROM coupon
            WHERE codice = :code 
              AND attivo = TRUE
              AND (data_inizio IS NULL OR data_inizio <= CURDATE())
              AND (data_fine IS NULL OR data_fine >= CURDATE())
        ");
        $stmt->execute(['code' => $couponCode]);
        $coupon = $stmt->fetch();
        
        if ($coupon) {
            $couponId = $coupon['id'];
            $discountAmount = $total * ($coupon['percentuale_sconto'] / 100);
            $total -= $discountAmount;
        }
    }
    
    // Crea l'ordine
    $stmt = $pdo->prepare("
        INSERT INTO ordini (utente_id, sessione_id, nome_cliente, indirizzo, email, metodo_pagamento, totale, coupon_id)
        VALUES (:user_id, :session_id, :nome, :indirizzo, :email, :metodo_pagamento, :totale, :coupon_id)
    ");
    $stmt->execute([
        'user_id' => $session->getUserId(),
        'session_id' => $session->getSessionId(),
        'nome' => $nome,
        'indirizzo' => $indirizzo,
        'email' => $email,
        'metodo_pagamento' => $metodoPagamento,
        'totale' => $total,
        'coupon_id' => $couponId
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // Aggiungi i dettagli dell'ordine
    foreach ($items as $item) {
        $stmt = $pdo->prepare("
            INSERT INTO dettagli_ordine (ordine_id, prodotto_id, nome_prodotto, prezzo_unitario, quantita, varianti_json, tipo)
            VALUES (:ordine_id, :prodotto_id, :nome_prodotto, :prezzo_unitario, :quantita, :varianti_json, :tipo)
        ");
        $stmt->execute([
            'ordine_id' => $orderId,
            'prodotto_id' => $item['prodotto_id'],
            'nome_prodotto' => $item['nome'],
            'prezzo_unitario' => $item['prezzo_unitario'],
            'quantita' => $item['quantita'],
            'varianti_json' => json_encode($item['varianti']),
            'tipo' => $item['tipo']
        ]);
    }
    
    // Svuota il carrello
    $cart->clearCart();
    
    // Commit della transazione
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Ordine completato con successo!'
    ]);
    
} catch (PDOException $e) {
    // Rollback in caso di errore
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Errore nell\'elaborazione dell\'ordine: ' . $e->getMessage()]);
}
?>