//===================CARRELLO_NAVBAR====================
// Funzione per aggiornare il contatore del carrello nella navbar
function aggiornaContatoreCarrello() {
    const carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
    const carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
    const carrelloBundle = JSON.parse(localStorage.getItem("carrelloBundle")) || { prodotti: [] };
    
    // Conta il numero totale di prodotti in tutti i carrelli
    const numeroProdotti = carrelloPreassemblato.prodotti.length + carrelloCatalogo.prodotti.length + carrelloBundle.prodotti.length;
    
    const counterElement = document.getElementById("carrello-counter");
    
    if (counterElement) {
        if (numeroProdotti > 0) {
            counterElement.textContent = numeroProdotti;
            counterElement.style.display = "inline-block";
        } else {
            counterElement.textContent = "";
            counterElement.style.display = "none";
        }
    }
}

// Esegui la funzione quando il documento è caricato
document.addEventListener("DOMContentLoaded", function() {
    aggiornaContatoreCarrello();
});

//====================FOOTER======================
document.addEventListener("DOMContentLoaded", function () {
    // Determina il percorso corretto per il file JSON in base alla pagina corrente
    const isHomePage = window.location.pathname.endsWith('index.html') || window.location.pathname.endsWith('/');
    const dataPath = isHomePage ? "data/footer.json" : "../data/footer.json";
    
    fetch(dataPath)
        .then(response => response.json())
        .then(data => {
            // Social media
            let socialHtml = "";
            data.social.forEach(social => {
                socialHtml += `<a href="${social.link}" class="text-light me-3"><i class="${social.icon} fa-lg"></i></a>`;
            });
            document.getElementById("footer-social").innerHTML = socialHtml;

            // Email
            const emailElements = document.querySelectorAll("#footer-email");
            emailElements.forEach(element => {
                element.textContent = data.email;
                element.href = `mailto:${data.email}`;
            });

            // Copyright
            document.getElementById("footer-copyright").innerHTML = data.copyright;
        })
        .catch(error => console.error("Errore nel caricamento del footer:", error));
});

//================ INDEX ==================

document.addEventListener("DOMContentLoaded", function () {
    // Determina il percorso corretto per il file JSON in base alla pagina corrente
    const isHomePage = window.location.pathname.endsWith('index.html') || window.location.pathname.endsWith('/');
    const dataPath = isHomePage ? "data/home.json" : "../data/home.json";
    
    fetch(dataPath)
        .then(response => response.json())
        .then(data => {
            // Carica l'hero section
            const heroTitle = document.getElementById("hero-title");
            const heroDesc = document.getElementById("hero-desc");
            const heroBtn = document.getElementById("hero-btn");
            
            if (heroTitle && heroDesc && heroBtn) {
                heroTitle.textContent = data.hero.titolo;
                heroDesc.textContent = data.hero.descrizione;
                heroBtn.textContent = data.hero.bottone.testo;
                heroBtn.href = data.hero.bottone.link;
            }

            // Carica i prodotti più acquistati
            caricaProdottiPopolari(data.prodotti_piu_acquistati);

            // Carica le offerte speciali
            caricaOfferteSpeciali(data.offerte_speciali);
        })
        .catch(error => console.error("Errore nel caricamento del JSON:", error));

    function caricaProdottiPopolari(prodotti) {
        let container = document.getElementById("prodotti-popolari");
        if (!container) return;
        
        container.innerHTML = "";

        prodotti.forEach(prodotto => {
            let col = document.createElement("div");
            col.className = "col-md-4 mb-4";

            col.innerHTML = `
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-image-container">
                        <img src="${prodotto.immagine}" class="card-img-top product-image" alt="${prodotto.nome}">
                        <div class="product-overlay">
                            <a href="${isHomePage ? 'pagine/catalogo.html' : 'catalogo.html'}?categoria=${prodotto.categoria}" class="btn btn-primary">Vedi dettagli</a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${prodotto.nome}</h5>
                        <p class="card-text text-muted"><i class="fas fa-tag me-2"></i>${prodotto.categoria}</p>
                        <div class="mt-auto">
                            <p class="card-text fw-bold text-primary fs-5">${prodotto.prezzo.toFixed(2)}€</p>
                            <a href="${isHomePage ? 'pagine/catalogo.html' : 'catalogo.html'}?categoria=${prodotto.categoria}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>Vedi
                            </a>
                        </div>
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
            let prodottiHTML = bundle.prodotti.map(p => 
                `<li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>${p.nome} (${p.categoria})</li>`
            ).join("");
    
            let card = document.createElement("div");
            card.classList.add("col-md-6", "mb-4");
    
            card.innerHTML = `
                <div class="card shadow bundle-card h-100">
                    <div class="ribbon-wrapper">
                        <div class="ribbon">OFFERTA</div>
                    </div>
                    <img src="${bundle.immagine}" class="card-img-top bundle-image" alt="${bundle.nome}">
                    <div class="card-body text-center">
                        <h5 class="card-title">${bundle.nome}</h5>
                        <ul class="list-unstyled text-start my-3">${prodottiHTML}</ul>
                        <div class="price-container mb-3">
                            <p class="text-danger fw-bold fs-4 mb-0">${bundle.prezzo_scontato.toFixed(2)}€
                                <span class="text-muted text-decoration-line-through fs-6">${bundle.prezzo_originale.toFixed(2)}€</span>
                            </p>
                            <p class="text-success fs-6">Risparmi: ${(bundle.prezzo_originale - bundle.prezzo_scontato).toFixed(2)}€</p>
                        </div>
                        <div class="timer-container mb-3">
                            <p class="text-warning fs-6 mb-1">
                                <i class="fas fa-clock me-2"></i>${offerte.timer_testo}
                            </p>
                            <div class="timer-display bg-dark text-white p-2 rounded">
                                <span class="timer" data-scadenza="${bundle.scadenza}">00:00:00</span>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 aggiungi-bundle" data-index="${index}">
                            <i class="fas fa-cart-plus me-2"></i>${offerte.bottone}
                        </button>
                    </div>
                </div>
            `;
    
            container.appendChild(card);
        });
    
        // Aggiungi event listener ai pulsanti
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
            immagine: bundle.immagine,
            prodotti: bundle.prodotti.map(p => ({ nome: p.nome, categoria: p.categoria })),
            prezzo_totale: bundle.prezzo_scontato
        };
    
        carrelloBundle.prodotti.push(bundleCarrello);
        localStorage.setItem("carrelloBundle", JSON.stringify(carrelloBundle));
        
        // Aggiorna il contatore del carrello
        aggiornaContatoreCarrello();
        
        // Mostra una notifica di conferma
        mostraNotifica("Bundle aggiunto al carrello!", "success");
        
        // Opzionale: reindirizza al carrello
        setTimeout(() => {
            window.location.href = isHomePage ? "pagine/carrello.html" : "carrello.html";
        }, 1000);
    }
    
    function mostraNotifica(messaggio, tipo = "info") {
        // Crea l'elemento di notifica se non esiste
        let notifica = document.getElementById("notifica");
        if (!notifica) {
            notifica = document.createElement("div");
            notifica.id = "notifica";
            notifica.className = "notifica";
            document.body.appendChild(notifica);
        }
        
        // Imposta il tipo e il messaggio
        notifica.className = `notifica notifica-${tipo}`;
        notifica.innerHTML = `
            <div class="notifica-content">
                <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                <span>${messaggio}</span>
            </div>
        `;
        
        // Mostra la notifica
        notifica.classList.add("show");
        
        // Nascondi dopo 3 secondi
        setTimeout(() => {
            notifica.classList.remove("show");
        }, 3000);
    }
    
    function avviaTimer() {
        let timerElements = document.querySelectorAll(".timer");
        
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
                    
                    // Formatta i numeri per mostrare sempre due cifre
                    ore = ore < 10 ? "0" + ore : ore;
                    minuti = minuti < 10 ? "0" + minuti : minuti;
                    secondi = secondi < 10 ? "0" + secondi : secondi;
                    
                    if (giorni > 0) {
                        element.innerHTML = `
                            <span class="timer-unit">${giorni}<small>g</small></span>
                            <span class="timer-unit">${ore}<small>h</small></span>
                            <span class="timer-unit">${minuti}<small>m</small></span>
                            <span class="timer-unit">${secondi}<small>s</small></span>
                        `;
                    } else {
                        element.innerHTML = `
                            <span class="timer-unit">${ore}<small>h</small></span>
                            <span class="timer-unit">${minuti}<small>m</small></span>
                            <span class="timer-unit">${secondi}<small>s</small></span>
                        `;
                    }
                } else {
                    element.innerHTML = `<span class="text-danger">Offerta scaduta</span>`;
                    element.closest(".bundle-card").classList.add("expired");
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