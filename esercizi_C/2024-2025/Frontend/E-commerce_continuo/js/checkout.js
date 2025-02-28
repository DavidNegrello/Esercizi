document.addEventListener("DOMContentLoaded", function () {
    const carrelloAcquisto = JSON.parse(localStorage.getItem("carrelloAcquisto"));

    const carrelloContainer = document.getElementById("carrello-container");
    const totalePrezzoElement = document.getElementById("totale-prezzo");
    const totaleScontoElement = document.getElementById("totale-sconto");
    const messaggioCoupon = document.getElementById("messaggio-coupon");

    if (!carrelloAcquisto || carrelloAcquisto.length === 0) {
        carrelloContainer.innerHTML = "<p>Il carrello è vuoto.</p>";
        return;
    }

    let totale = 0;
    carrelloAcquisto.forEach((prodotto) => {
        const prodottoHTML = `
            <div class="prodotto-carrello mb-3 p-3 border rounded d-flex align-items-center">
                <img src="${prodotto.immagine}" alt="${prodotto.nome}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; margin-right: 15px;">
                <div>
                    <h5>${prodotto.nome}</h5>
                    <p>Prezzo: <strong>${prodotto.prezzo}€</strong></p>
                    ${prodotto.varianti ? Object.entries(prodotto.varianti).map(([key, value]) => `<p><strong>${key}:</strong> ${value}</p>`).join('') : ''}
                </div>
            </div>
        `;
        carrelloContainer.innerHTML += prodottoHTML;
        totale += parseFloat(prodotto.prezzo);
    });

    totalePrezzoElement.innerText = `${totale.toFixed(2)}€`;
    totaleScontoElement.innerText = `${totale.toFixed(2)}€`;

    document.getElementById("applica-coupon").addEventListener("click", function () {
        const codiceCoupon = document.getElementById("coupon").value.trim();
        const couponValidi = {
            "Sconto10": 0.10,
            "Sconto20": 0.20,
            "Sconto30": 0.30
        };

        if (couponValidi[codiceCoupon]) {
            const sconto = couponValidi[codiceCoupon];
            const nuovoTotale = totale - (totale * sconto);
            totaleScontoElement.innerText = `${nuovoTotale.toFixed(2)}€`;
            messaggioCoupon.innerText = `Coupon applicato! Hai ricevuto uno sconto del ${sconto * 100}%`;
            messaggioCoupon.style.color = "green";
        } else {
            messaggioCoupon.innerText = "Coupon non valido.";
            messaggioCoupon.style.color = "red";
        }
    });

    document.getElementById("checkout-form").addEventListener("submit", function (e) {
        e.preventDefault();

        const nome = document.getElementById("nome").value;
        const indirizzo = document.getElementById("indirizzo").value;
        const email = document.getElementById("email").value;
        const metodoPagamento = document.getElementById("metodo-pagamento").value;

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

        console.log("Ordine inviato:", ordine);

        localStorage.removeItem("carrelloAcquisto");
        alert("Acquisto completato! Grazie per il tuo ordine.");
        window.location.href = "conferma.html";
    });
});

// Layout aggiornato per separare i dati utente e il carrello
const container = document.getElementById("checkout-container");
container.classList.add("d-flex", "justify-content-between", "align-items-start");

document.getElementById("checkout-form").classList.add("w-50", "p-3", "border", "rounded");
document.getElementById("carrello-container").classList.add("w-50", "p-3", "border", "rounded", "bg-light");
