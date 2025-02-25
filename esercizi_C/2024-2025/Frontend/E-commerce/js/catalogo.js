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
                                <a href="dettaglio_catalogo.html?id=${prodotto.id}" class="btn btn-primary">Visualizza</a>
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



//=========================Dettaglio_catalogo.html=======================
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const prodottoId = params.get("id");

    if (!prodottoId) {
        document.getElementById("dettaglio-prodotto").innerHTML = "<p>Prodotto non trovato.</p>";
        return;
    }

    fetch("../data/contenuti_catalogo.json")
        .then(response => response.json())
        .then(data => {
            const prodotto = data.prodotti.find(p => p.id === parseInt(prodottoId));

            if (!prodotto) {
                document.getElementById("dettaglio-prodotto").innerHTML = "<p>Prodotto non trovato.</p>";
                return;
            }

            // Carica le specifiche base
            let specificheHTML = "";
            for (const [chiave, valore] of Object.entries(prodotto.specifiche || {})) {
                specificheHTML += `
                    <li class="specifica-item">
                        <span class="specifica-titolo">${chiave}:</span> 
                        <span class="specifica-valore">${valore}</span>
                    </li>
                `;
            }

            // Carica tutte le specifiche dettagliate (nuove specifiche)
            let specificheDettagliateHTML = "";
            for (const [chiave, valore] of Object.entries(prodotto.specifiche_dettagliate || {})) {
                specificheDettagliateHTML += `
                    <li class="specifica-item">
                        <span class="specifica-titolo">${chiave}:</span> 
                        <span class="specifica-valore">${valore || "Non disponibile"}</span>
                    </li>
                `;
            }

            // Carica le miniature delle immagini
            let miniatureHTML = prodotto.immagini.map((img, index) => `
                <img src="${img}" class="miniatura ${index === 0 ? 'active' : ''}" data-index="${index}" alt="Miniatura ${index + 1}" style="width: 50px; cursor: pointer; margin-right: 5px;">
            `).join('');

            // Carica i colori disponibili
            let coloriDisponibili = prodotto.colori ? prodotto.colori.map(colore => `
                <button class="btn btn-outline-dark colore-btn" data-colore="${colore}">${colore}</button>
            `).join('') : "<p>Nessuna opzione colore disponibile.</p>";

            // Composizione finale dell'HTML
            document.getElementById("dettaglio-prodotto").innerHTML = `
                <div class="col-md-6">
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            ${prodotto.immagini.map((img, index) => `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <img src="${img}" class="d-block w-100" alt="${prodotto.nome}" style="object-fit: contain;">
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
                </div>
                <div class="col-md-12 mt-4 specifiche-dettagliate">
                    <h3>Specifiche Dettagliate</h3>
                    <ul class="specifiche-lista">
                        ${specificheDettagliateHTML}
                    </ul>
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
                            <img src="${img}" class="d-block w-100" alt="${prodotto.nome}" style="object-fit: contain;">
                        </div>
                    `).join('');
                    document.querySelector(".mt-2").innerHTML = immaginiColore.map((img, index) => `
                        <img src="${img}" class="miniatura ${index === 0 ? 'active' : ''}" data-index="${index}" alt="Miniatura ${index + 1}" style="width: 50px; cursor: pointer; margin-right: 5px;">
                    `).join('');
                });
            });
        })
        .catch(error => console.error("Errore nel caricamento del prodotto:", error));
});



