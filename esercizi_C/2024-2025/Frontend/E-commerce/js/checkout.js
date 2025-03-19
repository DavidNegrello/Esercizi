document.addEventListener("DOMContentLoaded", function () {
    // Carica il carrello dal localStorage
    const carrelloAcquisto = JSON.parse(localStorage.getItem("carrelloAcquisto"));

    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");
    const totaleScontoElement = document.getElementById("totale-sconto");
    const messaggioCoupon = document.getElementById("messaggio-coupon");

    if (!carrelloAcquisto || carrelloAcquisto.length === 0) {
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
    let totale = 0;
    carrelloAcquisto.forEach((prodotto) => {
        let prezzoProdotto = 0;
        let prodottoHTML = "";
        
        // Gestione diversa in base al tipo di prodotto
        if (prodotto.prodotti) {
            // È un bundle
            prezzoProdotto = prodotto.prezzo_totale || 0;
            
            let prodottiHTML = "";
            if (prodotto.prodotti && prodotto.prodotti.length > 0) {
                prodottiHTML = `
                    <div class="bundle-products mt-2">
                        <p class="mb-1 small text-muted">Il bundle include:</p>
                        <ul class="list-unstyled ps-3 small">
                            ${prodotto.prodotti.map(p => `<li><i class="fas fa-check text-success me-1"></i>${p.nome}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            prodottoHTML = `
                <div class="prodotto-checkout d-flex align-items-center mb-3 p-2 border-bottom">
                    <div class="product-image me-3">
                        <img src="${prodotto.immagine || '../immagini/bundle-default.jpg'}" alt="${prodotto.nome}" 
                            class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                        <span class="badge bg-primary position-absolute top-0 start-0 m-1 small">Bundle</span>
                    </div>
                    <div class="product-details flex-grow-1">
                        <h6 class="mb-1">${prodotto.nome}</h6>
                        ${prodottiHTML}
                    </div>
                    <div class="product-price text-end">
                        <p class="mb-0 fw-bold">${prezzoProdotto.toFixed(2)}€</p>
                    </div>
                </div>
            `;
        } else {
            // È un prodotto singolo
            let prezzoStr = (typeof prodotto.prezzo === "string") 
                ? prodotto.prezzo.replace('€', '').replace(',', '.') 
                : prodotto.prezzo.toString().replace('€', '').replace(',', '.');
            prezzoProdotto = parseFloat(prezzoStr);
            
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

    // Funzione per applicare il coupon
    document.getElementById("applica-coupon").addEventListener("click", function () {
        const codiceCoupon = document.getElementById("coupon").value.trim();

        // Definisci i coupon disponibili e gli sconti
        const couponValidi = {
            "Sconto10": 0.10, // 10% di sconto
            "Sconto20": 0.20, // 20% di sconto
            "Sconto30": 0.30  // 30% di sconto
        };

        if (couponValidi[codiceCoupon]) {
            const sconto = couponValidi[codiceCoupon];
            const scontoValore = totale * sconto;
            const nuovoTotale = totale - scontoValore;
            
            // Aggiorna il totale con lo sconto
            totaleScontoElement.innerText = `${nuovoTotale.toFixed(2)}€`;
            document.getElementById("totale-finale").innerText = `${nuovoTotale.toFixed(2)}€`;
            
            // Mostra la riga dello sconto
            document.querySelector(".coupon-row").style.display = "flex";
            document.getElementById("sconto-coupon").innerText = `-${scontoValore.toFixed(2)}€`;

            // Mostra un messaggio di conferma
            messaggioCoupon.innerText = `Coupon applicato! Hai ricevuto uno sconto del ${sconto * 100}%`;
            messaggioCoupon.style.color = "green";
            
            // Aggiungi effetto visivo
            document.getElementById("coupon").classList.add("is-valid");
            document.getElementById("coupon").classList.remove("is-invalid");
        } else {
            // Se il coupon non è valido
            messaggioCoupon.innerText = "Coupon non valido.";
            messaggioCoupon.style.color = "red";
            
            // Aggiungi effetto visivo
            document.getElementById("coupon").classList.add("is-invalid");
            document.getElementById("coupon").classList.remove("is-valid");
            
            // Ripristina il totale originale
            totaleScontoElement.innerText = `${totale.toFixed(2)}€`;
            document.getElementById("totale-finale").innerText = `${totale.toFixed(2)}€`;
            document.querySelector(".coupon-row").style.display = "none";
        }
    });

    // Gestione del submit del form
    document.getElementById("checkout-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const nome = document.getElementById("nome").value;
        const indirizzo = document.getElementById("indirizzo").value;
        const email = document.getElementById("email").value;
        const metodoPagamento = document.getElementById("metodo-pagamento").value;

        // Mostra il loader
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Elaborazione...';
        submitBtn.disabled = true;

        // Simula un ritardo per l'elaborazione
        setTimeout(() => {
            // Salva i dati dell'ordine
            const ordine = {
                cliente: {
                    nome,
                    indirizzo,
                    email
                },
                carrello: carrelloAcquisto,
                metodoPagamento,
                totale: parseFloat(totaleScontoElement.innerText.replace('€', ''))
            };

            // Invia i dati dell'ordine al server (o simula un invio)
            console.log("Ordine inviato:", ordine);

            // Pulisci il carrello
            localStorage.removeItem("carrelloAcquisto");
            localStorage.removeItem("carrelloPreassemblato");
            localStorage.removeItem("carrelloCatalogo");
            localStorage.removeItem("carrelloBundle");
            
            // Reindirizza alla pagina di conferma
            window.location.href = "conferma.html";
        }, 1500);
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