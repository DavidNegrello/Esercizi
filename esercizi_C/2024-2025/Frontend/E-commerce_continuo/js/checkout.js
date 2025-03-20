document.addEventListener("DOMContentLoaded", function () {
    // Carica i dati del carrello
    const carrelloAcquisto = JSON.parse(localStorage.getItem("carrelloAcquisto")) || [];
    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");
    const totaleScontoElement = document.getElementById("totale-sconto");
    
    // Variabili per il totale e lo sconto
    let totale = 0;
    let sconto = 0;
    
    // Mostra i prodotti del carrello
    if (carrelloAcquisto.length === 0) {
        carrelloContainer.innerHTML = "<p>Il carrello è vuoto.</p>";
        document.querySelector("button[type='submit']").disabled = true;
    } else {
        // Mostra ogni prodotto nel riepilogo
        carrelloAcquisto.forEach((prodotto, index) => {
            // Calcola il prezzo del prodotto
            let prezzoProdotto = (typeof prodotto.prezzo === "string") 
                ? prodotto.prezzo.replace('€', '').replace(',', '.') 
                : prodotto.prezzo.toString().replace('€', '').replace(',', '.');
            prezzoProdotto = parseFloat(prezzoProdotto);
            
            // Aggiungi le personalizzazioni al prezzo
            if (prodotto.personalizzazioni && prodotto.personalizzazioni.length > 0) {
                prodotto.personalizzazioni.forEach(p => {
                    prezzoProdotto += p.prezzo;
                });
            }
            
            // Aggiungi al totale
            totale += prezzoProdotto;
            
            // Crea l'HTML per il prodotto
            const prodottoHTML = `
                <div class="checkout-item d-flex align-items-center mb-2">
                    <img src="${prodotto.immagine}" alt="${prodotto.nome}" class="checkout-item-img me-3">
                    <div class="checkout-item-details flex-grow-1">
                        <h6 class="mb-0">${prodotto.nome}</h6>
                        <p class="text-muted small mb-0">${prezzoProdotto.toFixed(2)}€</p>
                    </div>
                </div>
            `;
            
            carrelloContainer.innerHTML += prodottoHTML;
        });
        
        // Aggiorna i totali
        totalePrezzoElement.textContent = `${totale.toFixed(2)}€`;
        totaleScontoElement.textContent = `${totale.toFixed(2)}€`; // Inizialmente uguale al totale
    }
    
    // Gestione del coupon
    document.getElementById("applica-coupon").addEventListener("click", function () {
        const couponCode = document.getElementById("coupon").value.trim();
        const messaggioCoupon = document.getElementById("messaggio-coupon");
        
        if (!couponCode) {
            messaggioCoupon.textContent = "Inserisci un codice coupon.";
            messaggioCoupon.style.color = "red";
            return;
        }
        
        // Verifica il codice coupon (in un'implementazione reale, questo dovrebbe essere verificato sul server)
        let percentualeSconto = 0;
        
        switch (couponCode.toLowerCase()) {
            case "sconto10":
                percentualeSconto = 10;
                break;
            case "sconto20":
                percentualeSconto = 20;
                break;
            case "sconto30":
                percentualeSconto = 30;
                break;
            default:
                messaggioCoupon.textContent = "Codice coupon non valido.";
                messaggioCoupon.style.color = "red";
                return;
        }
        
        // Calcola lo sconto
        sconto = (totale * percentualeSconto) / 100;
        const totaleConSconto = totale - sconto;
        
        // Aggiorna il totale con lo sconto
        totaleScontoElement.textContent = `${totaleConSconto.toFixed(2)}€`;
        
        // Mostra il messaggio di successo
        messaggioCoupon.textContent = `Coupon applicato! Sconto del ${percentualeSconto}% (${sconto.toFixed(2)}€)`;
        messaggioCoupon.style.color = "green";
    });
    
    // Gestione dell'invio del form
    document.getElementById("checkout-form").addEventListener("submit", function (event) {
        event.preventDefault();
        
        // Raccogli i dati del form
        const nome = document.getElementById("nome").value;
        const indirizzo = document.getElementById("indirizzo").value;
        const email = document.getElementById("email").value;
        const metodoPagamento = document.getElementById("metodo-pagamento").value;
        
        // Crea l'oggetto ordine
        const ordine = {
            utente: {
                nome: nome,
                indirizzo: indirizzo,
                email: email
            },
            metodoPagamento: metodoPagamento,
            prodotti: carrelloAcquisto,
            totale: parseFloat(totaleScontoElement.textContent),
            sconto: sconto,
            data: new Date().toISOString(),
            sessionId: window.getCurrentId()
        };
        
        // In un'implementazione reale, qui invieresti l'ordine al server
        // Per ora, salviamo l'ordine nel localStorage
        const ordini = JSON.parse(localStorage.getItem("ordini")) || [];
        ordini.push(ordine);
        localStorage.setItem("ordini", JSON.stringify(ordini));
        
        // Svuota il carrello (disattiva tutti i prodotti)
        let carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
        let carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
        
        carrelloPreassemblato.prodotti.forEach(prodotto => prodotto.attivo = false);
        carrelloCatalogo.prodotti.forEach(prodotto => prodotto.attivo = false);
        
        localStorage.setItem("carrelloPreassemblato", JSON.stringify(carrelloPreassemblato));
        localStorage.setItem("carrelloCatalogo", JSON.stringify(carrelloCatalogo));
        localStorage.removeItem("carrelloAcquisto");
        
        // Reindirizza alla pagina di conferma
        window.location.href = "conferma_ordine.html";
    });
});