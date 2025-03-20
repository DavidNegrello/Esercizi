<?php
require_once '../config/database.php';
require_once '../includes/Session.php';
require_once '../includes/Cart.php';

// Inizializza la sessione
$session = new Session($pdo);
$cart = new Cart($pdo, $session->getSessionId(), $session->getUserId());

// Verifica che ci siano dati POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo non consentito']);
    exit;
}

// Ottieni i dati dell'ordine
$nome = $_POST['nome'] ?? null;
$indirizzo = $_POST['indirizzo'] ?? null;
$email = $_POST['email'] ?? null;
$metodoPagamento = $_POST['metodo_pagamento'] ?? null;
$couponId = $_POST['coupon_id'] ?? null;

// Verifica che tutti i campi obbligatori siano presenti
if (!$nome || !$indirizzo || !$email || !$metodoPagamento) {
    http_response_code(400);
    echo json_encode(['error' => 'Dati mancanti']);
    exit;
}

// Ottieni gli elementi del carrello
$items = $cart->getCartItems();
$total = $cart->getCartTotal();

// Applica lo sconto del coupon se presente
if ($couponId) {
    $stmt = $pdo->prepare("SELECT percentuale_sconto FROM coupon WHERE id = :id AND attivo = TRUE");
    $stmt->execute(['id' => $couponId]);
    $coupon = $stmt->fetch();
    
    if ($coupon) {
        $total = $total * (1 - ($coupon['percentuale_sconto'] / 100));
    }
}

// Crea l'ordine
$stmt = $pdo->prepare("
    INSERT INTO ordini (
        utente_id, sessione_id, nome_cliente, indirizzo, email, 
        metodo_pagamento, totale, coupon_id, prodotti
    ) VALUES (
        :utente_id, :sessione_id, :nome, :indirizzo, :email, 
        :metodo_pagamento, :totale, :coupon_id, :prodotti
    )
");

$stmt->execute([
    'utente_id' => $session->getUserId(),
    'sessione_id' => $session->getSessionId(),
    'nome' => $nome,
    'indirizzo' => $indirizzo,
    'email' => $email,
    'metodo_pagamento' => $metodoPagamento,
    'totale' => $total,
    'coupon_id' => $couponId,
    'prodotti' => json_encode($items)
]);

$ordineId = $pdo->lastInsertId();

// Svuota il carrello
$cart->clearCart();

// Restituisci l'ID dell'ordine
echo json_encode([
    'success' => true,
    'order_id' => $ordineId
]);
?>