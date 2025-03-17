//===================CARRELLO_NAVBAR====================
// Funzione per aggiornare il contatore del carrello nella navbar
function aggiornaContatoreCarrello() {
    const carrello = JSON.parse(localStorage.getItem("carrello")) || [];
    const numeroProdotti = carrello.length;
    const counterElement = document.getElementById("carrello-counter");
    console.log("Numero prodotti carrello:", numeroProdotti); // Debug: mostra il numero di prodotti

    if (counterElement) {
        counterElement.textContent = numeroProdotti > 0 ? numeroProdotti : ""; // Mostra il numero o nulla se il carrello è vuoto
    }
}



//====================FOOTER======================
document.addEventListener("DOMContentLoaded", function () {
    fetch("../data/footer.json")
        .then(response => response.json())
        .then(data => {
            // Social media
            let socialHtml = "";
            data.social.forEach(social => {
                socialHtml += `<a href="${social.link}" class="text-light me-3"><i class="${social.icon} fa-lg"></i></a>`;
            });
            document.getElementById("footer-social").innerHTML = socialHtml;

            // Email
            document.getElementById("footer-email").textContent = data.email;
            document.getElementById("footer-email").href = `mailto:${data.email}`;

            // Copyright
            document.getElementById("footer-copyright").innerHTML = data.copyright;
        })
        .catch(error => console.error("Errore nel caricamento del footer:", error));
});

//================ INDEX ==================

document.addEventListener("DOMContentLoaded", function () {
    fetch("../data/home.json")
        .then(response => response.json())
        .then(data => {
            // Carica l'hero section
            document.getElementById("hero-title").textContent = data.hero.titolo;
            document.getElementById("hero-desc").textContent = data.hero.descrizione;
            let heroBtn = document.getElementById("hero-btn");
            heroBtn.textContent = data.hero.bottone.testo;
            heroBtn.href = data.hero.bottone.link;

            // Carica i prodotti più acquistati
            caricaProdottiPopolari(data.prodotti_piu_acquistati);

            // Carica le offerte speciali
            caricaOfferteSpeciali(data.offerte_speciali);
        })
        .catch(error => console.error("Errore nel caricamento del JSON:", error));

    function caricaProdottiPopolari(prodotti) {
        let container = document.getElementById("prodotti-popolari");
        container.innerHTML = "";

        prodotti.forEach(prodotto => {
            let col = document.createElement("div");
            col.className = "col-md-4";

            col.innerHTML = `
                <div class="card">
                    <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                    <div class="card-body">
                        <h5 class="card-title">${prodotto.nome}</h5>
                        <p class="card-text"><strong>Categoria:</strong> ${prodotto.categoria}</p>
                        <p class="card-text"><strong>Prezzo:</strong> €${prodotto.prezzo.toFixed(2)}</p>
                        <a href="pagine/catalogo.html?categoria=${prodotto.categoria}" class="btn btn-primary">Vedi</a>
                    </div>
                </div>
            `;

            container.appendChild(col);
        });
    }

    function caricaOfferteSpeciali(offerte) {
        let container = document.getElementById("offerte-speciali");
        let titoloOfferte = document.getElementById("titolo-offerte");
    
        titoloOfferte.textContent = offerte.titolo;
        container.innerHTML = "";
    
        offerte.bundle.forEach((bundle, index) => {
            let prodottiHTML = bundle.prodotti.map(p => `<li>${p.nome} (${p.categoria}) - €0.00</li>`).join("");
    
            let card = document.createElement("div");
            card.classList.add("col-md-6", "mb-4");
    
            card.innerHTML = `
                <div class="card shadow-lg border-0">
                    <img src="${bundle.immagine}" class="card-img-top" alt="${bundle.nome}">
                    <div class="card-body text-center">
                        <h5 class="card-title">${bundle.nome}</h5>
                        <ul class="list-unstyled">${prodottiHTML}</ul>
                        <p class="text-danger fw-bold fs-4">€${bundle.prezzo_scontato.toFixed(2)}
                            <span class="text-muted text-decoration-line-through fs-6">€${bundle.prezzo_originale.toFixed(2)}</span>
                        </p>
                        <p class="text-warning fs-6">${offerte.timer_testo} <span class="timer" data-scadenza="${bundle.scadenza}"></span></p>
                        <button class="btn btn-primary aggiungi-bundle" data-index="${index}">${offerte.bottone}</button>
                    </div>
                </div>
            `;
    
            container.appendChild(card);
        });
    
        // Aggiungi event listener ai pulsanti per evitare problemi con JSON
        document.querySelectorAll(".aggiungi-bundle").forEach(button => {
            button.addEventListener("click", function () {
                let bundleIndex = this.getAttribute("data-index");
                let selectedBundle = offerte.bundle[bundleIndex];
                aggiungiAlCarrello(selectedBundle);
            });
        });
    
        avviaTimer();
    }
    
    function aggiungiAlCarrello(bundle) {
        let carrelloBundle = JSON.parse(localStorage.getItem("carrelloBundle")) || { prodotti: [] };
    
        let bundleCarrello = {
            nome: bundle.nome,
            prodotti: bundle.prodotti.map(p => ({ nome: p.nome, categoria: p.categoria, prezzo: 0 })), // Prezzo 0 per i singoli prodotti
            prezzo_totale: bundle.prezzo_scontato
        };
    
        carrelloBundle.prodotti.push(bundleCarrello);
        localStorage.setItem("carrelloBundle", JSON.stringify(carrelloBundle));
    
        window.location.href = "../pagine/carrello.html";
    }
    
    
    function avviaTimer() {
        let timerElements = document.querySelectorAll(".timer");
        let timerInterval;
    
        function aggiornaTimer() {
            let now = new Date().getTime();
    
            timerElements.forEach(element => {
                let scadenza = new Date(element.getAttribute("data-scadenza")).getTime();
                let diff = scadenza - now;
    
                if (diff > 0) {
                    let giorni = Math.floor(diff / (1000 * 60 * 60 * 24));
                    let ore = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minuti = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    let secondi = Math.floor((diff % (1000 * 60)) / 1000);
                    element.textContent = `${giorni}g ${ore}h ${minuti}m ${secondi}s`;
                } else {
                    element.textContent = "Offerta scaduta";
                }
            });
        }
    
        if (window.timerInterval) {
            clearInterval(window.timerInterval);
        }
        
        aggiornaTimer();
        window.timerInterval = setInterval(aggiornaTimer, 1000);
    }
    
    function aggiungiAlCarrello(bundle) {
        let carrello = JSON.parse(localStorage.getItem("carrello")) || [];
        let bundleCarrello = {
            nome: bundle.nome,
            prodotti: bundle.prodotti.map(p => ({ nome: p.nome, categoria: p.categoria, prezzo: 0 })),
            prezzo_totale: bundle.prezzo_scontato
        };
        
        carrello.push(bundleCarrello);
        localStorage.setItem("carrello", JSON.stringify(carrello));
        
        window.location.href = "../pagine/carrello.html";
    }
    
});









