//====================CATALOGO=====================
document.addEventListener("DOMContentLoaded", function () {
    let prodottiOriginali = []; // Ora è globale

    fetch("../api/catalogo.php")
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
                            <img src="${prodotto.immagine_principale}" class="card-img-top" alt="${prodotto.nome}">
                            <div class="card-body">
                                <h5 class="card-title">${prodotto.nome}</h5>
                                <p class="card-text">${prodotto.categoria} - ${prodotto.marca}</p>
                                <p class="card-text fw-bold">${prodotto.prezzo_base}€</p>
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
            let matchPrezzo = isNaN(filtroPrezzo) || prodotto.prezzo_base <= filtroPrezzo;
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

    fetch(`../api/prodotto.php?id=${prodottoId}`)
        .then(response => response.json())
        .then(prodotto => {
            if (!prodotto || prodotto.error) {
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

            // Carica tutte le specifiche dettagliate
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

            // Gestione delle varianti per PSU (750W, 850W, 1000W)
            let variantiDisponibili = "";
            let prezzoBase = prodotto.prezzo_base;
            if (prodotto.categoria === "PSU" && prodotto.varianti && prodotto.varianti.potenza) {
                variantiDisponibili = ` 
                    <div class="mt-3">
                        <h5>Seleziona la potenza:</h5>
                        ${Object.keys(prodotto.varianti.potenza).map(potenza => ` 
                            <button class="btn btn-outline-primary potenza-btn" data-potenza="${potenza}">${potenza}</button>
                        `).join('')}
                    </div>
                `;
            }

            // Gestione delle varianti per SSD (capacità)
            let variantiSSD = "";
            if (prodotto.categoria === "Storage" && prodotto.varianti && prodotto.varianti.capacita) {
                variantiSSD = ` 
                    <div class="mt-3">
                        <h5>Seleziona la capacità:</h5>
                        ${Object.keys(prodotto.varianti.capacita).map(capacita => ` 
                            <button class="btn btn-outline-primary capacita-btn" data-capacita="${capacita}">${capacita}</button>
                        `).join('')}
                    </div>
                `;
            }

            // Gestione delle varianti per RAM (colore e taglia)
            let variantiRAM = "";
            if (prodotto.categoria === "RAM" && prodotto.varianti) {
                if (prodotto.varianti.colore) {
                    variantiRAM += ` 
                        <div class="mt-3">
                            <h5>Seleziona il colore:</h5>
                            ${Object.keys(prodotto.varianti.colore).map(colore => ` 
                                <button class="btn btn-outline-primary colore-btn" data-colore="${colore}">${colore}</button>
                            `).join('')}
                        </div>
                    `;
                }
                
                if (prodotto.varianti.taglia) {
                    variantiRAM += ` 
                        <div class="mt-3">
                            <h5>Seleziona la taglia:</h5>
                            ${Object.keys(prodotto.varianti.taglia).map(taglia => ` 
                                <button class="btn btn-outline-primary taglia-btn" data-taglia="${taglia}" data-prezzo="${prodotto.varianti.taglia[taglia].prezzo}">${taglia}</button>
                            `).join('')}
                        </div>
                    `;
                }
            }

            // Composizione finale dell'HTML con tutte le varianti
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
                    <h4 class="text-primary" id="prezzo">${prodotto.prezzo_base}€</h4>
                    <button class="btn btn-success w-100">Aggiungi al Carrello</button>
                    ${variantiDisponibili}
                    ${variantiRAM}
                    ${variantiSSD}
                </div>
                <div class="col-md-12 mt-4 specifiche-dettagliate">
                    <h3>Specifiche Dettagliate</h3>
                    <ul class="specifiche-lista">
                        ${specificheDettagliateHTML}
                    </ul>
                    <ul class="specifiche-lista">
                        ${specificheHTML}
                    </ul>
                </div>
            `;

            // Variabile per tracciare il prezzo corrente
            let prezzoCorrente = prodotto.prezzo_base;

            // Gestione del cambio di potenza (PSU)
            document.querySelectorAll(".potenza-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".potenza-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    const potenzaSelezionata = this.getAttribute("data-potenza");
                    // Aggiorna il prezzo
                    prezzoCorrente = prodotto.prezzo_base + (prodotto.varianti.potenza[potenzaSelezionata]?.prezzo_aggiuntivo || 0);
                    document.getElementById("prezzo").textContent = prezzoCorrente + "€";
                });
            });

            // Gestione del cambio di colore (RAM)
            document.querySelectorAll(".colore-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".colore-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    document.getElementById("prezzo").textContent = prezzoCorrente + "€";
                });
            });

            // Gestione del cambio di taglia (RAM)
            document.querySelectorAll(".taglia-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".taglia-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    const tagliaSelezionata = this.getAttribute("data-taglia");
                    prezzoCorrente = parseFloat(this.getAttribute("data-prezzo"));
                    document.getElementById("prezzo").textContent = prezzoCorrente + "€";
                });
            });

            // Gestione del cambio di capacità (SSD)
            document.querySelectorAll(".capacita-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".capacita-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    const capacitaSelezionata = this.getAttribute("data-capacita");
                    prezzoCorrente = prodotto.prezzo_base + (prodotto.varianti.capacita[capacitaSelezionata]?.prezzo_aggiuntivo || 0);
                    document.getElementById("prezzo").textContent = prezzoCorrente + "€";
                });
            });

            // Gestione dell'aggiunta al carrello per i prodotti del catalogo
            document.querySelector(".btn-success").addEventListener("click", function () {
                const variantiSelezionate = {};
                
                // Raccogli le varianti selezionate
                const potenzaSelezionata = document.querySelector(".potenza-btn.active");
                if (potenzaSelezionata) {
                    variantiSelezionate.potenza = potenzaSelezionata.getAttribute("data-potenza");
                }
                
                const coloreSelezionato = document.querySelector(".colore-btn.active");
                if (coloreSelezionato) {
                    variantiSelezionate.colore = coloreSelezionato.getAttribute("data-colore");
                }
                
                const tagliaSelezionata = document.querySelector(".taglia-btn.active");
                if (tagliaSelezionata) {
                    variantiSelezionate.taglia = tagliaSelezionata.getAttribute("data-taglia");
                }
                
                const capacitaSelezionata = document.querySelector(".capacita-btn.active");
                if (capacitaSelezionata) {
                    variantiSelezionate.capacita = capacitaSelezionata.getAttribute("data-capacita");
                }
                
                // Crea i dati da inviare
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('product_id', prodotto.id);
                formData.append('price', prezzoCorrente);
                formData.append('quantity', 1);
                formData.append('variants', JSON.stringify(variantiSelezionate));
                formData.append('type', 'catalogo');
                
                // Invia la richiesta al server
                fetch('../api/carrello.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Prodotto aggiunto al carrello!");
                        // Aggiorna il contatore del carrello
                        aggiornaContatoreCarrello();
                    } else {
                        alert("Errore nell'aggiunta del prodotto al carrello.");
                    }
                })
                .catch(error => {
                    console.error("Errore:", error);
                    alert("Si è verificato un errore durante l'aggiunta al carrello.");
                });
            });

            // Gestione delle miniature
            document.querySelectorAll(".miniatura").forEach(miniatura => {
                miniatura.addEventListener("click", function() {
                    const index = parseInt(this.getAttribute("data-index"));
                    document.querySelectorAll(".carousel-item").forEach((item, i) => {
                        item.classList.toggle("active", i === index);
                    });
                    document.querySelectorAll(".miniatura").forEach(m => {
                        m.classList.remove("active");
                    });
                    this.classList.add("active");
                });
            });
        })
        .catch(err => {
            console.error("Errore nel caricamento dei dati:", err);
            document.getElementById("dettaglio-prodotto").innerHTML = "<p>Errore nel caricamento del prodotto.</p>";
        });
});