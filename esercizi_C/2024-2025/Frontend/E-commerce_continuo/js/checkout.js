document.addEventListener("DOMContentLoaded", function () {
    // Carica il carrello dal server
    fetch("../api/carrello.php")
        .then(response => response.json())
        .then(data => {
            const items = data.items;
            const total = data.total;
            
            const carrelloContainer = document.getElementById("carrello-container");
            const totalePrezzoElement = document.getElementById("totale-prezzo");
            const totaleScontoElement = document.getElementById("totale-sconto");
            const messaggioCoupon = document.getElementById("messaggio-coupon");

            if (!items || items.length === 0) {
                carrelloContainer.innerHTML = `
                    <div class="empty-cart text-center py-4">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h3>Il carrello è vuoto</h3>
                        <p class="text-muted">Non ci sono prodotti nel tuo carrello</p>
                        <a href="catalogo.html" class="btn btn-primary mt-3">Torna al catalogo</a>
                    </div>
                `;
                document.getElementById("checkout-form").style.display = "none";
                return;
            }

            // Aggiungi ogni prodotto al riepilogo del carrello
            let prodottiHtml = "";
            items.forEach(item => {
                const varianti = JSON.parse(item.varianti || '{}');
                let variantiHtml = '';
                
                if (Object.keys(varianti).length > 0) {
                    variantiHtml = `
                        <div class="product-variants mt-1">
                            <p class="mb-0 small text-muted">
                                ${Object.entries(varianti).map(([key, value]) => 
                                    `${key.charAt(0).toUpperCase() + key.slice(1)}: ${value}`
                                ).join(' | ')}
                            </p>
                        </div>
                    `;
                }
                
                prodottiHtml += `
                    <div class="prodotto-checkout d-flex align-items-center mb-3 p-2 border-bottom">
                        <div class="product-image me-3">
                            <img src="${item.immagine_principale}" alt="${item.nome}" 
                                class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            ${item.tipo !== 'catalogo' ? `<span class="badge bg-primary position-absolute top-0 start-0 m-1 small">${item.tipo}</span>` : ''}
                        </div>
                        <div class="product-details flex-grow-1">
                            <h6 class="mb-1">${item.nome}</h6>
                            ${variantiHtml}
                        </div>
                        <div class="product-price text-end">
                            <p class="mb-0 fw-bold">${(item.prezzo_unitario * item.quantita).toFixed(2)}€</p>
                            <small class="text-muted">${item.quantita} x ${item.prezzo_unitario.toFixed(2)}€</small>
                        </div>
                    </div>
                `;
            });
            
            carrelloContainer.innerHTML = prodottiHtml;

            // Aggiungi il riepilogo del totale
            carrelloContainer.innerHTML += `
                <div class="checkout-summary mt-3">
                    <div class="d-flex justify-content-between">
                        <p class="mb-1">Subtotale:</p>
                        <p class="mb-1 fw-bold">${total.toFixed(2)}€</p>
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
                        <h5 class="text-primary" id="totale-finale">${total.toFixed(2)}€</h5>
                    </div>
                </div>
            `;

            // Mostra il totale
            totalePrezzoElement.innerText = `${total.toFixed(2)}€`;
            totaleScontoElement.innerText = `${total.toFixed(2)}€`; // Inizialmente senza sconto
            
            // Salva il totale originale per i calcoli successivi
            document.getElementById("checkout-form").dataset.totale = total;
        })
        .catch(error => {
            console.error("Errore nel caricamento del carrello:", error);
            document.getElementById("carrello-container").innerHTML = `
                <div class="alert alert-danger">
                    Si è verificato un errore nel caricamento del carrello. Riprova più tardi.
                </div>
            `;
        });

    // Funzione per applicare il coupon
    document.getElementById("applica-coupon").addEventListener("click", function () {
        const codiceCoupon = document.getElementById("coupon").value.trim();
        const messaggioCoupon = document.getElementById("messaggio-coupon");
        
        if (!codiceCoupon) {
            messaggioCoupon.innerText = "Inserisci un codice coupon.";
            messaggioCoupon.style.color = "red";
            return;
        }
        
        // Crea i dati da inviare
        const formData = new FormData();
        formData.append('codice', codiceCoupon);
        
        // Invia la richiesta al server
        fetch('../api/coupon.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                // Coupon valido
                const totale = parseFloat(document.getElementById("checkout-form").dataset.totale);
                const sconto = totale * (data.discount / 100);
                const nuovoTotale = totale - sconto;
                
                // Aggiorna il totale con lo sconto
                document.getElementById("totale-sconto").innerText = `${nuovoTotale.toFixed(2)}€`;
                document.getElementById("totale-finale").innerText = `${nuovoTotale.toFixed(2)}€`;
                
                // Mostra la riga dello sconto
                document.querySelector(".coupon-row").style.display = "flex";
                document.getElementById("sconto-coupon").innerText = `-${sconto.toFixed(2)}€`;
                
                // Salva l'ID del coupon per il checkout
                document.getElementById("checkout-form").dataset.couponId = data.id;
                
                // Mostra un messaggio di conferma
                messaggioCoupon.innerText = data.message;
                messaggioCoupon.style.color = "green";
                
                // Aggiungi effetto visivo
                document.getElementById("coupon").classList.add("is-valid");
                document.getElementById("coupon").classList.remove("is-invalid");
            } else {
                // Coupon non valido
                messaggioCoupon.innerText = data.message;
                messaggioCoupon.style.color = "red";
                
                // Aggiungi effetto visivo
                document.getElementById("coupon").classList.add("is-invalid");
                document.getElementById("coupon").classList.remove("is-valid");
                
                // Ripristina il totale originale
                const totale = parseFloat(document.getElementById("checkout-form").dataset.totale);
                document.getElementById("totale-sconto").innerText = `${totale.toFixed(2)}€`;
                document.getElementById("totale-finale").innerText = `${totale.toFixed(2)}€`;
                document.querySelector(".coupon-row").style.display = "none";
                
                // Rimuovi l'ID del coupon
                delete document.getElementById("checkout-form").dataset.couponId;
            }
        })
        .catch(error => {
            console.error("Errore:", error);
            messaggioCoupon.innerText = "Si è verificato un errore durante la verifica del coupon.";
            messaggioCoupon.style.color = "red";
        });
    });

    // Gestione del submit del form
    document.getElementById("checkout-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const nome = document.getElementById("nome").value;
        const indirizzo = document.getElementById("indirizzo").value;
        const email = document.getElementById("email").value;
        const metodoPagamento = document.getElementById("metodo-pagamento").value;
        const couponId = this.dataset.couponId || null;

        // Mostra il loader
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Elaborazione...';
        submitBtn.disabled = true;

        // Crea i dati da inviare
        const formData = new FormData();
        formData.append('nome', nome);
        formData.append('indirizzo', indirizzo);
        formData.append('email', email);
        formData.append('metodo_pagamento', metodoPagamento);
        if (couponId) {
            formData.append('coupon_id', couponId);
        }
        
        // Invia la richiesta al server
        fetch('../api/checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reindirizza alla pagina di conferma
                window.location.href = `conferma.html?order_id=${data.order_id}`;
            } else {
                alert("Si è verificato un errore durante il checkout: " + (data.error || "Errore sconosciuto"));
                // Ripristina il pulsante
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error("Errore:", error);
            alert("Si è verificato un errore durante il checkout.");
            // Ripristina il pulsante
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });

    // Migliora l'interattività del form
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