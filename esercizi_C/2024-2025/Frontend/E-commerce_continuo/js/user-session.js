/**
 * Gestione dell'ID utente e dell'ID sessione
 * Quando l'utente non è loggato, si utilizza l'ID sessione
 * Quando l'utente si logga, si sostituisce l'ID sessione con l'ID utente
 */

document.addEventListener("DOMContentLoaded", function() {
    // Controlla se esiste già un ID sessione
    let sessionId = localStorage.getItem("sessionId");
    if (!sessionId) {
        // Genera un nuovo ID sessione casuale
        sessionId = generateUniqueId();
        localStorage.setItem("sessionId", sessionId);
    }
    
    // Funzione per generare un ID univoco
    function generateUniqueId() {
        return 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Funzione per ottenere l'ID corrente (utente o sessione)
    window.getCurrentId = function() {
        const userId = localStorage.getItem("userId");
        return userId || sessionId;
    };
    
    // Funzione per aggiornare l'ID utente quando si effettua il login
    window.setUserId = function(userId) {
        localStorage.setItem("userId", userId);
        
        // Aggiorna i carrelli con il nuovo ID utente
        updateCartsWithUserId(userId);
    };
    
    // Funzione per effettuare il logout
    window.userLogout = function() {
        localStorage.removeItem("userId");
    };
    
    // Funzione per aggiornare i carrelli con il nuovo ID utente
    function updateCartsWithUserId(userId) {
        // Aggiorna il carrello preassemblato
        let carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
        carrelloPreassemblato.userId = userId;
        localStorage.setItem("carrelloPreassemblato", JSON.stringify(carrelloPreassemblato));
        
        // Aggiorna il carrello catalogo
        let carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
        carrelloCatalogo.userId = userId;
        localStorage.setItem("carrelloCatalogo", JSON.stringify(carrelloCatalogo));
    }
    
    // Aggiorna i carrelli con l'ID corrente (sessione o utente)
    const currentId = window.getCurrentId();
    updateCartsWithUserId(currentId);
});