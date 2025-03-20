<?php
class Cart {
    private $pdo;
    private $sessionId;
    private $userId;
    private $cartId;
    
    public function __construct($pdo, $sessionId, $userId = null) {
        $this->pdo = $pdo;
        $this->sessionId = $sessionId;
        $this->userId = $userId;
        
        // Ottieni o crea il carrello
        $this->cartId = $this->getOrCreateCart();
    }
    
    private function getOrCreateCart() {
        // Cerca un carrello attivo per l'utente o la sessione
        $params = [];
        $sql = "SELECT id FROM carrello WHERE attivo = TRUE AND ";
        
        if ($this->userId) {
            $sql .= "utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "sessione_id = :session_id AND utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $cart = $stmt->fetch();
        
        if ($cart) {
            return $cart['id'];
        }
        
        // Crea un nuovo carrello se non esiste
        $sql = "INSERT INTO carrello (sessione_id, utente_id) VALUES (:session_id, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'session_id' => $this->sessionId,
            'user_id' => $this->userId
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    public function addProduct($productId, $price, $quantity = 1, $variants = [], $type = 'catalogo') {
        // Controlla se il prodotto è già nel carrello
        $stmt = $this->pdo->prepare("
            SELECT id, quantita FROM elementi_carrello 
            WHERE carrello_id = :cart_id AND prodotto_id = :product_id AND tipo = :type
        ");
        $stmt->execute([
            'cart_id' => $this->cartId,
            'product_id' => $productId,
            'type' => $type
        ]);
        $item = $stmt->fetch();
        
        if ($item) {
            // Aggiorna la quantità se il prodotto è già nel carrello
            $newQuantity = $item['quantita'] + $quantity;
            $stmt = $this->pdo->prepare("
                UPDATE elementi_carrello 
                SET quantita = :quantity 
                WHERE id = :id
            ");
            $stmt->execute([
                'quantity' => $newQuantity,
                'id' => $item['id']
            ]);
        } else {
            // Aggiungi il nuovo prodotto al carrello
            $stmt = $this->pdo->prepare("
                INSERT INTO elementi_carrello (carrello_id, prodotto_id, quantita, prezzo_unitario, varianti_json, tipo) 
                VALUES (:cart_id, :product_id, :quantity, :price, :variants, :type)
            ");
            $stmt->execute([
                'cart_id' => $this->cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variants' => json_encode($variants),
                'type' => $type
            ]);
        }
        
        return true;
    }
    
    public function updateProductQuantity($itemId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeProduct($itemId);
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE elementi_carrello 
            SET quantita = :quantity 
            WHERE id = :id AND carrello_id = :cart_id
        ");
        $stmt->execute([
            'quantity' => $quantity,
            'id' => $itemId,
            'cart_id' => $this->cartId
        ]);
        
        return $stmt->rowCount() > 0;
    }
    
    public function removeProduct($itemId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM elementi_carrello 
            WHERE id = :id AND carrello_id = :cart_id
        ");
        $stmt->execute([
            'id' => $itemId,
            'cart_id' => $this->cartId
        ]);
        
        return $stmt->rowCount() > 0;
    }
    
    public function getCartContents() {
        $stmt = $this->pdo->prepare("
            SELECT ec.id, ec.prodotto_id, p.nome, ec.prezzo_unitario, ec.quantita, 
                   ec.varianti_json, ec.tipo, p.immagine_principale as immagine
            FROM elementi_carrello ec
            LEFT JOIN prodotti p ON ec.prodotto_id = p.id
            WHERE ec.carrello_id = :cart_id
            ORDER BY ec.id DESC
        ");
        $stmt->execute(['cart_id' => $this->cartId]);
        
        $items = $stmt->fetchAll();
        
        // Decodifica le varianti JSON
        foreach ($items as &$item) {
            $item['varianti'] = json_decode($item['varianti_json'], true);
            unset($item['varianti_json']); // Rimuovi il campo JSON grezzo
        }
        
        return $items;
    }
    
    public function getCartTotal() {
        $stmt = $this->pdo->prepare("
            SELECT SUM(prezzo_unitario * quantita) as total
            FROM elementi_carrello
            WHERE carrello_id = :cart_id
        ");
        $stmt->execute(['cart_id' => $this->cartId]);
        
        $result = $stmt->fetch();
        return $result['total'] ?: 0;
    }
    
    public function getCartCount() {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM elementi_carrello
            WHERE carrello_id = :cart_id
        ");
        $stmt->execute(['cart_id' => $this->cartId]);
        
        $result = $stmt->fetch();
        return $result['count'] ?: 0;
    }
    
    public function clearCart() {
        $stmt = $this->pdo->prepare("
            DELETE FROM elementi_carrello
            WHERE carrello_id = :cart_id
        ");
        $stmt->execute(['cart_id' => $this->cartId]);
        
        return $stmt->rowCount() > 0;
    }
    
    public function getCartId() {
        return $this->cartId;
    }
}
?>