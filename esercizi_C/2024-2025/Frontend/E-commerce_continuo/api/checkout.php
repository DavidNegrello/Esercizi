<?php
require_once '../config/database.php';
require_once 'session.php';
require_once 'carrello.php';

// Funzione per verificare un coupon
function verifyCoupon($code) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT id, percentuale_sconto 
        FROM coupon 
        WHERE codice = ? 
        AND (data_scadenza IS NULL OR data_scadenza > NOW())
    ");
    $stmt->execute([$code]);
    
    return $stmt->fetch();
}

// Funzione per creare un nuovo ordine
function createOrder($customerData, $couponId = null) {
    global $pdo;
    $sessionId = getSessionId();
    $userId = getCurrentUserId();
    
    // Ottieni i prodotti dal carrello
    $cartProducts = getCartContents();
    
    if (empty($cartProducts)) {
        return ['success' => false, 'error' => 'Il carrello è vuoto'];
    }
    
    // Calcola il totale
    $total = 0;
    foreach ($cartProducts as $product) {
        $total += floatval($product['prezzo']);
    }
    
    // Applica lo sconto del coupon se presente
    $discount = 0;
    if ($couponId) {
        $stmt = $pdo->prepare("SELECT percentuale_sconto FROM coupon WHERE id = ?");
        $stmt->execute([$couponId]);
        $coupon = $stmt->fetch();
        
        if ($coupon) {
            $discount = $total * ($coupon['percentuale_sconto'] / 100);
            $total -= $discount;
        }
    }
    
    try {
        // Inizia la transazione
        $pdo->beginTransaction();
        
        // Crea l'ordine
        $stmt = $pdo->prepare("
            INSERT INTO ordini 
            (utente_id, sessione_id, nome, indirizzo, email, metodo_pagamento, totale, sconto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $sessionId,
            $customerData['nome'],
            $customerData['indirizzo'],
            $customerData['email'],
            $customerData['metodo_pagamento'],
            $total,
            $discount
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Aggiungi i prodotti all'ordine
        foreach ($cartProducts as $product) {
            $stmt = $pdo->prepare("
                INSERT INTO ordini_prodotti 
                (ordine_id, prodotto_id, nome_prodotto, prezzo_unitario, varianti_selezionate, personalizzazioni) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $orderId,
                $product['prodotto_id'],
                $product['nome'],
                $product['prezzo'],
                isset($product['varianti']) ? json_encode($product['varianti']) : null,
                isset($product['personalizzazioni']) ? json_encode($product['personalizzazioni']) : null
            ]);
        }
        
        // Svuota il carrello
        emptyCart();
        
        // Commit della transazione
        $pdo->commit();
        
        return ['success' => true, 'order_id' => $orderId];
    } catch (Exception $e) {
        // Rollback in caso di errore
        $pdo->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Gestione delle richieste API
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    
    switch ($_GET['action'] ?? '') {
        case 'coupon':
            $couponCode = $data['coupon'] ?? '';
            $coupon = verifyCoupon($couponCode);
            
            if ($coupon) {
                echo json_encode([
                    'success' => true, 
                    'coupon_id' => $coupon['id'],
                    'percentuale_sconto' => $coupon['percentuale_sconto']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Coupon non valido o scaduto']);
            }
            break;
            
        case 'order':
            $customerData = $data['customer'] ?? [];
            $couponId = $data['coupon_id'] ?? null;
            
            if (empty($customerData['nome']) || empty($customerData['indirizzo']) || 
                empty($customerData['email']) || empty($customerData['metodo_pagamento'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Dati cliente incompleti']);
                break;
            }
            
            $result = createOrder($customerData, $couponId);
            echo json_encode($result);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Azione non valida']);
    }
}
?>