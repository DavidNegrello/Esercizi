
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
                        <a href="prodotti.html?categoria=${prodotto.categoria}" class="btn btn-primary">Vedi</a>
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

        offerte.bundle.forEach(bundle => {
            let prodottiHTML = bundle.prodotti.map(p => `<li>${p.nome} (${p.categoria})</li>`).join("");

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
                        <a href="../pagine/catalogo.html" class="btn btn-primary">${offerte.bottone}</a>
                    </div>
                </div>
            `;

            container.appendChild(card);
        });

        avviaTimer();
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
                    element.textContent = `${giorni}g ${ore}h ${minuti}m ${secondi}s`;
                } else {
                    element.textContent = "Offerta scaduta";
                }
            });
        }

        aggiornaTimer();
        setInterval(aggiornaTimer, 1000);
    }
});



//====================CATALOGO=====================
document.addEventListener("DOMContentLoaded", function () {
    let prodottiOriginali = []; // Ora è globale

    fetch("../data/catalogo.json")
        .then(response => response.json())
        .then(data => {
            prodottiOriginali = data.prodotti; 
            console.log("Dati caricati:", prodottiOriginali);

            // Carica Sidebar
            let sidebarHtml = `<h5>${data.sidebar.titolo}</h5>`;

            // Barra di ricerca
            sidebarHtml += `<input type="text" id="search-bar" class="form-control mb-3" placeholder="${data.sidebar.filtri.ricerca}">`;

            // Categorie
            sidebarHtml += `<h6>Categorie</h6><ul id="category-list" class="list-unstyled">`;
            data.sidebar.filtri.categorie.forEach(cat => {
                sidebarHtml += `<li><input type="checkbox" class="category-filter" value="${cat}"> ${cat}</li>`;
            });
            sidebarHtml += `</ul>`;

            // Filtro Prezzo
            sidebarHtml += `
                <h6>Prezzo</h6>
                <input type="range" id="price-filter" class="form-range" min="${data.sidebar.filtri.prezzo.min}" max="${data.sidebar.filtri.prezzo.max}" step="${data.sidebar.filtri.prezzo.step}">
                <span id="price-label">${data.sidebar.filtri.prezzo.min} - ${data.sidebar.filtri.prezzo.max}€</span>
            `;

            // Marche
            sidebarHtml += `<h6>Marca</h6><ul id="brand-list" class="list-unstyled">`;
            data.sidebar.filtri.marche.forEach(marca => {
                sidebarHtml += `<li><input type="checkbox" class="brand-filter" value="${marca}"> ${marca}</li>`;
            });
            sidebarHtml += `</ul>`;

            document.getElementById("sidebar").innerHTML = sidebarHtml;

            // Carica prodotti iniziali
            caricaProdotti(prodottiOriginali);
            
            // Associa gli eventi SOLO dopo aver caricato i prodotti
            document.getElementById("search-bar").addEventListener("input", applicaFiltri);
            document.getElementById("price-filter").addEventListener("input", function () {
                document.getElementById("price-label").textContent = `Fino a ${this.value}€`;
                applicaFiltri();
            });

            document.querySelectorAll(".category-filter, .brand-filter").forEach(el => {
                el.addEventListener("change", applicaFiltri);
            });
        })
        .catch(error => console.error("Errore nel caricamento del catalogo:", error));

    function caricaProdotti(prodotti) {
        let prodottiHtml = "";
        prodotti.forEach(prodotto => {
            prodottiHtml += `
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                        <div class="card-body">
                            <h5 class="card-title">${prodotto.nome}</h5>
                            <p class="card-text">${prodotto.categoria} - ${prodotto.marca}</p>
                            <p class="card-text fw-bold">${prodotto.prezzo}€</p>
                            <a href="#" class="btn btn-primary">Visualizza</a>
                        </div>
                    </div>
                </div>
            `;
        });
        document.getElementById("catalogo-prodotti").innerHTML = prodottiHtml;
    }

    function applicaFiltri() {
        if (prodottiOriginali.length === 0) {
            console.log("⚠️ prodottiOriginali è vuoto, impossibile applicare filtri!");
            return;
        }

        let filtroTesto = document.getElementById("search-bar").value.toLowerCase();
        let filtroPrezzo = parseFloat(document.getElementById("price-filter").value);
        let categorieSelezionate = [...document.querySelectorAll(".category-filter:checked")].map(el => el.value);
        let marcheSelezionate = [...document.querySelectorAll(".brand-filter:checked")].map(el => el.value);

        console.log("Categorie selezionate:", categorieSelezionate);
        console.log("Marche selezionate:", marcheSelezionate);
        console.log("Prezzo massimo selezionato:", filtroPrezzo);

        let prodottiFiltrati = prodottiOriginali.filter(prodotto => {
            let matchTesto = prodotto.nome.toLowerCase().includes(filtroTesto);
            let matchPrezzo = isNaN(filtroPrezzo) || prodotto.prezzo <= filtroPrezzo;
            let matchCategoria = categorieSelezionate.length === 0 || categorieSelezionate.includes(prodotto.categoria);
            let matchMarca = marcheSelezionate.length === 0 || marcheSelezionate.includes(prodotto.marca);

            return matchTesto && matchPrezzo && matchCategoria && matchMarca;
        });

        console.log("Prodotti filtrati dopo il filtro:", prodottiFiltrati);

        caricaProdotti(prodottiFiltrati);
    }
});


//========================PREASSEMBLATI==================
document.addEventListener("DOMContentLoaded", function () {
    fetch("../data/preassemblati.json")
        .then(response => response.json())
        .then(data => {
            const catalogo = document.getElementById("catalogo");
            const categorie = {};

            data.forEach(prodotto => {
                if (!categorie[prodotto.categoria]) {
                    categorie[prodotto.categoria] = [];
                }
                categorie[prodotto.categoria].push(prodotto);
            });

            Object.keys(categorie).forEach(categoria => {
                const section = document.createElement("div");
                section.classList.add("mb-5");
                section.innerHTML = `
                    <h2 class="text-center">${categoria}</h2>
                    <div class="row" id="sezione-${categoria.replace(/\s+/g, '-')}"></div>
                `;
                catalogo.appendChild(section);

                const sezioneProdotti = document.getElementById(`sezione-${categoria.replace(/\s+/g, '-')}`);

                categorie[categoria].forEach(prodotto => {
                    const card = document.createElement("div");
                    card.classList.add("col-md-4", "mb-4");

                    card.innerHTML = `
                        <div class="card shadow-sm">
                            <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                            <div class="card-body">
                                <h5 class="card-title">${prodotto.nome}</h5>
                                <p class="card-text">${prodotto.descrizione}</p>
                                <h6 class="text-primary">${prodotto.prezzo} €</h6>
                                <a href="dettaglio.html?id=${prodotto.id}" class="btn btn-dark w-100">Visualizza</a>
                            </div>
                        </div>
                    `;

                    sezioneProdotti.appendChild(card);
                });
            });
        })
        .catch(error => console.error("Errore nel caricamento dei dati:", error));
});



//=========================Dettaglio.html=======================
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const prodottoId = params.get("id");

    if (!prodottoId) {
        document.getElementById("dettaglio-prodotto").innerHTML = "<p>Prodotto non trovato.</p>";
        return;
    }

    fetch("../data/contenuti_pc.json")
        .then(response => response.json())
        .then(data => {
            const prodotto = data.prodotti.find(p => p.id === prodottoId);

            if (!prodotto) {
                document.getElementById("dettaglio-prodotto").innerHTML = "<p>Prodotto non trovato.</p>";
                return;
            }

            // Carica le specifiche base
            let specificheHTML = "";
            for (const [chiave, valore] of Object.entries(prodotto.specifiche)) {
                specificheHTML += `<li><strong>${chiave}:</strong> ${valore}</li>`;
            }

            // Carica le specifiche dettagliate
            let specificheDettagliateHTML = "";
            if (prodotto.specifiche_dettagliate) {
                specificheDettagliateHTML = Object.entries(prodotto.specifiche_dettagliate).map(([chiave, valore]) => `
                    <div class="specifica-dettagliata">
                        <div class="specifica-titolo">${chiave}</div>
                        <div class="specifica-valore">${valore}</div>
                    </div>
                `).join('');
            }

            // Carica le miniature delle immagini
            let miniatureHTML = prodotto.immagini.map((img, index) => `
                <img src="${img}" class="miniatura ${index === 0 ? 'active' : ''}" data-index="${index}" alt="Miniatura ${index + 1}" style="width: 50px; cursor: pointer; margin-right: 5px;">
            `).join('');

            // Carica i colori disponibili
            let coloriDisponibili = prodotto.colori ? prodotto.colori.map(colore => `
                <button class="btn btn-outline-dark colore-btn" data-colore="${colore}">${colore}</button>
            `).join('') : "<p>Nessuna opzione colore disponibile.</p>";

            // Carica le informazioni aggiuntive
            let informazioniAggiuntiveHTML = prodotto.informazioniAggiuntive ? prodotto.informazioniAggiuntive.map(img => `
                <img src="${img}" class="img-fluid mb-2" alt="Informazione Aggiuntiva">
            `).join('') : "<p>Nessuna informazione aggiuntiva disponibile.</p>";

            // Carica le personalizzazioni
            let personalizzazioniHTML = prodotto.personalizzazioni ? prodotto.personalizzazioni.map(opzione => `
                <li>
                    ${opzione.nome} - +${opzione.prezzo}€
                    <button class="btn btn-outline-success btn-sm aggiungi-personalizzazione" data-prezzo="${opzione.prezzo}" data-nome="${opzione.nome}">+</button>
                </li>
            `).join('') : "<p>Nessuna opzione di personalizzazione disponibile.</p>";

            // Composizione finale dell'HTML
            document.getElementById("dettaglio-prodotto").innerHTML = `
                <div class="col-md-6">
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            ${prodotto.immagini.map((img, index) => `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <img src="${img}" class="d-block w-100" alt="${prodotto.nome}">
                                </div>
                            `).join('')}
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
                            <span class="visually-hidden">Precedente</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
                            <span class="visually-hidden">Successivo</span>
                        </button>
                    </div>
                    <div class="mt-2 d-flex">${miniatureHTML}</div>
                </div>
                <div class="col-md-6">
                    <h2>${prodotto.nome}</h2>
                    <p>${prodotto.descrizione}</p>
                    <h4 class="text-primary" id="prezzo">${prodotto.prezzo}</h4>
                    <button class="btn btn-success w-100">Aggiungi al Carrello</button>
                    <div class="mt-3">
                        <h5>Colori disponibili:</h5>
                        <div>${coloriDisponibili}</div>
                    </div>
                    <div class="mt-3">
                        <h5>Personalizzazione:</h5>
                        <ul>${personalizzazioniHTML}</ul>
                    </div>
                </div>
                <div class="col-md-12 mt-4">
                    <h3>Informazioni Aggiuntive</h3>
                    ${informazioniAggiuntiveHTML}
                </div>
                <div class="col-md-12 mt-4 specifiche-dettagliate">
                    <h3>Specifiche Dettagliate</h3>
                    ${specificheDettagliateHTML}
                </div>
            `;

            // Gestione delle miniature
            document.querySelectorAll(".miniatura").forEach(img => {
                img.addEventListener("click", function () {
                    let index = this.getAttribute("data-index");
                    document.querySelector(".carousel-inner .active").classList.remove("active");
                    document.querySelectorAll(".carousel-item")[index].classList.add("active");
                    document.querySelectorAll(".miniatura").forEach(mini => mini.classList.remove("active"));
                    this.classList.add("active");
                });
            });

            // Gestione del cambio di colore
            document.querySelectorAll(".colore-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    const coloreSelezionato = this.getAttribute("data-colore");
                    const immaginiColore = prodotto.varianti[coloreSelezionato] || prodotto.immagini;
                    document.querySelector(".carousel-inner").innerHTML = immaginiColore.map((img, index) => `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="${img}" class="d-block w-100" alt="${prodotto.nome}">
                        </div>
                    `).join('');
                    document.querySelector(".mt-2").innerHTML = immaginiColore.map((img, index) => `
                        <img src="${img}" class="miniatura ${index === 0 ? 'active' : ''}" data-index="${index}" alt="Miniatura ${index + 1}" style="width: 50px; cursor: pointer; margin-right: 5px;">
                    `).join('');
                });
            });

            // Gestione del tasto + per aggiungere una personalizzazione
            let prezzoBase = parseFloat(prodotto.prezzo.replace('€', '').replace(',', '.'));
            document.querySelectorAll(".aggiungi-personalizzazione").forEach(btn => {
                btn.addEventListener("click", function () {
                    const prezzoAggiuntivo = parseFloat(this.getAttribute("data-prezzo"));
                    const nomePersonalizzazione = this.getAttribute("data-nome");
                    prezzoBase += prezzoAggiuntivo;
                    document.getElementById("prezzo").innerText = `${prezzoBase.toFixed(2)}€`;
                    this.disabled = true; // Disabilita il tasto dopo che è stato aggiunto
                    this.innerText = `Aggiunto - ${nomePersonalizzazione}`;
                });
            });
        })
        .catch(error => console.error("Errore nel caricamento del prodotto:", error));
});
