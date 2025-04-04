document.addEventListener("DOMContentLoaded", function () {
    // Carica i carrelli dal localStorage
    const carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
    const carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
    const carrelloBundle = JSON.parse(localStorage.getItem("carrelloBundle")) || { prodotti: [] };

    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");

    // Unisci tutti i prodotti dei diversi carrelli
    const carrello = [
        ...carrelloPreassemblato.prodotti, 
        ...carrelloCatalogo.prodotti, 
        ...carrelloBundle.prodotti
    ];

    // Se il carrello è vuoto
    if (carrello.length === 0) {
        carrelloContainer.innerHTML = `
            <div class="empty-cart text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h3>Il tuo carrello è vuoto</h3>
                <p class="text-muted">Aggiungi prodotti dal catalogo o dai preassemblati</p>
                <a href="catalogo.html" class="btn btn-primary mt-3">Continua lo shopping</a>
            </div>
        `;
    } else {
        let totale = 0; // Variabile per calcolare il totale del carrello

        // Aggiungi ogni prodotto al carrello
        carrello.forEach((prodotto, index) => {
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
                            <p class="mb-1 fw-bold">Il bundle include:</p>
                            <ul class="list-unstyled ps-3">
                                ${prodotto.prodotti.map(p => `<li><i class="fas fa-check text-success me-2"></i>${p.nome}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                }
                
                prodottoHTML = `
                    <div class="prodotto-carrello card mb-3 shadow-sm" data-index="${index}" data-type="bundle">
                        <div class="card-body d-flex align-items-center">
                            <div class="product-image me-3">
                                <img src="${prodotto.immagine || '../immagini/bundle-default.jpg'}" alt="${prodotto.nome}" 
                                    class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                <span class="badge bg-primary position-absolute top-0 start-0 m-2">Bundle</span>
                            </div>
                            <div class="product-details flex-grow-1">
                                <h4 class="card-title">${prodotto.nome}</h4>
                                ${prodottiHTML}
                                <p class="card-text text-primary fw-bold mt-2">Prezzo: ${prezzoProdotto.toFixed(2)}€</p>
                            </div>
                            <button class="btn btn-outline-danger elimina-prodotto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            } else {
                // È un prodotto singolo (preassemblato o catalogo)
                // Assicurati che prezzo sia una stringa, altrimenti converti in stringa
                let prezzoStr = (typeof prodotto.prezzo === "string") 
                    ? prodotto.prezzo.replace('€', '').replace(',', '.') 
                    : prodotto.prezzo.toString().replace('€', '').replace(',', '.');
                prezzoProdotto = parseFloat(prezzoStr);

                let variantiHTML = '';
                // Mostra varianti (se presenti) per i prodotti del catalogo
                if (prodotto.varianti) {
                    variantiHTML = `
                        <div class="product-variants mt-2">
                            ${Object.entries(prodotto.varianti).map(([key, value]) => 
                                `<span class="badge bg-light text-dark me-2">${key.charAt(0).toUpperCase() + key.slice(1)}: ${value}</span>`
                            ).join('')}
                        </div>
                    `;
                }

                // Aggiungi le personalizzazioni per i prodotti preassemblati
                let personalizzazioniHTML = '';
                if (prodotto.personalizzazioni && prodotto.personalizzazioni.length > 0) {
                    personalizzazioniHTML = `
                        <div class="product-customizations mt-2">
                            <p class="mb-1 fw-bold">Personalizzazioni:</p>
                            <ul class="list-unstyled ps-3">
                                ${prodotto.personalizzazioni.map(p => {
                                    prezzoProdotto += p.prezzo;
                                    return `<li><i class="fas fa-plus text-success me-2"></i>${p.nome}: +${p.prezzo}€</li>`;
                                }).join('')}
                            </ul>
                        </div>
                    `;
                }

                prodottoHTML = `
                    <div class="prodotto-carrello card mb-3 shadow-sm" data-index="${index}" data-type="prodotto">
                        <div class="card-body d-flex align-items-center">
                            <div class="product-image me-3">
                                <img src="${prodotto.immagine}" alt="${prodotto.nome}" 
                                    class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <div class="product-details flex-grow-1">
                                <h4 class="card-title">${prodotto.nome}</h4>
                                ${variantiHTML}
                                ${personalizzazioniHTML}
                                <p class="card-text text-primary fw-bold mt-2">Prezzo: ${prezzoProdotto.toFixed(2)}€</p>
                            </div>
                            <button class="btn btn-outline-danger elimina-prodotto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }

            // Calcola il totale
            totale += prezzoProdotto;
            carrelloContainer.innerHTML += prodottoHTML;
        });

        // Aggiungi il riepilogo del carrello
        carrelloContainer.innerHTML += `
            <div class="cart-summary card mt-4 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Totale Carrello</h4>
                        <h3 class="text-primary mb-0">${totale.toFixed(2)}€</h3>
                    </div>
                </div>
            </div>
        `;

        // Mostra il totale del carrello
        totalePrezzoElement.innerText = `${totale.toFixed(2)}€`;
    }

    // Gestione dell'eliminazione dei prodotti dal carrello (delegazione)
    carrelloContainer.addEventListener("click", function (event) {
        const deleteButton = event.target.closest(".elimina-prodotto");
        if (deleteButton) {
            const prodottoElement = deleteButton.closest(".prodotto-carrello");
            const prodottoIndex = prodottoElement.getAttribute("data-index");
            const prodottoTipo = prodottoElement.getAttribute("data-type");

            // Verifica se il prodotto appartiene al carrello preassemblato, catalogo o bundle
            let carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
            let carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
            let carrelloBundle = JSON.parse(localStorage.getItem("carrelloBundle")) || { prodotti: [] };

            if (prodottoTipo === "bundle") {
                // Calcola l'indice corretto per il bundle
                const bundleIndex = prodottoIndex - (carrelloPreassemblato.prodotti.length + carrelloCatalogo.prodotti.length);
                if (bundleIndex >= 0 && bundleIndex < carrelloBundle.prodotti.length) {
                    carrelloBundle.prodotti.splice(bundleIndex, 1);
                    localStorage.setItem("carrelloBundle", JSON.stringify(carrelloBundle));
                }
            } else {
                // Rimuovi il prodotto dal carrello preassemblato o catalogo
                if (prodottoIndex < carrelloPreassemblato.prodotti.length) {
                    carrelloPreassemblato.prodotti.splice(prodottoIndex, 1);
                    localStorage.setItem("carrelloPreassemblato", JSON.stringify(carrelloPreassemblato));
                } else {
                    let updatedIndex = prodottoIndex - carrelloPreassemblato.prodotti.length;
                    if (updatedIndex >= 0 && updatedIndex < carrelloCatalogo.prodotti.length) {
                        carrelloCatalogo.prodotti.splice(updatedIndex, 1);
                        localStorage.setItem("carrelloCatalogo", JSON.stringify(carrelloCatalogo));
                    }
                }
            }

            // Animazione di rimozione
            prodottoElement.classList.add("fade-out");
            setTimeout(() => {
                // Ricarica la pagina per aggiornare il carrello
                location.reload();
            }, 300);
        }
    });

    // Gestione dello svuotamento del carrello
    document.getElementById("svuota-carrello").addEventListener("click", function () {
        if (carrello.length === 0) return;
        
        if (confirm("Sei sicuro di voler svuotare il carrello?")) {
            localStorage.removeItem("carrelloPreassemblato");
            localStorage.removeItem("carrelloCatalogo");
            localStorage.removeItem("carrelloBundle");
            
            // Animazione di svuotamento
            document.querySelectorAll(".prodotto-carrello").forEach(el => {
                el.classList.add("fade-out");
            });
            
            setTimeout(() => {
                location.reload(); // Ricarica la pagina per svuotare il carrello
            }, 300);
        }
    });

    // Funzione per procedere all'acquisto
    document.getElementById("procedi-acquisto").addEventListener("click", function () {
        if (carrello.length === 0) {
            alert("Il carrello è vuoto!");
            return;
        }
    
        // Salva il carrello nel localStorage prima del reindirizzamento
        localStorage.setItem("carrelloAcquisto", JSON.stringify(carrello));
    
        // Reindirizza alla pagina di checkout
        window.location.href = "checkout.html";
    });
});