document.addEventListener("DOMContentLoaded", function() {
    // Carica il carrello dal localStorage
    const carrello = JSON.parse(localStorage.getItem("carrello")) || { prodotti: [] };

    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");

    // Se il carrello è vuoto
    if (carrello.prodotti.length === 0) {
        carrelloContainer.innerHTML = "<p>Il carrello è vuoto.</p>";
    } else {
        // Aggiungi ogni prodotto al carrello
        carrello.prodotti.forEach(prodotto => {
            const prodottoHTML = `
                <div class="prodotto-carrello d-flex align-items-center mb-3">
                    <img src="${prodotto.immagine}" alt="${prodotto.nome}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px;">
                    <div>
                        <h4>${prodotto.nome}</h4>
                        <p>Prezzo: ${prodotto.prezzo}</p>
                        <p>Colore: ${prodotto.colore}</p>
                        <ul>
                            ${prodotto.personalizzazioni.map(p => `<li>${p.nome}: +${p.prezzo}€</li>`).join('')}
                        </ul>
                        <hr>
                    </div>
                </div>
            `;
            carrelloContainer.innerHTML += prodottoHTML;
        });

        // Calcola e mostra il totale
        const totale = carrello.prodotti.reduce((totale, prodotto) => {
            let prezzoProdotto = parseFloat(prodotto.prezzo.replace('€', '').replace(',', '.'));
            prodotto.personalizzazioni.forEach(p => {
                prezzoProdotto += p.prezzo;
            });
            return totale + prezzoProdotto;
        }, 0);

        totalePrezzoElement.innerText = `${totale.toFixed(2)}€`;
    }

    // Funzione per svuotare il carrello
    document.getElementById("svuota-carrello").addEventListener("click", function() {
        localStorage.removeItem("carrello"); // Rimuove il carrello dal localStorage
        location.reload(); // Ricarica la pagina
    });

    // Funzione per procedere all'acquisto
    document.getElementById("procedi-acquisto").addEventListener("click", function() {
        if (carrello.prodotti.length === 0) {
            alert("Il carrello è vuoto!");
            return;
        }
        // Qui puoi implementare la logica per il pagamento o il checkout
        alert("Procedi all'acquisto!");
    });
});
