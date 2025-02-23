
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
// Funzione per caricare i prodotti nel catalogo
document.addEventListener("DOMContentLoaded", function () {
    fetch("../data/preassemblati.json")
        .then(response => response.json())
        .then(data => {
            const catalogo = document.getElementById("catalogo");

            // Oggetto per raggruppare i prodotti per categoria
            const categorie = {};

            data.forEach(prodotto => {
                if (!categorie[prodotto.categoria]) {
                    categorie[prodotto.categoria] = [];
                }
                categorie[prodotto.categoria].push(prodotto);
            });

            // Genera dinamicamente le sezioni per ogni categoria
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
                                <h6 class="text-primary">${prodotto.prezzo}€</h6>
                                <!-- Link per visualizzare il prodotto -->
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
    // Ottieni l'ID prodotto dall'URL
    const urlParams = new URLSearchParams(window.location.search);
    const prodottoId = urlParams.get('id');

    // Caricamento dei dati del prodotto
    fetch('../data/contenuti_pc.json')
        .then(response => response.json())
        .then(data => {
            const prodotto = data.prodotti.find(item => item.id === prodottoId);

            if (!prodotto) {
                console.error('Errore: Prodotto non trovato.');
                return;
            }

            // Popolare il contenuto della pagina solo se il prodotto esiste
            document.getElementById('prodotto-nome').textContent = prodotto.nome;
            document.getElementById('nome-prodotto').textContent = prodotto.nome;
            document.getElementById('descrizione-prodotto').textContent = prodotto.descrizione;
            document.getElementById('prezzo-prodotto').textContent = prodotto.prezzo;
            document.getElementById('img-prodotto').src = prodotto.immagini[0];
            document.getElementById('img-thumb1').src = prodotto.immagini[0];
            document.getElementById('img-thumb2').src = prodotto.immagini[1] || prodotto.immagini[0];

            // Specifiche del prodotto
            const specTable = document.getElementById('specifiche-prodotto');
            for (let key in prodotto.specifiche) {
                const row = document.createElement('tr');
                row.innerHTML = `<td><strong>${key}</strong></td><td>${prodotto.specifiche[key]}</td>`;
                specTable.appendChild(row);
            }

            // Gestione selezione immagini per la gallery
            document.querySelectorAll('.img-thumbnail').forEach((thumb, index) => {
                thumb.addEventListener('click', function() {
                    document.getElementById('img-prodotto').src = prodotto.immagini[index];
                });
            });
        })
        .catch(error => {
            console.error("Errore nel caricamento dei dati:", error);
        });
});



