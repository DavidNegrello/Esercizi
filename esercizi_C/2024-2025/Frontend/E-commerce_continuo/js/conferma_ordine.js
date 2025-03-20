document.addEventListener("DOMContentLoaded", function() {
    // Recupera gli ordini dal localStorage
    const ordini = JSON.parse(localStorage.getItem("ordini")) || [];
    
    // Se non ci sono ordini, reindirizza alla home
    if (ordini.length === 0) {
        alert("Nessun ordine trovato.");
        window.location.href = "../index.html";
        return;
    }
    
    // Prendi l'ultimo ordine
    const ultimoOrdine = ordini[ordini.length - 1];
    
    // Genera un numero d'ordine casuale
    const numeroOrdine = "ORD-" + Math.floor(100000 + Math.random() * 900000);
    
    // Formatta la data
    const dataOrdine = new Date(ultimoOrdine.data);
    const dataFormattata = dataOrdine.toLocaleDateString("it-IT", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
    
    // Popola i dettagli dell'ordine
    document.getElementById("order-number").textContent = numeroOrdine;
    document.getElementById("order-date").textContent = dataFormattata;
    document.getElementById("order-name").textContent = ultimoOrdine.utente.nome;
    document.getElementById("order-email").textContent = ultimoOrdine.utente.email;
    document.getElementById("order-address").textContent = ultimoOrdine.utente.indirizzo;
    document.getElementById("order-payment").textContent = getPaymentMethod(ultimoOrdine.metodoPagamento);
    document.getElementById("order-total").textContent = `${ultimoOrdine.totale.toFixed(2)}€`;
    
    // Funzione per ottenere il metodo di pagamento in formato leggibile
    function getPaymentMethod(method) {
        switch (method) {
            case "carta":
                return "Carta di Credito";
            case "paypal":
                return "PayPal";
            case "bonifico":
                return "Bonifico Bancario";
            default:
                return method;
        }
    }
});