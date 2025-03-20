document.addEventListener("DOMContentLoaded", function () {
    // Carica i prodotti nel carrello per il checkout
    caricaCarrelloCheckout();

    // Gestione dell'applicazione del coupon
    document.getElementById("applica-coupon").addEventListener("click", function () {
        applicaCoupon();
    });

    // Gestione dell'invio del form di checkout
    document.getElementById("checkout-form").addEventListener("submit", function (e) {
        e.preventDefault();
        completaOrdine();
    });
});

// Funzione per caricare i prodotti nel carrello per il checkout
function caricaCarrelloCheckout() {
    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");
    const totaleScontoElement = document.getElementById("totale-sconto");
    
    // Mostra un loader mentre si caricano i dati
    carrelloContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Caricamento...</span></div></div>';
    
    // Richiedi i dati del carrello dall'API
    fetch('../api/carrello.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Errore nel caricamento del carrello');
            }
            
            const prodotti = data.prodotti;
            
            // Se il carrello è vuoto
            if (prodotti.length === 0) {
                carrelloContainer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h3>Il carrello è vuoto</h3>
                        <p>Non hai ancora aggiunto prodotti al carrello.</p>
                        <a href="catalogo.html" class="btn btn-primary mt-3">Vai al catalogo</a>
                    </div>
                `;
                totalePrezzoElement.innerText = "0.00€";
                totaleScontoElement.innerText = "0.00€";
                
                // Nascondi il form di checkout
                document.getElementById("checkout-form").style.display = "none";
                return;
            }
            
            // Svuota il container
            carrelloContainer.innerHTML = '';
            
            let totale = 0;
            
            // Aggiungi ogni prodotto al riepilogo del checkout
            prodotti.forEach((prodotto) => {
                let prezzoProdotto = parseFloat(prodotto.prezzo);
                let prodottoHTML = "";
                
                // Gestione diversa in base al tipo di prodotto
                if (prodotto.tipo === "catalogo") {
                    let variantiHTML = '';
                    if (prodotto.varianti) {
                        variantiHTML = `
                            <div class="product-variants mt-1">
                                <p class="mb-0 small text-muted">
                                    ${Object.entries(prodotto.varianti).map(([key, value]) => 
                                        `${key.charAt(0).toUpperCase() + key.slice(1)}: ${value}`
                                    ).join(' | ')}
                                </p>
                            </div>
                        `;
                    }
                    
                    prodottoHTML = `
                        <div class="prodotto-checkout d-flex align-items-center mb-3 p-2 border-bottom">
                            <div class="product-image me-3">
                                <img src="${prodotto.immagine}" alt="${prodotto.nome}" 
                                    class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div class="product-details flex-grow-1">
                                <h6 class="mb-1">${prodotto.nome}</h6>
                                ${variantiHTML}
                            </div>
                            <div class="product-price text-end">
                                <p class="mb-0 fw-bold">${prezzoProdotto.toFixed(2)}€</p>
                            </div>
                        </div>
                    `;
                } else {
                    // Per altri tipi di prodotti (preassemblati, bundle, ecc.)
                    let personalizzazioniHTML = '';
                    if (prodotto.personalizzazioni && prodotto.personalizzazioni.length > 0) {
                        personalizzazioniHTML = `
                            <div class="product-customizations mt-1">
                                <p class="mb-0 small text-muted">
                                    ${prodotto.personalizzazioni.map(p => `${p.nome}: +${p.prezzo}€`).join(' | ')}
                                </p>
                            </div>
                        `;
                    }
                    
                    prodottoHTML = `
                        <div class="prodotto-checkout d-flex align-items-center mb-3 p-2 border-bottom">
                            <div class="product-image me-3">
                                <img src="${prodotto.immagine}" alt="${prodotto.nome}" 
                                    class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div class="product-details flex-grow-1">
                                <h6 class="mb-1">${prodotto.nome}</h6>
                                ${personalizzazioniHTML}
                            </div>
                            <div class="product-price text-end">
                                <p class="mb-0 fw-bold">${prezzoProdotto.toFixed(2)}€</p>
                            </div>
                        </div>
                    `;
                }
                
                totale += prezzoProdotto;
                carrelloContainer.innerHTML += prodottoHTML;
            });
            
            // Aggiungi il riepilogo del totale
            carrelloContainer.innerHTML += `
                <div class="checkout-summary mt-3">
                    <div class="d-flex justify-content-between">
                        <p class="mb-1">Subtotale:</p>
                        <p class="mb-1 fw-bold">${totale.toFixed(2)}€</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="mb-1">Spedizione:</p>
                        <p class="mb-1">Gratuita</p>
                    </div>
                    <div class="d-flex justify-content-between coupon-row" style="display: none;">
                        <p class="mb-1">Sconto coupon:</p>
                        <p class="mb-1 text-success" id="sconto-coupon">0.00€</p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>Totale:</h5>
                        <h5 class="text-primary" id="totale-finale">${totale.toFixed(2)}€</h5>
                    </div>
                </div>
            `;
            
            // Mostra il totale
            totalePrezzoElement.innerText = `${totale.toFixed(2)}€`;
            totaleScontoElement.innerText = `${totale.toFixed(2)}€`; // Inizialmente senza sconto
            
            // Salva il totale come attributo data per usarlo più tardi
            document.getElementById("totale-finale").setAttribute("data-totale", totale.toFixed(2));
        })
        .catch(error => {
            console.error("Errore nel caricamento del carrello:", error);
            carrelloContainer.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Si è verificato un errore nel caricamento del carrello. Riprova più tardi.
                </div>
            `;
        });
}

// Funzione per applicare un coupon
function applicaCoupon() {
    const codiceCoupon = document.getElementById("coupon").value.trim();
    const messaggioCoupon = document.getElementById("messaggio-coupon");
    
    if (!codiceCoupon) {
        messaggioCoupon.innerText = "Inserisci un codice coupon.";
        messaggioCoupon.style.color = "red";
        return;
    }
    
    // Richiedi la verifica del coupon all'API
    fetch('../api/checkout.php?action=coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ coupon: codiceCoupon })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Coupon valido
            const totale = parseFloat(document.getElementById("totale-finale").getAttribute("data-totale"));
            const percentualeSconto = data.percentuale_sconto;
            const scontoValore = totale * (percentualeSconto / 100);
            const nuovoTotale = totale - scontoValore;
            
            // Aggiorna il totale con lo sconto
            document.getElementById("totale-sconto").innerText = `${nuovoTotale.toFixed(2)}€`;
            document.getElementById("totale-finale").innerText = `${nuovoTotale.toFixed(2)}€`;
            
            // Mostra la riga dello sconto
            document.querySelector(".coupon-row").style.display = "flex";
            document.getElementById("sconto-coupon").innerText = `-${scontoValore.toFixed(2)}€`;
            
            // Mostra un messaggio di conferma
            messaggioCoupon.innerText = `Coupon applicato! Hai ricevuto uno sconto del ${percentualeSconto}%`;
            messaggioCoupon.style.color = "green";
            
            // Aggiungi effetto visivo
            document.getElementById("coupon").classList.add("is-valid");
            document.getElementById("coupon").classList.remove("is-invalid");
            
            // Salva l'ID del coupon per l'ordine
            document.getElementById("checkout-form").setAttribute("data-coupon-id", data.coupon_id);
        } else {
            // Coupon non valido
            messaggioCoupon.innerText = data.error || "Coupon non valido.";
            messaggioCoupon.style.color = "red";
            
            // Aggiungi effetto visivo
            document.getElementById("coupon").classList.add("is-invalid");
            document.getElementById("coupon").classList.remove("is-valid");
            
            // Ripristina il totale originale
            const totale = document.getElementById("totale-finale").getAttribute("data-totale");
            document.getElementById("totale-sconto").innerText = `${totale}€`;
            document.getElementById("totale-finale").innerText = `${totale}€`;
            document.querySelector(".coupon-row").style.display = "none";
            
            // Rimuovi l'ID del coupon
            document.getElementById("checkout-form").removeAttribute("data-coupon-id");
        }
    })
    .catch(error => {
        console.error("Errore nella verifica del coupon:", error);
        messaggioCoupon.innerText = "Si è verificato un errore. Riprova più tardi.";
        messaggioCoupon.style.color = "red";
    });
}

// Funzione per completare l'ordine
function completaOrdine() {
    const nome = document.getElementById("nome").value;
    const indirizzo = document.getElementById("indirizzo").value;
    const email = document.getElementById("email").value;
    const metodoPagamento = document.getElementById("metodo-pagamento").value;
    const couponId = document.getElementById("checkout-form").getAttribute("data-coupon-id");
    
    // Mostra il loader
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Elaborazione...';
    submitBtn.disabled = true;
    
    // Prepara i dati dell'ordine
    const orderData = {
        customer: {
            nome: nome,
            indirizzo: indirizzo,
            email: email,
            metodo_pagamento: metodoPagamento
        },
        coupon_id: couponId || null
    };
    
    // Invia l'ordine all'API
    fetch('../api/checkout.php?action=order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ordine completato con successo
            window.location.href = "conferma.html?order_id=" + data.order_id;
        } else {
            // Errore nell'ordine
            alert(data.error || "Si è verificato un errore durante l'elaborazione dell'ordine.");
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error("Errore nel completamento dell'ordine:", error);
        alert("Si è verificato un errore. Riprova più tardi.");
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
}

// Migliora l'interattività del form
document.addEventListener("DOMContentLoaded", function() {
    const inputs = document.querySelectorAll('#checkout-form input, #checkout-form select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('input-focused');
            }
        });
        
        // Inizializza lo stato per input con valori precompilati
        if (input.value) {
            input.parentElement.classList.add('input-focused');
        }
    });
});