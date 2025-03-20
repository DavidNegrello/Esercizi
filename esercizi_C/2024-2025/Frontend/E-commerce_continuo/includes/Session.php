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
        
        // Trasferisci il carrello dalla sessione all'utente
        $this->transferCartFromSessionToUser();
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
        
        // Aggiorna gli elementi del carrello dalla sessione all'utente
        $stmt = $this->pdo->prepare("
            UPDATE carrello 
            SET utente_id = :user_id 
            WHERE sessione_id = :session_id AND utente_id IS NULL AND attivo = TRUE
        ");
        $stmt->execute([
            'user_id' => $this->userId,
            'session_id' => $this->sessionId
        ]);
        
        return true;
    }
}
?>