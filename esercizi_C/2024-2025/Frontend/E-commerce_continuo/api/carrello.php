<?php
require_once '../config/database.php';
require_once '../includes/Session.php';
require_once '../includes/Cart.php';

// Inizializza la sessione
$session = new Session($pdo);
$cart = new Cart($pdo, $session->getSessionId(), $session->getUserId());

// Gestisci le diverse operazioni sul carrello
$action = $_POST['action'] ?? $_GET['action'] ?? 'get';

switch ($action) {
    case 'add':
        // Aggiungi un prodotto al carrello
        $productId = $_POST['product_id'] ?? null;
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        $variants = $_POST['variants'] ?? [];
        $type = $_POST['type'] ?? 'catalogo';
        
        if (!$productId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID prodotto mancante']);
            exit;
        }
        
        $result = $cart->addProduct($productId, $price, $quantity, $variants, $type);
        echo json_encode(['success' => $result]);
        break;
        
    case 'add_bundle':
        // Aggiungi un bundle al carrello
        $bundleId = $_POST['bundle_id'] ?? null;
        
        if (!$bundleId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID bundle mancante']);
            exit;
        }
        
        // Ottieni i dettagli del bundle
        $stmt = $pdo->prepare("SELECT * FROM bundle WHERE id = :id");
        $stmt->execute(['id' => $bundleId]);
        $bundle = $stmt->fetch();
        
        if (!$bundle) {
            http_response_code(404);
            echo json_encode(['error' => 'Bundle non trovato']);
            exit;
        }
        
        // Aggiungi il bundle come prodotto speciale
        $result = $cart->addProduct(
            $bundleId, 
            $bundle['prezzo_scontato'], 
            1, 
            ['prodotti' => $bundle['prodotti']], 
            'bundle'
        );
        
        echo json_encode(['success' => $result]);
        break;
        
    case 'update':
        // Aggiorna la quantità di un prodotto
        $itemId = $_POST['item_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID elemento mancante']);
            exit;
        }
        
        $result = $cart->updateQuantity($itemId, $quantity);
        echo json_encode(['success' => $result]);
        break;
        
    case 'remove':
        // Rimuovi un prodotto dal carrello
        $itemId = $_POST['item_id'] ?? null;
        
        if (!$itemId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID elemento mancante']);
            exit;
        }
        
        $result = $cart->removeItem($itemId);
        echo json_encode(['success' => $result]);
        break;
        
    case 'clear':
        // Svuota il carrello
        $result = $cart->clearCart();
        echo json_encode(['success' => $result]);
        break;
        
    case 'count':
        // Ottieni il numero di elementi nel carrello
        $count = $cart->getCartCount();
        echo json_encode(['count' => $count]);
        break;
        
    case 'get':
    default:
        // Ottieni tutti gli elementi del carrello
        $items = $cart->getCartItems();
        $total = $cart->getCartTotal();
        
        // Per ogni elemento di tipo bundle, aggiungi informazioni aggiuntive
        foreach ($items as &$item) {
            if ($item['tipo'] === 'bundle') {
                // Ottieni i dettagli del bundle
                $stmt = $pdo->prepare("SELECT nome, immagine FROM bundle WHERE id = :id");
                $stmt->execute(['id' => $item['prodotto_id']]);
                $bundle = $stmt->fetch();
                
                if ($bundle) {
                    $item['nome'] = $bundle['nome'];
                    $item['immagine_principale'] = $bundle['immagine'];
                }
                
                // Ottieni i dettagli dei prodotti nel bundle
                $prodotti_ids = json_decode($item['varianti'], true)['prodotti'] ?? [];
                $prodotti_bundle = [];
                
                if (!empty($prodotti_ids)) {
                    $prodotti_ids = json_decode($prodotti_ids);
                    $placeholders = implode(',', array_fill(0, count($prodotti_ids), '?'));
                    
                    $stmt = $pdo->prepare("SELECT id, nome, categoria FROM prodotti WHERE id IN ($placeholders)");
                    $stmt->execute($prodotti_ids);
                    $prodotti_bundle = $stmt->fetchAll();
                }
                
                $item['prodotti_bundle'] = $prodotti_bundle;
            }
        }
        
        echo json_encode([
            'items' => $items,
            'total' => $total
        ]);
        break;
}
?>