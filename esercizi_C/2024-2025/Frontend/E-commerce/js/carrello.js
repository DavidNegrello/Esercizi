document.addEventListener("DOMContentLoaded", function () {
    // Carica i carrelli dal localStorage
    const carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
    const carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
    const carrelloBundle = JSON.parse(localStorage.getItem("carrelloBundle")) || [];

    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");

    // Unisci tutti i prodotti e i bundle
    const carrello = [...carrelloPreassemblato.prodotti, ...carrelloCatalogo.prodotti, ...carrelloBundle];

    // Se il carrello è vuoto
    if (carrello.length === 0) {
        carrelloContainer.innerHTML = "<p>Il carrello è vuoto.</p>";
    } else {
        let totale = 0; // Totale del carrello

        carrello.forEach((item, index) => {
            let prodottoHTML = "";

            if (item.prodotti) {
                // È un bundle
                let prodottiHTML = item.prodotti.map(p => `<li>${p.nome} (${p.categoria})</li>`).join("");

                prodottoHTML = `
                    <div class="prodotto-carrello d-flex align-items-center mb-3" data-index="${index}" data-type="bundle">
                        <img src="${item.immagine || 'placeholder.jpg'}" alt="${item.nome}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px;">
                        <div>
                            <h4>${item.nome} (Bundle)</h4>
                            <ul>${prodottiHTML}</ul>
                            <p>Prezzo Totale: €${item.prezzo_totale.toFixed(2)}</p>
                        </div>
                        <button class="btn btn-danger btn-sm elimina-prodotto">
                            <i class="fas fa-trash"></i> Elimina
                        </button>
                    </div>
                `;

                totale += item.prezzo_totale;
            } else {
                // È un prodotto singolo
                let prezzoProdotto = parseFloat(item.prezzo) || 0;
                totale += prezzoProdotto;

                prodottoHTML = `
                    <div class="prodotto-carrello d-flex align-items-center mb-3" data-index="${index}" data-type="prodotto">
                        <img src="${item.immagine || 'placeholder.jpg'}" alt="${item.nome}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px;">
                        <div>
                            <h4>${item.nome}</h4>
                            <p>Prezzo: €${prezzoProdotto.toFixed(2)}</p>
                        </div>
                        <button class="btn btn-danger btn-sm elimina-prodotto">
                            <i class="fas fa-trash"></i> Elimina
                        </button>
                    </div>
                `;
            }

            carrelloContainer.innerHTML += prodottoHTML;
        });

        // Mostra il totale del carrello
        totalePrezzoElement.innerText = `€${totale.toFixed(2)}`;
    }

    // Eliminazione dei prodotti/bundle
    carrelloContainer.addEventListener("click", function (event) {
        if (event.target.classList.contains("elimina-prodotto") || event.target.closest(".elimina-prodotto")) {
            const prodottoIndex = event.target.closest(".prodotto-carrello").getAttribute("data-index");
            const prodottoTipo = event.target.closest(".prodotto-carrello").getAttribute("data-type");

            let carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
            let carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
            let carrelloBundle = JSON.parse(localStorage.getItem("carrelloBundle")) || [];

            if (prodottoTipo === "prodotto") {
                if (prodottoIndex < carrelloPreassemblato.prodotti.length) {
                    carrelloPreassemblato.prodotti.splice(prodottoIndex, 1);
                    localStorage.setItem("carrelloPreassemblato", JSON.stringify(carrelloPreassemblato));
                } else {
                    let updatedIndex = prodottoIndex - carrelloPreassemblato.prodotti.length;
                    carrelloCatalogo.prodotti.splice(updatedIndex, 1);
                    localStorage.setItem("carrelloCatalogo", JSON.stringify(carrelloCatalogo));
                }
            } else if (prodottoTipo === "bundle") {
                let updatedIndex = prodottoIndex - (carrelloPreassemblato.prodotti.length + carrelloCatalogo.prodotti.length);
                carrelloBundle.splice(updatedIndex, 1);
                localStorage.setItem("carrelloBundle", JSON.stringify(carrelloBundle));
            }

            // Ricarica la pagina per aggiornare il carrello
            location.reload();
        }
    });

    // Gestione dello svuotamento del carrello
    document.getElementById("svuota-carrello").addEventListener("click", function () {
        localStorage.removeItem("carrelloPreassemblato");
        localStorage.removeItem("carrelloCatalogo");
        localStorage.removeItem("carrelloBundle");
        location.reload(); // Ricarica la pagina per svuotare il carrello
    });

    // Funzione per procedere all'acquisto
    document.getElementById("procedi-acquisto").addEventListener("click", function () {
        const carrello = [...carrelloPreassemblato.prodotti, ...carrelloCatalogo.prodotti, ...carrelloBundle];
        
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

