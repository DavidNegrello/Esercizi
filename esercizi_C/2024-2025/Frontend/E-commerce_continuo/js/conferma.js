document.addEventListener("DOMContentLoaded", function () {
    // Ottieni l'ID dell'ordine dall'URL
    const params = new URLSearchParams(window.location.search);
    const orderId = params.get("order_id");
    
    if (!orderId) {
        // Se non c'è un ID ordine, mostra un messaggio di errore
        document.querySelector(".card-body").innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <h2>Errore</h2>
                <p>Nessun ordine trovato. Torna al <a href="catalogo.html">catalogo</a> per continuare lo shopping.</p>
            </div>
        `;
        return;
    }
    
    // Carica i dettagli dell'ordine
    fetch(`../api/ordine.php?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Popola i dettagli dell'ordine
            document.getElementById("order-id").textContent = `#${data.id}`;
            document.getElementById("order-date").textContent = new Date(data.data_ordine).toLocaleDateString();
            document.getElementById("order-total").textContent = `${data.totale.toFixed(2)}€`;
            document.getElementById("payment-method").textContent = data.metodo_pagamento;
            
            // Popola le informazioni di spedizione
            document.getElementById("customer-name").textContent = data.nome_cliente;
            document.getElementById("shipping-address").textContent = data.indirizzo;
            document.getElementById("customer-email").textContent = data.email;
            
            // Calcola la data di consegna stimata (7-10 giorni lavorativi)
            const oggi = new Date();
            const consegnaMin = new Date(oggi);
            consegnaMin.setDate(oggi.getDate() + 7);
            const consegnaMax = new Date(oggi);
            consegnaMax.setDate(oggi.getDate() + 10);
            
            document.getElementById("delivery-date").textContent = `${consegnaMin.toLocaleDateString()} - ${consegnaMax.toLocaleDateString()}`;
            
            // Gestisci il pulsante di stampa
            document.getElementById("print-button").addEventListener("click", function(e) {
                e.preventDefault();
                window.print();
            });
        })
        .catch(error => {
            console.error("Errore nel caricamento dei dettagli dell'ordine:", error);
            document.querySelector(".card-body").innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <h2>Errore</h2>
                    <p>Si è verificato un errore nel caricamento dei dettagli dell'ordine. Torna al <a href="catalogo.html">catalogo</a> per continuare lo shopping.</p>
                </div>
            `;
        });
});