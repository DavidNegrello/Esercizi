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

// Gestisci le diverse azioni del carrello
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        // Aggiungi un prodotto al carrello
        if (!isset($_POST['product_id']) || !isset($_POST['price'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dati mancanti']);
            exit;
        }
        
        $productId = (int)$_POST['product_id'];
        $price = (float)$_POST['price'];
        $quantity = (int)($_POST['quantity'] ?? 1);
        $variants = $_POST['variants'] ?? [];
        $type = $_POST['type'] ?? 'catalogo';
        
        $result = $cart->addProduct($productId, $price, $quantity, $variants, $type);
        echo json_encode(['success' => $result, 'count' => $cart->getCartCount()]);
        break;
        
    case 'update':
        // Aggiorna la quantità di un prodotto
        if (!isset($_POST['item_id']) || !isset($_POST['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dati mancanti']);
            exit;
        }
        
        $itemId = (int)$_POST['item_id'];
        $quantity = (int)$_POST['quantity'];
        
        $result = $cart->updateProductQuantity($itemId, $quantity);
        echo json_encode([
            'success' => $result, 
            'count' => $cart->getCartCount(),
            'total' => $cart->getCartTotal()
        ]);
        break;
        
    case 'remove':
        // Rimuovi un prodotto dal carrello
        if (!isset($_POST['item_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID elemento mancante']);
            exit;
        }
        
        $itemId = (int)$_POST['item_id'];
        
        $result = $cart->removeProduct($itemId);
        echo json_encode([
            'success' => $result, 
            'count' => $cart->getCartCount(),
            'total' => $cart->getCartTotal()
        ]);
        break;
        
    case 'get':
        // Ottieni il contenuto del carrello
        $items = $cart->getCartContents();
        $total = $cart->getCartTotal();
        $count = $cart->getCartCount();
        
        echo json_encode([
            'items' => $items,
            'total' => $total,
            'count' => $count
        ]);
        break;
        
    case 'clear':
        // Svuota il carrello
        $result = $cart->clearCart();
        echo json_encode(['success' => $result]);
        break;
        
    case 'count':
        // Ottieni solo il conteggio degli elementi nel carrello
        echo json_encode(['count' => $cart->getCartCount()]);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Azione non valida']);
        break;
}
?>