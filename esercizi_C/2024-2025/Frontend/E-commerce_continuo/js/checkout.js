document.addEventListener("DOMContentLoaded", function () {
    // Carica il carrello dal localStorage
    const carrelloAcquisto = JSON.parse(localStorage.getItem("carrelloAcquisto"));

    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");
    const totaleScontoElement = document.getElementById("totale-sconto");
    const messaggioCoupon = document.getElementById("messaggio-coupon");

    if (!carrelloAcquisto || carrelloAcquisto.length === 0) {
        carrelloContainer.innerHTML = "<p>Il carrello è vuoto.</p>";
        return;
    }

    // Aggiungi ogni prodotto al riepilogo del carrello
    let totale = 0;
    carrelloAcquisto.forEach((prodotto) => {
        const prodottoHTML = `
            <div class="prodotto-carrello mb-3">
                <h4>${prodotto.nome}</h4>
                <p>Prezzo: ${prodotto.prezzo}€</p>
                ${prodotto.varianti ? Object.entries(prodotto.varianti).map(([key, value]) => `<p><strong>${key}:</strong> ${value}</p>`).join('') : ''}
            </div>
        `;
        carrelloContainer.innerHTML += prodottoHTML;

        // Somma il prezzo dei prodotti
        totale += parseFloat(prodotto.prezzo);
    });

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
            const nuovoTotale = totale - (totale * sconto);
            
            // Aggiorna il totale con lo sconto
            totaleScontoElement.innerText = `${nuovoTotale.toFixed(2)}€`;

            // Mostra un messaggio di conferma
            messaggioCoupon.innerText = `Coupon applicato! Hai ricevuto uno sconto del ${sconto * 100}%`;
            messaggioCoupon.style.color = "green";
        } else {
            // Se il coupon non è valido
            messaggioCoupon.innerText = "Coupon non valido.";
            messaggioCoupon.style.color = "red";
        }
    });

    // Gestione del submit del form
    document.getElementById("checkout-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const nome = document.getElementById("nome").value;
        const indirizzo = document.getElementById("indirizzo").value;
        const email = document.getElementById("email").value;
        const metodoPagamento = document.getElementById("metodo-pagamento").value;

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

        // Pulisci il carrello e reindirizza alla pagina di conferma
        localStorage.removeItem("carrelloAcquisto");
        alert("Acquisto completato! Grazie per il tuo ordine.");
        window.location.href = "conferma.html"; // Redirigi a una pagina di conferma (ad esempio conferma.html)
    });
});
