<?php
class Session {
    private $pdo;
    private $sessionId;
    private $userId = null;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        
        // Avvia la sessione PHP
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Controlla se esiste già un ID sessione
        if (!isset($_SESSION['custom_session_id'])) {
            $_SESSION['custom_session_id'] = $this->generateSessionId();
            $this->createSession($_SESSION['custom_session_id']);
        }
        
        $this->sessionId = $_SESSION['custom_session_id'];
        
        // Controlla se l'utente è loggato
        if (isset($_SESSION['user_id'])) {
            $this->userId = $_SESSION['user_id'];
            $this->updateSession();
        }
    }
    
    private function generateSessionId() {
        return bin2hex(random_bytes(16));
    }
    
    private function createSession($sessionId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO sessioni (id, data_scadenza) 
            VALUES (:id, DATE_ADD(NOW(), INTERVAL 30 DAY))
        ");
        $stmt->execute(['id' => $sessionId]);
        
        return $sessionId;
    }
    
    private function updateSession() {
        $stmt = $this->pdo->prepare("
            UPDATE sessioni 
            SET utente_id = :user_id, data_scadenza = DATE_ADD(NOW(), INTERVAL 30 DAY) 
            WHERE id = :session_id
        ");
        $stmt->execute([
            'user_id' => $this->userId,
            'session_id' => $this->sessionId
        ]);
    }
    
    public function getSessionId() {
        return $this->sessionId;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function setUserId($userId) {
        $this->userId = $userId;
        $_SESSION['user_id'] = $userId;
        $this->updateSession();
    }
    
    public function clearUserId() {
        $this->userId = null;
        unset($_SESSION['user_id']);
        
        $stmt = $this->pdo->prepare("
            UPDATE sessioni 
            SET utente_id = NULL 
            WHERE id = :session_id
        ");
        $stmt->execute(['session_id' => $this->sessionId]);
    }
    
    public function transferCartFromSessionToUser() {
        if (!$this->userId) {
            return false;
        }
        
        // Trova il carrello della sessione
        $stmt = $this->pdo->prepare("
            SELECT id FROM carrello 
            WHERE sessione_id = :session_id AND utente_id IS NULL AND attivo = TRUE
        ");
        $stmt->execute(['session_id' => $this->sessionId]);
        $sessionCart = $stmt->fetch();
        
        if (!$sessionCart) {
            return false;
        }
        
        // Trova il carrello dell'utente
        $stmt = $this->pdo->prepare("
            SELECT id FROM carrello 
            WHERE utente_id = :user_id AND attivo = TRUE
        ");
        $stmt->execute(['user_id' => $this->userId]);
        $userCart = $stmt->fetch();
        
        if ($userCart) {
            // Se l'utente ha già un carrello, trasferisci gli elementi dal carrello della sessione
            $stmt = $this->pdo->prepare("
                UPDATE elementi_carrello 
                SET carrello_id = :user_cart_id 
                WHERE carrello_id = :session_cart_id
            ");
            $stmt->execute([
                'user_cart_id' => $userCart['id'],
                'session_cart_id' => $sessionCart['id']
            ]);
            
            // Disattiva il carrello della sessione
            $stmt = $this->pdo->prepare("
                UPDATE carrello 
                SET attivo = FALSE 
                WHERE id = :cart_id
            ");
            $stmt->execute(['cart_id' => $sessionCart['id']]);
        } else {
            // Se l'utente non ha un carrello, associa il carrello della sessione all'utente
            $stmt = $this->pdo->prepare("
                UPDATE carrello 
                SET utente_id = :user_id 
                WHERE id = :cart_id
            ");
            $stmt->execute([
                'user_id' => $this->userId,
                'cart_id' => $sessionCart['id']
            ]);
        }
        
        return true;
    }
}
?>