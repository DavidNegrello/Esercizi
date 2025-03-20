document.addEventListener("DOMContentLoaded", function () {
    // Carica i prodotti nel carrello
    caricaCarrello();

    // Gestione dello svuotamento del carrello
    document.getElementById("svuota-carrello").addEventListener("click", function () {
        if (confirm("Sei sicuro di voler svuotare il carrello?")) {
            svuotaCarrello();
        }
    });

    // Funzione per procedere all'acquisto
    document.getElementById("procedi-acquisto").addEventListener("click", function () {
        // Reindirizza alla pagina di checkout
        window.location.href = "checkout.html";
    });
});

// Funzione per caricare i prodotti nel carrello
function caricaCarrello() {
    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");
    
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
                return;
            }
            
            // Svuota il container
            carrelloContainer.innerHTML = '';
            
            let totale = 0;
            
            // Aggiungi ogni prodotto al carrello
            prodotti.forEach((prodotto) => {
                let prezzoProdotto = parseFloat(prodotto.prezzo);
                
                let variantiHTML = '';
                if (prodotto.varianti) {
                    for (const [variantiKey, variantiValue] of Object.entries(prodotto.varianti)) {
                        variantiHTML += `<p><strong>${variantiKey.charAt(0).toUpperCase() + variantiKey.slice(1)}:</strong> ${variantiValue}</p>`;
                    }
                }
                
                let personalizzazioniHTML = '';
                if (prodotto.personalizzazioni && prodotto.personalizzazioni.length > 0) {
                    personalizzazioniHTML = '<ul>';
                    prodotto.personalizzazioni.forEach(p => {
                        personalizzazioniHTML += `<li>${p.nome}: +${p.prezzo}€</li>`;
                    });
                    personalizzazioniHTML += '</ul>';
                }
                
                // Calcola il totale
                totale += prezzoProdotto;
                
                // HTML per ogni prodotto nel carrello
                const prodottoHTML = `
                    <div class="prodotto-carrello d-flex align-items-center mb-3" data-id="${prodotto.id}">
                        <img src="${prodotto.immagine}" alt="${prodotto.nome}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px;">
                        <div>
                            <h4>${prodotto.nome}</h4>
                            <p>Prezzo: ${prezzoProdotto.toFixed(2)}€</p>
                            ${variantiHTML}
                            ${personalizzazioniHTML}
                            <hr>
                        </div>
                        <button class="btn btn-danger btn-sm elimina-prodotto">
                            <i class="fas fa-trash"></i> Elimina
                        </button>
                    </div>
                `;
                carrelloContainer.innerHTML += prodottoHTML;
            });
            
            // Mostra il totale del carrello
            totalePrezzoElement.innerText = `${totale.toFixed(2)}€`;
            
            // Aggiungi event listener per i pulsanti di eliminazione
            document.querySelectorAll(".elimina-prodotto").forEach(button => {
                button.addEventListener("click", function() {
                    const prodottoId = this.closest(".prodotto-carrello").getAttribute("data-id");
                    rimuoviDalCarrello(prodottoId);
                });
            });
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

// Funzione per rimuovere un prodotto dal carrello
function rimuoviDalCarrello(prodottoId) {
    fetch('../api/carrello.php?action=remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: prodottoId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ricarica il carrello per mostrare le modifiche
            caricaCarrello();
        } else {
            alert("Errore nella rimozione del prodotto dal carrello.");
        }
    })
    .catch(error => {
        console.error("Errore nella rimozione del prodotto:", error);
        alert("Si è verificato un errore. Riprova più tardi.");
    });
}

// Funzione per svuotare il carrello
function svuotaCarrello() {
    fetch('../api/carrello.php?action=empty', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ricarica il carrello per mostrare le modifiche
            caricaCarrello();
        } else {
            alert("Errore nello svuotamento del carrello.");
        }
    })
    .catch(error => {
        console.error("Errore nello svuotamento del carrello:", error);
        alert("Si è verificato un errore. Riprova più tardi.");
    });
}