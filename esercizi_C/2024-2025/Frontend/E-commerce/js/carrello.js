document.addEventListener("DOMContentLoaded", function () {
    // Carica i carrelli dal localStorage
    const carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
    const carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };

    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");

    // Unisci i prodotti dei due carrelli
    const carrello = [...carrelloPreassemblato.prodotti, ...carrelloCatalogo.prodotti];

    // Se il carrello è vuoto
    if (carrello.length === 0) {
        carrelloContainer.innerHTML = "<p>Il carrello è vuoto.</p>";
    } else {
        let totale = 0; // Variabile per calcolare il totale del carrello

        // Aggiungi ogni prodotto al carrello
        carrello.forEach((prodotto, index) => {
            // Assicurati che prezzo sia una stringa, altrimenti converti in stringa
            let prezzoProdotto = (typeof prodotto.prezzo === "string") 
                ? prodotto.prezzo.replace('€', '').replace(',', '.') 
                : prodotto.prezzo.toString().replace('€', '').replace(',', '.');
            prezzoProdotto = parseFloat(prezzoProdotto);

            let variantiHTML = '';

            // Mostra varianti (se presenti) per i prodotti del catalogo
            if (prodotto.varianti) {
                for (const [variantiKey, variantiValue] of Object.entries(prodotto.varianti)) {
                    variantiHTML += `<p><strong>${variantiKey.charAt(0).toUpperCase() + variantiKey.slice(1)}:</strong> ${variantiValue}</p>`;
                }
            }

            // Aggiungi le personalizzazioni per i prodotti preassemblati
            let personalizzazioniHTML = '';
            if (prodotto.personalizzazioni && prodotto.personalizzazioni.length > 0) {
                prodotto.personalizzazioni.forEach(p => {
                    prezzoProdotto += p.prezzo;
                    personalizzazioniHTML += `<li>${p.nome}: +${p.prezzo}€</li>`;
                });
            }

            // Calcola il totale
            totale += prezzoProdotto;

            // HTML per ogni prodotto nel carrello
            const prodottoHTML = `
                <div class="prodotto-carrello d-flex align-items-center mb-3" data-index="${index}">
                    <img src="${prodotto.immagine}" alt="${prodotto.nome}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px;">
                    <div>
                        <h4>${prodotto.nome}</h4>
                        <p>Prezzo: ${prezzoProdotto.toFixed(2)}€</p>
                        ${variantiHTML}
                        ${personalizzazioniHTML ? `<ul>${personalizzazioniHTML}</ul>` : ''}
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
    }

    // Gestione dell'eliminazione dei prodotti dal carrello (delegazione)
    carrelloContainer.addEventListener("click", function (event) {
        if (event.target.classList.contains("elimina-prodotto")) {
            const prodottoIndex = event.target.closest(".prodotto-carrello").getAttribute("data-index");

            // Verifica se il prodotto appartiene al carrello preassemblato o catalogo
            let carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
            let carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };

            // Rimuovi il prodotto dal carrello preassemblato o catalogo
            if (prodottoIndex < carrelloPreassemblato.prodotti.length) {
                carrelloPreassemblato.prodotti.splice(prodottoIndex, 1);
                localStorage.setItem("carrelloPreassemblato", JSON.stringify(carrelloPreassemblato));
            } else {
                let updatedIndex = prodottoIndex - carrelloPreassemblato.prodotti.length; // Correggi l'indice per il carrelloCatalogo
                carrelloCatalogo.prodotti.splice(updatedIndex, 1);
                localStorage.setItem("carrelloCatalogo", JSON.stringify(carrelloCatalogo));
            }

            // Ricarica la pagina per aggiornare il carrello
            location.reload();
        }
    });

    // Gestione dello svuotamento del carrello
    document.getElementById("svuota-carrello").addEventListener("click", function () {
        localStorage.removeItem("carrelloPreassemblato");
        localStorage.removeItem("carrelloCatalogo");
        location.reload(); // Ricarica la pagina per svuotare il carrello
    });

    // Funzione per procedere all'acquisto (puoi aggiungere una logica di pagamento qui)
    document.getElementById("procedi-acquisto").addEventListener("click", function () {
        const carrello = [...carrelloPreassemblato.prodotti, ...carrelloCatalogo.prodotti];
        if (carrello.length === 0) {
            alert("Il carrello è vuoto!");
            return;
        }
        alert("Procedi all'acquisto!");
    });
});
