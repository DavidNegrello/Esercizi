//===================CARRELLO_NAVBAR====================
// Funzione per aggiornare il contatore del carrello nella navbar
function aggiornaContatoreCarrello() {
    fetch("../api/carrello.php?action=count")
        .then(response => response.json())
        .then(data => {
            const counterElement = document.getElementById("carrello-counter");
            if (counterElement) {
                counterElement.textContent = data.count > 0 ? data.count : "";
            }
        })
        .catch(error => console.error("Errore nell'aggiornamento del contatore del carrello:", error));
}

// Aggiorna il contatore del carrello quando la pagina viene caricata
document.addEventListener("DOMContentLoaded", function() {
    aggiornaContatoreCarrello();
});

//====================FOOTER======================document.addEventListener("DOMContentLoaded", function () {
    fetch("../api/footer.php")
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
    fetch("../api/home.php")
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
        .catch(error => console.error("Errore nel caricamento dei dati:", error));

    function caricaProdottiPopolari(prodotti) {
        let container = document.getElementById("prodotti-popolari");
        if (!container) return;
        
        container.innerHTML = "";

        prodotti.forEach(prodotto => {
            let col = document.createElement("div");
            col.className = "col-md-4";

            col.innerHTML = `
                <div class="card">
                    <img src="${prodotto.immagine_principale}" class="card-img-top" alt="${prodotto.nome}">
                    <div class="card-body">
                        <h5 class="card-title">${prodotto.nome}</h5>
                        <p class="card-text"><strong>Categoria:</strong> ${prodotto.categoria}</p>
                        <p class="card-text"><strong>Prezzo:</strong> €${prodotto.prezzo_base.toFixed(2)}</p>
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
        
        if (!container || !titoloOfferte) return;
    
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
                        <p class="text-warning fs-6">${offerte.timer_testo} <span class="timer" data-scadenza="${bundle.data_scadenza}"></span></p>
                        <button class="btn btn-primary aggiungi-bundle" data-id="${bundle.id}">${offerte.bottone}</button>
                    </div>
                </div>
            `;
    
            container.appendChild(card);
        });
    
        // Aggiungi event listener ai pulsanti
        document.querySelectorAll(".aggiungi-bundle").forEach(button => {
            button.addEventListener("click", function () {
                const bundleId = this.getAttribute("data-id");
                aggiungiBundle(bundleId);
            });
        });
    
        avviaTimer();
    }
    
    function aggiungiBundle(bundleId) {
        // Crea i dati da inviare
        const formData = new FormData();
        formData.append('action', 'add_bundle');
        formData.append('bundle_id', bundleId);
        
        // Invia la richiesta al server
        fetch('../api/carrello.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Bundle aggiunto al carrello!");
                // Aggiorna il contatore del carrello
                aggiornaContatoreCarrello();
                // Reindirizza al carrello
                window.location.href = "../pagine/carrello.html";
            } else {
                alert("Errore nell'aggiunta del bundle al carrello.");
            }
        })
        .catch(error => {
            console.error("Errore:", error);
            alert("Si è verificato un errore durante l'aggiunta al carrello.");
        });
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
});