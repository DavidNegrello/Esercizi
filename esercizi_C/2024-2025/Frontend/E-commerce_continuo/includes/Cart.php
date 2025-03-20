<?php
class Cart {
    private $pdo;
    private $sessionId;
    private $userId;
    
    public function __construct($pdo, $sessionId, $userId = null) {
        $this->pdo = $pdo;
        $this->sessionId = $sessionId;
        $this->userId = $userId;
    }
    
    public function addProduct($productId, $price, $quantity = 1, $variants = [], $type = 'catalogo') {
        // Controlla se il prodotto è già nel carrello
        $params = [
            'product_id' => $productId,
            'type' => $type
        ];
        
        $sql = "SELECT id, quantita FROM carrello WHERE prodotto_id = :product_id AND tipo = :type AND attivo = TRUE AND ";
        
        if ($this->userId) {
            $sql .= "utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "sessione_id = :session_id AND utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $item = $stmt->fetch();
        
        if ($item) {
            // Aggiorna la quantità se il prodotto è già nel carrello
            $newQuantity = $item['quantita'] + $quantity;
            $stmt = $this->pdo->prepare("
                UPDATE carrello 
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
                INSERT INTO carrello (sessione_id, utente_id, prodotto_id, quantita, prezzo_unitario, varianti, tipo) 
                VALUES (:session_id, :user_id, :product_id, :quantity, :price, :variants, :type)
            ");
            $stmt->execute([
                'session_id' => $this->sessionId,
                'user_id' => $this->userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variants' => json_encode($variants),
                'type' => $type
            ]);
        }
        
        return true;
    }
    
    public function getCartItems() {
        $params = [];
        $sql = "
            SELECT c.*, p.nome, p.immagine_principale 
            FROM carrello c
            LEFT JOIN prodotti p ON c.prodotto_id = p.id
            WHERE c.attivo = TRUE AND 
        ";
        
        if ($this->userId) {
            $sql .= "c.utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "c.sessione_id = :session_id AND c.utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function updateQuantity($itemId, $quantity) {
        $params = [
            'id' => $itemId,
            'quantity' => $quantity
        ];
        
        $sql = "UPDATE carrello SET quantita = :quantity WHERE id = :id AND attivo = TRUE AND ";
        
        if ($this->userId) {
            $sql .= "utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "sessione_id = :session_id AND utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function removeItem($itemId) {
        $params = [
            'id' => $itemId
        ];
        
        $sql = "UPDATE carrello SET attivo = FALSE WHERE id = :id AND ";
        
        if ($this->userId) {
            $sql .= "utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "sessione_id = :session_id AND utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getCartTotal() {
        $params = [];
        $sql = "
            SELECT SUM(quantita * prezzo_unitario) as totale
            FROM carrello
            WHERE attivo = TRUE AND 
        ";
        
        if ($this->userId) {
            $sql .= "utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "sessione_id = :session_id AND utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['totale'] ?? 0;
    }
    
    public function clearCart() {
        $params = [];
        $sql = "UPDATE carrello SET attivo = FALSE WHERE attivo = TRUE AND ";
        
        if ($this->userId) {
            $sql .= "utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "sessione_id = :session_id AND utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getCartCount() {
        $params = [];
        $sql = "
            SELECT COUNT(*) as count
            FROM carrello
            WHERE attivo = TRUE AND 
        ";
        
        if ($this->userId) {
            $sql .= "utente_id = :user_id";
            $params['user_id'] = $this->userId;
        } else {
            $sql .= "sessione_id = :session_id AND utente_id IS NULL";
            $params['session_id'] = $this->sessionId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] ?? 0;
    }
}
?>